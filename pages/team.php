<?php
$teamID = $_GET['id'];
if (Db::querySingle("SELECT COUNT(*) from TEAMS WHERE ID = ?", $teamID)) {
    $teamName = Db::querySingle("SELECT NAME from TEAMS WHERE ID = ?", $teamID);
    $players = Db::queryAll("SELECT pl.name,plt.player_role
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
                    <div class="col-md-6 text-center">
                        <img src="images/581.png" alt="HTML tutorial">
                    </div>
                    <div class="text-left col-md-6 " style="margin:auto;">
                        <table class="content-table">
                            <h2><?= $teamName ?></h2>
                            <thead>
                                <tr>
                                    <th>Jméno</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($players as $player) {
                                ?>
                                    <tr>
                                        <td><?= $player['name'] ?></td>
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
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-center">
                    <?php
                    if ($leagues) {
                        echo ('<p>Ligy</p>');
                        foreach ($leagues as $leagues) {
                            echo ('<p><a href="index.php?page=table&id=' . $leagues['COMPETITION_ANNUAL_ID'] . '">' . $leagues['cName'] . ' ' . $leagues['caName'] . '</p>');
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
