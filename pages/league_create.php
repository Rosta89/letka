    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $league_create = $league_err = $send_err = "";
        $n = 0;
        if ($_POST['creatab'] == null) {
            header("location: index.php?page=home");
            $creatab_err = "nevybral si team";
        } else if ($_POST['league_name'] == null) {
            header("location: index.php?page=home");
            $creatab_err = "nezadal si jmeno ligy";
        } else {
            $table_teams = $_POST['creatab'];

            //VLOŽENÍ DO TABULKY COMPETITIONS ANNUALS;
            //to-do error checking;
            $annual = DB::querySingle("SELECT MAX(ANNUAL) ann from competition_annuals where COMPETITION_ID = ?", $_POST['league']) + 1;
            $compAnnualID = DB::getLastId(Db::query("INSERT INTO competition_annuals (NAME,COMPETITION_ID,ANNUAL) VALUES (?,?,?)", $_POST['league_name'], $_POST['league'], $annual));
            $n = count($table_teams);
            $a = $n;
            if ($n % 2 == 0) {
                $num_week = $n - 1;
                $n2 = ($n - 1) / 2;
            } else {
                $num_week = $n;
                $table_teams[$n] = "0";
                $n++;
                $n2 = ($n - 1) / 2;
            }
            for ($i = 0; $i < $n; $i++) {
                //VLOŽENÍ TÝMU DO TABULKY S ANNUALS
                Db::query("INSERT INTO teams_2_competition_annuals (TEAM_ID,COMPETITION_ANNUAL_ID) VALUES (?,?)", $table_teams[$i], $compAnnualID);
            }
            $j = 0;
            for ($i = 0; $i < $n; $i++) {
                for ($x = 0; $x < $num_week; $x++) {
                    for ($i = 0; $i < $n2; $i++) {
                        $team1 = $table_teams[$n2 - $i];
                        $team2 = $table_teams[$n2 + $i + 1];
                        //Vložení do tabulky SERIES
                        $param_home = $team1;
                        $param_away = $team2;
                        $param_round = $x + 1;
                        Db::query("INSERT INTO series (COMPETITION_ANNUAL_ID,HOME_TEAM,AWAY_TEAM,ROUND) VALUES (?,?,?,?)", $compAnnualID, $param_home, $param_away, $param_round);
                    }
                    $tmp = $table_teams[1];
                    for ($i = 1; $i < sizeof($table_teams) - 1; $i++) {
                        $table_teams[$i] = $table_teams[$i + 1];
                    }
                    $table_teams[sizeof($table_teams) - 1] = $tmp;
                }
            }
            if (empty($send_err)) {
                $league_create = "liga založena";
            }
        }
    }











    //bez tymu s ID 0
    $result = Db::queryAll("SELECT ID,NAME FROM TEAMS WHERE ID <> 0;");
    ?>
    <div class="text-center mt-5">
        <h2>Týmy</h2>
        <?php
        if (!empty($league_err)) {
            echo '<div class="alert alert-danger">' . $league_err . '</div>';
        }
        if (!empty($send_err)) {
            echo '<div class="alert alert-danger">' . $send_err . '</div>';
        }
        if (!empty($league_create)) {
            echo '<div class="alert alert-success">' . $league_create . '</div>';
        }
        ?>
        <form action="index.php?page=league_create" method="post">
            <table class="content-table">
                <thead">
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">jméno týmu</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            foreach ($result as $row) {
                                echo ('<th scope="row">');
                            ?>
                                <input class="form-check-input <?php echo (!empty($creatab_err)) ? 'is-invalid' : ''; ?>" type="checkbox" value="<?php echo $row['ID']; ?>" name=" creatab[]">
                            <?php
                                echo ('</th><td>');
                                echo $row['NAME'];
                                echo ('</td></tr>');
                            }
                            ?>
                    </tbody>
            </table>
            <label for="league">Vyber ligu </label>
            <select name='league'>
                <?php
                $result = Db::queryAll('SELECT ID,NAME FROM competitions ORDER BY NAME ASC');
                if ($result) {
                    foreach ($result as $row) {
                ?>
                        <option value="<?php echo $row['ID']; ?>"><?php echo $row['NAME']; ?></option>
                <?php
                    }
                }

                ?>
            </select>
            <span>Jméno ligy :</span>
            <input type="text" name="league_name">
            <div class=" form-group mt-2">
                <input type="submit" class="btn btn-primary" value="Vytvořit ligu">
            </div>

        </form>
    </div>