<?php
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $competitionAnnual = 13;
    $sql = ("SELECT s.*, th.NAME t_home, ta.NAME t_away 
    FROM series s 
    JOIN teams th ON th.ID=s.HOME_TEAM 
    JOIN teams ta ON ta.ID=s.AWAY_TEAM
    JOIN teams_2_competition_annuals tcah ON (tcah.team_id = th.ID AND s.COMPETITION_ANNUAL_ID = tcah.COMPETITION_ANNUAL_ID)
    JOIN teams_2_competition_annuals tcaa ON (tcaa.team_id = ta.ID AND s.COMPETITION_ANNUAL_ID = tcaa.COMPETITION_ANNUAL_ID)
    WHERE s.COMPETITION_ANNUAL_ID = '$competitionAnnual' AND tcah.ACTIVE = 1 AND tcaa.ACTIVE = 1
    ORDER BY s.ROUND");
    if (($result = $conn->query($sql)) == true) { ?>
        <div class="mt-5">
            <div class="containerInput">
                <h2>Zápasy</h2>
                <?php
                $curr_fixture = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    if (($row['ROUND'] != $curr_fixture)) {
                        if ($curr_fixture > 0) {
                            echo "</tbody></table>";
                        }

                        echo "<p class='text_center'>Kolo";
                        echo $row['ROUND'];
                        echo "</p>";
                        $curr_fixture++;
                ?>
                        <table class="content-table">
                            <thead>
                                <tr>
                                    <th class="col-md-5">Team1</th>
                                    <th class="col-md-2">Skóre</th>
                                    <th class="col-md-5">Team2</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                        }
                        echo ('<tr><td>');
                        echo $row['t_home'];
                        echo ('</td><td>');
                            ?>
                            <form id="form-id<?php echo $row['ID'] ?>" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="page" value="match_update">
                                <input type="hidden" name="id" value="<?php echo $competitionAnnual ?>">
                                <input type="hidden" name="series_id" value="<?php echo $row['ID']; ?>">
                                <div onmouseover="" style="cursor: pointer;" onclick="document.getElementById('form-id<?php echo $row['ID'] ?>').submit();">zadat</div>
                            </form>
                <?php
                    echo ('</td><td>');
                    echo $row['t_away'];
                    echo ('</td></tr>');
                }
                echo "</tbody></table></div>";
                $conn->close();
            }
        }
                ?>