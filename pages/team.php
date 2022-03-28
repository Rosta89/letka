<?php
$teamID = $_GET['id'];
if (Db::querySingle("SELECT COUNT(*) from TEAMS WHERE ID = ?", $teamID)) {
    if (isset($_GET['edit'])) {
        //ověřit zda má na to právo
        $playersNumber = Db::querySingle("SELECT COUNT(*) from players_2_teams WHERE TEAM_ID = ?", $teamID);
        for ($i = 0; $i < $playersNumber; $i++) {
            $teamRole = $_GET['role'];
            $playerID = $_GET['playerID'];
            Db::query('UPDATE players_2_teams SET player_role = ? WHERE PLAYER_ID =? AND TEAM_ID = ?', $teamRole[$i], $playerID[$i], $teamID);
        }
    }
    $teamName = Db::querySingle("SELECT NAME from TEAMS WHERE ID = ?", $teamID);
    $players = Db::queryAll("SELECT pl.id,pl.name,plt.player_role
    FROM players pl 
    JOIN players_2_teams plt on plt.player_id = pl.id
    WHERE plt.team_id = ?", $teamID);
    $leagues = Db::queryAll("SELECT c.NAME cName, ca.NAME caName, t.COMPETITION_ANNUAL_ID
    from competitions c
    JOIN teams_2_competition_annuals t on t.TEAM_ID = ?
    JOIN competition_annuals ca on ca.ID = t.COMPETITION_ANNUAL_ID
    WHERE c.ID = ca.COMPETITION_ID", $teamID);
    if ($players) { ?>
        <div class="mt-5">
            <div class="containerInput">
                <div class="row">
                    <div class="col-md-6 text-center" style="margin:auto;">
                        <?php
                        if (file_exists('images/' . $teamID . '.png')) {
                            echo '<img src="images/' . $teamID . '.png">';
                        } else {
                            echo '<img src="images/no_logo.png">';
                        }
                        ?>
                    </div>
                    <div class="text-left col-md-6 " style="margin:auto;">
                        <form action="index.php" method="GET">
                            <table class="content-table">
                                <input type="hidden" name="page" value="team">
                                <input type="hidden" name="id" value="<?= $teamID ?>">
                                <input type="hidden" name="edit" value="true">
                                <h2><?= $teamName ?></h2>
                                <thead>
                                    <tr>
                                        <th>Jméno</th>
                                        <th>Role</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($players as $player) {
                                    ?>
                                        <tr>
                                            <td>
                                                <?php echo ('<p><a href="index.php?page=player&id=' . $player['id'] . '">' . $player['name'] .  '</a></p>');
                                                ?>
                                            </td>
                                            <td>
                                                <?php

                                                if ($player['player_role'] == 0) {
                                                    echo "Hráč";
                                                } else if ($player['player_role'] == 1) {
                                                    echo "Kapitán";
                                                } else {
                                                    echo "Neurčeno";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <input type="hidden" name="playerID[]" value="<?= $player['id'] ?>">
                                                <select name='role[]'>
                                                    <option value="0">Hráč</option>
                                                    <option value="1">Kapitán</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <input type="submit" class="btn btn-primary" value="Edit">
                        </form>
                    </div>

                </div>
                <div class="text-center">
                    <?php
                    if ($leagues) {
                        echo ('<p>Ligy</p>');
                        foreach ($leagues as $league) {
                            echo ('<p><a href="index.php?page=table&id=' . $league['COMPETITION_ANNUAL_ID'] . '">' . $league['cName'] . ' ' . $league['caName'] . '</a></p>');
                        }
                    } else {
                        echo ('<p>Není v lize</p>');
                    }
                    ?>
                </div>
            </div>
        </div>
<?php
    } else {
        echo 'žádní hráči týmu';
    }
} else {
    echo ("Tým neexistuje");
}
