<?php
$teamID = $_GET['id'];
if (Db::querySingle("SELECT COUNT(*) from TEAMS WHERE ID = ?",$teamID)) {
    $teamName = Db::querySingle("SELECT NAME from TEAMS WHERE ID = ?",$teamID);
    $players = Db::queryAll("SELECT pl.name,plt.player_role
    FROM players pl 
    JOIN players_2_teams plt on plt.player_id = pl.id
    WHERE plt.team_id = ?", $teamID);
    if ($players){?>
        <div class="mt-5">
            <div class="containerInput">
                <table class="content-table">
                    <h2><?=$teamName?></h2>
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
                            <td><?=$player['name']?></td>
                            <td><?=$player['player_role']?></td>
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
            echo 'žádní hráči týmu';
        }
    } else {
        echo("Tým neexistuje");
}

