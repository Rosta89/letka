<?php
$seriesID = $_GET['id'];
//$matchID = $_GET['match_id'];
$teams = Db::queryOne("SELECT s.*, th.NAME t_home, ta.NAME t_away, co.PLAYERS_COUNT
FROM series s 
JOIN teams th ON th.ID = s.HOME_TEAM 
JOIN teams ta ON ta.ID = s.AWAY_TEAM
JOIN competition_annuals ca ON ca.id = s.COMPETITION_ANNUAL_ID
JOIN competitions co ON co.id = ca.COMPETITION_ID
WHERE s.ID = ?", $seriesID);
$teamsID[0] = $teams['HOME_TEAM'];
$teamsID[1] = $teams['AWAY_TEAM'];
$teamsName[0] = $teams['t_home'];
$teamsName[1] = $teams['t_away'];
if (isset($_GET['match_id'])) {
    $playerStats = Db::queryAll("SELECT * FROM STATISTICS WHERE MATCH_ID = ? ORDER BY HOME DESC,TEAM_ORDER ASC", $_GET['match_id']);
    $matchScore = Db::queryOne("SELECT * FROM MATCHES WHERE ID = ?", $_GET['match_id']);
?>
<?php
}
?>
<div class="containerInput">
    <div class="text-center">
        <form action="index.php?page=matches_create" method="POST">
            <input type="hidden" name="series_id" value="<?php echo $seriesID ?>">
            <?php if (isset($_GET['match_id'])) { ?>
                <input type="hidden" name="match_id" value="<?php echo $_GET['match_id'] ?>">
            <?php
            }
            ?>
            <table class="content-table">
                <thead>
                    <tr>
                        <th class="col-md-4">Team1</th>
                        <th class="col-md-2">Skóre domácí</th>
                        <th class="col-md-2">Skóre hosté</th>
                        <th class="col-md-4">Team2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    echo ('<tr>');
                    echo '<td>' . $teams['t_home'] . '</td>';
                    ?>
                    <td scope="col">
                        <input type="number" name="homeGoals" id="homeGoals" value="<?php if (isset($_GET['match_id'])) {
                                                                                        echo $matchScore['HOME_SCORE'];
                                                                                    } else {
                                                                                        echo 0;
                                                                                    } ?>" min="0" max="99">
                    </td>
                    <td scope="col">
                        <input type="number" name="awayGoals" id="awayGoals" value="<?php if (isset($_GET['match_id'])) {
                                                                                        echo $matchScore['AWAY_SCORE'];
                                                                                    } else {
                                                                                        echo 0;
                                                                                    } ?>" min="0" max="99">
                    </td>
                    <?php
                    echo '<td>' . $teams['t_away'] . '</td>';
                    echo '<tr>';
                    ?>
                </tbody>
            </table>

            <?php for ($i = 0; $i < 2; $i++) {
                $teamPlayers = Db::queryAll("SELECT pl.* 
            FROM players pl
            JOIN players_2_teams plt on plt.player_id = pl.id
            WHERE plt.TEAM_ID = ?", $teamsID[$i]);
            ?>
                <table class="content-table">
                    <thead></thead>
                    <tr>
                        <th class="col-md-4"><?php echo ($teamsName[$i]); ?></th>
                        <th class="col-md-2">Góly</th>
                        <th class="col-md-2">Asistence</th>
                        <th class="col-md-2">Savy</th>
                        <th class="col-md-2">MVP</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($j = 0; $j < $teams['PLAYERS_COUNT']; $j++) {
                        ?>
                            <td>
                                <select name='players_id[]'>
                                    <?php
                                    foreach ($teamPlayers as $row) {
                                    ?>
                                        <option <?php if (isset($_GET['match_id'])) {
                                                    if ($row['ID'] == $playerStats[$j + $i * $teams['PLAYERS_COUNT']]['PLAYER_ID']) {
                                                        echo 'selected';
                                                    }
                                                } elseif ($row['ID'] == $teamPlayers[$j]['ID']) {
                                                    echo 'selected';
                                                } ?> value="<?php echo $row['ID']; ?>"><?php echo $row['NAME']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <input type="hidden" name="team_order[]" value="<?php echo $j; ?>">
                            <input type="hidden" name="home_team[]" value="<?php echo 1 - $i; ?>">
                            <td scope="col">
                                <input type="number" name="goals[]" id="goals" value="<?php if (isset($_GET['match_id'])) {
                                                                                            echo ($playerStats[$j + $i * $teams['PLAYERS_COUNT']]['GOALS']);
                                                                                        } else {
                                                                                            echo 0;
                                                                                        }  ?>" min="0" max="99">
                            </td>
                            <td scope=" col">
                                <input type="number" name="assists[]" id="assists" value="<?php if (isset($_GET['match_id'])) {
                                                                                                echo ($playerStats[$j + $i * $teams['PLAYERS_COUNT']]['ASSISTS']);
                                                                                            } else {
                                                                                                echo 0;
                                                                                            }  ?>" min="0" max="99">
                            </td>
                            <td scope=" col">
                                <input type="number" name="saves[]" id="saves" value="<?php if (isset($_GET['match_id'])) {
                                                                                            echo ($playerStats[$j + $i * $teams['PLAYERS_COUNT']]['SAVES']);
                                                                                        } else {
                                                                                            echo 0;
                                                                                        }  ?>" min="0" max="99">
                            </td>
                            <td scope=" col">
                                <input type="number" name="mvp[]" id="mvp" value="<?php if (isset($_GET['match_id'])) {
                                                                                        echo ($playerStats[$j + $i * $teams['PLAYERS_COUNT']]['MVPS']);
                                                                                    } else {
                                                                                        echo 0;
                                                                                    }  ?>" min="0" max="99">
                            </td>

                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php } ?>
            <div class=" form-group mt-2">
                <input type="submit" class="btn btn-primary" value="uložit">
            </div>
        </form>
    </div>