<?php
$seriesID = $_GET['id'];
$result = Db::queryOne("SELECT s.*, th.NAME t_home, ta.NAME t_away 
FROM series s 
JOIN teams th ON th.ID=s.HOME_TEAM 
JOIN teams ta ON ta.ID=s.AWAY_TEAM
WHERE s.ID = ?", $seriesID);
$matches = Db::queryAll("SELECT ma.*, th.NAME t_home, ta.NAME t_away 
FROM series s 
JOIN teams th ON th.ID=s.HOME_TEAM 
JOIN teams ta ON ta.ID=s.AWAY_TEAM
JOIN matches ma ON ma.SERIES_ID = s.ID
WHERE s.ID = ?", $seriesID);

if ($result) { ?>
    <div class="containerInput">
        <div class="text-center mt-5">
            <table class="content-table">
                <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="page" value="matches">
                    <input type="hidden" name="id" value="<?php echo $seriesID ?>">
                    <thead>
                        <tr>
                            <th class="col-md-5">Team1</th>
                            <th class="col-md-2">Skóre</th>
                            <th class="col-md-5">Team2</th>
                            <th class="col-md-2">Smazat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        echo ('<tr>');
                        echo '<td>' . $result['t_home'] . '</td>';
                        if (is_null($result['HOME_SCORE'])) {
                            echo '<td>-:-</td>';
                        } else {
                            echo '<td>' . $result['HOME_SCORE'] . ':' . $result['AWAY_SCORE'] . '</td>';
                        }
                        echo '<td>' . $result['t_away'] . '</td>';
                        echo '<tr>';

                        foreach ($matches as $row) {
                            echo ('<tr>');
                            echo '<td>' . $row['t_home'] . '</td>';
                            echo ('<td><a href="index.php?page=matches&id=' . $seriesID . '&match_id=' . $row['ID'] . '">');
                            if (is_null($row['HOME_SCORE'])) {
                                echo '<td>-:-</td>';
                            } else {
                                echo $row['HOME_SCORE'] . ':' . $row['AWAY_SCORE'];
                            }
                            echo ('</td></a>');
                            echo '<td>' . $row['t_away'] . '</td>';
                            echo ('<td><a href="index.php?page=matches_delete&id=' . $seriesID . '&match_id=' . $row['ID'] . '">');
                            echo ('smazat</td></a>');
                            echo '<tr>';
                        }
                        ?>
                    <?php

                    echo "</tbody></table>";
                }
                    ?>
                    <div class=" form-group mt-2">
                        <input type="submit" class="btn btn-primary" value="Přidat zápas">
                    </div>
                </form>
        </div>
    </div>