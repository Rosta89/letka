<?php
$competitionAnnual = $_GET['id'];
$result = Db::queryAll("SELECT s.*, th.NAME t_home, ta.NAME t_away 
FROM series s 
JOIN teams th ON th.ID=s.HOME_TEAM 
JOIN teams ta ON ta.ID=s.AWAY_TEAM
JOIN teams_2_competition_annuals tcah ON (tcah.team_id = th.ID AND s.COMPETITION_ANNUAL_ID = tcah.COMPETITION_ANNUAL_ID)
JOIN teams_2_competition_annuals tcaa ON (tcaa.team_id = ta.ID AND s.COMPETITION_ANNUAL_ID = tcaa.COMPETITION_ANNUAL_ID)
WHERE s.COMPETITION_ANNUAL_ID = ? AND tcah.ACTIVE = 1 AND tcaa.ACTIVE = 1
ORDER BY s.ROUND", $competitionAnnual);

if ($result) { ?>
    <div class="mt-5">
        <div class="containerInput">
            <h2>Zápasy</h2>
            <?php
            $curr_fixture = 0;
            foreach ($result as $row) {
                if (($row['ROUND'] != $curr_fixture)) {
                    if ($curr_fixture > 0) {
                        echo "</tbody></table>";
                    }

                    echo "<p class='text_center'>Kolo";
                    echo $row['ROUND'];
                    echo "</p>";
                    $curr_fixture++;
            ?>
                    <table class="content-table" style="overflow: visible;">
                        <thead>
                            <tr>
                                <th>Team1</th>
                                <th>Skóre</th>
                                <th>Team2</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                }
                echo ('<tr><td>');
                echo $row['t_home'];
                echo ('</td><td>');
                $matchresult = Db::queryAll("SELECT * FROM matches WHERE SERIES_ID = ? ORDER BY ID", $row['ID']);
                if (is_null($row['HOME_SCORE'])) {
                    echo '-:-';
                } else {
                    echo ('<div class="popup" onclick="myFunction(event)">');
                    echo $row['HOME_SCORE'] . ':' . $row['AWAY_SCORE'];

                    echo ('<span class="popuptext">');
                    foreach ($matchresult as $match) {
                        echo ('<p>' . $match['HOME_SCORE'] . ':' . $match['AWAY_SCORE'] . '</p>');
                    }
                    echo ('</span>');
                }


                echo ('</td></div><td>');
                echo $row['t_away'];
                echo ('</td><td><a href="index.php?page=series&id=' . $row['ID'] . '">Upravit</a></td>');
                echo ('</tr>');
            }
            echo "</tbody></table></div>";
        }
                ?>
                <script>
                    function myFunction(event) {
                        const currentlyVisible = document.querySelector('.popup .show');
                        if (currentlyVisible) {
                            currentlyVisible.classList.toggle('show');
                        }
                        var popup = event.currentTarget.querySelector('.popuptext');
                        popup.classList.toggle("show");
                    }
                </script>