<?php

$team_name = $new_team = "";
$error = "";
//TODO ověření pro vytvoření týmu, počet hráčů?
//Ověření zda je zadáno jméno týmu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["team_name"]))) {
        $error = "Zadej název týmu";
    } else {
        //Ověření zda již tým neexistuje, potom vkladání do DB
        if (Db::query("SELECT ID FROM teams WHERE NAME = ?", $_POST["team_name"]) == 0) {
            Db::beginTransaction();
            Db::insert('teams', array(
                'NAME' => $_POST["team_name"]
            ));
            //todo error checking, todo players roles
            $player_role = 1;
            $players = $_POST["players"];
            for ($i = 0; $i < count($players); $i++) {
                if ($players[$i] != "") {
                    if (Db::insert('players_2_teams', array(
                        'PLAYER_ID' => Db::querySingle("SELECT ID from players WHERE NAME = ?", $players[$i]),
                        'TEAM_ID' => Db::querySingle("SELECT ID from teams WHERE NAME = ?", $_POST["team_name"]),
                        'PLAYER_ROLE' => $player_role
                    )) != 1) {
                        Db::rollbackTransaction();
                        echo "Stala se chyba, zkus to znovu";
                        exit();
                    }
                }
            }
            $new_team = "Team založen";
            Db::commitTransaction();
        } else {
            $error = "Jméno je již zabrané.";
        }
    }
}
?>
<div class="mt-5">
    <div class="containerInput" style="display: flex;">
        <form action="index.php?page=team_create" method="POST">
            <?php
            if (!empty($new_team)) {
                echo '<div class="alert alert-success">' . $new_team . '</div>';
            }
            if (!empty($error)) {
                echo '<div class="alert alert-danger">' . $error . '</div>';
            }
            ?>
            <div class="inputBox">
                <span>Jméno týmu :</span>
                <div class="box">
                    <div class="icon">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                    <input type="text" name="team_name" value="<?= $team_name ?>">
                </div>
            </div>
            <div class="inputBox">
                <span>Kapitán :</span>
                <div class="box">
                    <div class="icon">
                        <ion-icon name="person"></ion-icon>
                    </div>
                    <select name="players[0]">
                        <option value=""></option>
                        <?php
                        $result = Db::queryAll('SELECT NAME FROM players ORDER BY NAME ASC');
                        if ($result) {
                            foreach ($result as $row) {
                        ?>
                                <option value="<?= $row['NAME'] ?>"><?= $row['NAME'] ?></option>
                        <?php
                            }
                        }

                        ?>
                    </select>
                </div>
            </div>
            <?php for ($i = 1; $i < 5; $i++) { ?>
                <div class="inputBox">
                    <span>Hráč <?= $i ?> :</span>
                    <div class="box">
                        <div class="icon">
                            <ion-icon name="person-add-outline"></ion-icon>
                        </div>
                        <select name='players[<?= $i ?>]'>
                            <option value=""></option>
                            <?php
                            if ($result) {
                                foreach ($result as $row) {
                            ?>
                                    <option value="<?= $row['NAME'] ?>"><?= $row['NAME'] ?></option>
                            <?php
                                }
                            }

                            ?>
                        </select>
                    </div>
                </div>
            <?php } ?>

            <div class="inputBox">
                <div class="box">
                    <input type="submit" value="Potvrdit">
                </div>
            </div>

    </div>
    </form>
</div>