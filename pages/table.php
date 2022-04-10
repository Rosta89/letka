<?php


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $competitionsAnnualsId = $_GET['id'];
    $result = Db::queryAll("SELECT ID,TEAM_NAME, 
    SUM(CASE WHEN TEAM_SCORE is not null then 1 ELSE 0 END) SERIES,
    SUM(CASE WHEN TEAM_SCORE>OPPONENT_SCORE then 3 ELSE 0 END) POINTS,
    SUM(CASE WHEN TEAM_SCORE>OPPONENT_SCORE then 1 ELSE 0 END) WINS,
    SUM(CASE WHEN TEAM_SCORE<OPPONENT_SCORE then 1 ELSE 0 END) LOSSES
      FROM (
    SELECT 
    te.ID, case when te.ID = se.HOME_TEAM then se.HOME_SCORE else se.AWAY_SCORE end TEAM_SCORE,
    case when te.ID = se.AWAY_TEAM then se.HOME_SCORE else se.AWAY_SCORE end OPPONENT_SCORE,
    te.NAME TEAM_NAME FROM teams_2_competition_annuals tca
    JOIN TEAMS te ON te.ID = tca.TEAM_ID
    JOIN SERIES se ON ((te.ID = se.HOME_TEAM OR te.ID = se.AWAY_TEAM) and se.COMPETITION_ANNUAL_ID = tca.COMPETITION_ANNUAL_ID)
    JOIN teams_2_competition_annuals tca2 ON ((tca2.TEAM_ID = se.HOME_TEAM OR tca2.TEAM_ID  = se.AWAY_TEAM) and se.COMPETITION_ANNUAL_ID = tca2.COMPETITION_ANNUAL_ID and tca2.TEAM_ID <> te.ID)
    WHERE HOME_TEAM <>0 AND AWAY_TEAM <> 0 AND TCA.COMPETITION_ANNUAL_ID = ? AND tca.ACTIVE = 1 AND tca2.ACTIVE = 1
    ) a GROUP BY ID,TEAM_NAME ORDER BY POINTS DESC
    ", $competitionsAnnualsId);
    $playerStats = Db::queryAll("SELECT te.ID TEAM_ID, te.NAME TEAM_NAME, pl.ID PLAYER_ID, pl.NAME PLAYER_NAME,
     COUNT(*) MATCHES,
     SUM(GOALS) GOALS,
     SUM(ASSISTS) ASSISTS,
     SUM(SAVES) SAVES,
     SUM(MVPS) MVPS,
     SUM(2*GOALS+ASSISTS+SAVES+2*MVPS) POINTS
     
     FROM 
     statistics st
     JOIN players pl ON pl.ID = st.PLAYER_ID
     JOIN matches ma ON ma.ID = st.MATCH_ID
     JOIN series se ON se.id = ma.SERIES_ID
     JOIN players_2_teams plt ON pl.id = plt.PLAYER_ID
     JOIN teams te ON te.id = plt.TEAM_ID
     JOIN teams_2_competition_annuals tea ON tea.TEAM_ID = plt.TEAM_ID AND tea.COMPETITION_ANNUAL_ID = ?
     WHERE se.COMPETITION_ANNUAL_ID = ?
     GROUP BY te.NAME,pl.NAME ORDER BY POINTS DESC
     ", $competitionsAnnualsId, $competitionsAnnualsId);

    //SQL 
    if ($result) {
?>
        <div class="mt-5">
            <div class="containerInput">
                <table class="content-table">
                    <h2>Tabulka</h2>
                    <thead>
                        <tr>
                            <th>Pořadí</th>
                            <th>Jméno týmu</th>
                            <th>Zápasy</th>
                            <th>Body</th>
                            <th>Výhry</th>
                            <th>Prohry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $i = 0;
                            foreach ($result as $row) {
                                $i++; ?>
                        <tr>
                            <td><?= $i ?>.</td>
                            <td><a href="index.php?page=team&id=<?= $row['ID'] ?>"><?= $row['TEAM_NAME'] ?></a></td>
                            <td><?= $row['SERIES'] ?></td>
                            <td><?= $row['POINTS'] ?></td>
                            <td><?= $row['WINS'] ?></td>
                            <td><?= $row['LOSSES'] ?></td>
                        </tr>
                    <?php
                            }
                    ?>
                    </tbody>
                </table>
                <table class="content-table">
                    <h2>Statistiky hráčů</h2>
                    <thead>
                        <tr>
                            <th>Pořadí</th>
                            <th>Jméno hráče</th>
                            <th>Tým</th>
                            <th>Zápasy</th>
                            <th>Góly</th>
                            <th>Assistence</th>
                            <th>Savy</th>
                            <th>MVPS</th>
                            <th>Body</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($playerStats as $row) {
                            $i++; ?>
                            <tr>
                                <td><?= $i ?>.</td>
                                <td><a href="index.php?page=player&id=<?= $row['PLAYER_ID'] ?>"><?= $row['PLAYER_NAME'] ?></a></td>
                                <td><a href="index.php?page=team&id=<?= $row['TEAM_ID'] ?>"><?= $row['TEAM_NAME'] ?></a></td>
                                <td><?= $row['MATCHES'] ?></td>
                                <td><?= $row['GOALS'] ?></td>
                                <td><?= $row['ASSISTS'] ?></td>
                                <td><?= $row['SAVES'] ?></td>
                                <td><?= $row['MVPS'] ?></td>
                                <td><?= $row['POINTS'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
<?php
    } else {
        echo 'liga neexistuje';
    }
}
?>