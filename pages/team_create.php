<?php

$team_name = $new_team = $captain  = $player1 = $player2 = $player3 = $player4 = "";
$error = "";
//TODO ověření pro vytvoření týmu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["team_name"]))) {
        $error = "Zadej název týmu";
    } else if (($_POST["captain"] == '')) {
        $error = "Zadej jmeno kapitána";
    } else if (($_POST["player1"] == '' or (($_POST["player2"]) == ''))) {
        $error = "Nezadal si dostatek hráčů";
    } else {
        if (Db::query("SELECT ID FROM teams WHERE NAME = ?", $_POST["team_name"]) == 0) {
            $i = 0;
            $team_name = trim($_POST["team_name"]);
            $players[$i++] = ($_POST["captain"]);
            //$contact = trim($_POST["contact"]);
            $players[$i++] = ($_POST["player1"]);
            $players[$i++] = ($_POST["player2"]);
            $players[$i++] = ($_POST["player3"]);
            $players[$i] = ($_POST["player4"]);
            Db::beginTransaction();
            Db::insert('teams', array(
                'NAME' => $_POST["team_name"]
            ));
            //todo error checking, todo players roles
            $player_role = 1;
            for ($i = 0; $i < 4; $i++) {
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
                    <input type="text" name="team_name" value="<?php echo $team_name; ?>">
                </div>
            </div>
            <div class="inputBox">
                <span>Kapitán :</span>
                <div class="box">
                    <div class="icon">
                        <ion-icon name="person"></ion-icon>
                    </div>
                    <select name='captain'>
                        <option value=""></option>
                        <?php
                        $result = Db::queryAll('SELECT NAME FROM players ORDER BY NAME ASC');
                        if ($result) {
                            foreach ($result as $row) {
                        ?>
                                <option value="<?php echo $row['NAME']; ?>"><?php echo $row['NAME']; ?></option>
                        <?php
                            }
                        }

                        ?>
                    </select>
                </div>
            </div>
            <?php for ($i = 1; $i < 5; $i++) { ?>
                <div class="inputBox">
                    <span>Hráč <?php echo $i; ?> :</span>
                    <div class="box">
                        <div class="icon">
                            <ion-icon name="person-add-outline"></ion-icon>
                        </div>
                        <select name='player<?php echo $i; ?>'>
                            <option value=""></option>
                            <?php
                            if ($result) {
                                foreach ($result as $row) {
                            ?>
                                    <option value="<?php echo $row['NAME']; ?>"><?php echo $row['NAME']; ?></option>
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