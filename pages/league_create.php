    <?php
    //TODO ošetřit počet hráčů v týmu na ligu, zda-li již ten hráč není v jiném týmu
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $league_create = $league_err = "";
        $n = 0;
        if (!isset($_POST['creatab'])) {
            $league_err = "nevybral si team";
        } else if ($_POST['league_name'] == null) {
            $league_err = "nezadal si jmeno ligy";
        } else if (count($_POST['creatab']) <= 1) {
            $league_err = "nevybral si dostatek týmu";
        } else {
            $table_teams = $_POST['creatab'];
            $annual = Db::querySingle("SELECT MAX(ANNUAL) ann from competition_annuals where COMPETITION_ID = ?", $_POST['league']) + 1;
            Db::beginTransaction(); // začátek transakce
            $compAnnualID = Db::getLastId(Db::insert(
                'competition_annuals',
                array(
                    'NAME' =>  $_POST['league_name'],
                    'COMPETITION_ID' => $_POST['league'],
                    'ANNUAL' => $annual
                )
            ));
            //Rozřazení týmu + vložení týmu do tabulky
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
                Db::insert(
                    'teams_2_competition_annuals',
                    array(
                        'TEAM_ID' => $table_teams[$i],
                        'COMPETITION_ANNUAL_ID' => $compAnnualID
                    )
                );
            }

            $j = 0;
            for ($i = 0; $i < $n; $i++) {
                for ($x = 0; $x < $num_week; $x++) {
                    for ($i = 0; $i < $n2; $i++) {
                        $team1 = $table_teams[$n2 - $i];
                        $team2 = $table_teams[$n2 + $i + 1];
                        $param_round = $x + 1;
                        Db::insert(
                            'series',
                            array(
                                'COMPETITION_ANNUAL_ID' => $compAnnualID,
                                'HOME_TEAM' => $team1,
                                'AWAY_TEAM' => $team2,
                                'ROUND' => $param_round
                            )
                        );
                    }
                    $tmp = $table_teams[1];
                    for ($i = 1; $i < sizeof($table_teams) - 1; $i++) {
                        $table_teams[$i] = $table_teams[$i + 1];
                    }
                    $table_teams[sizeof($table_teams) - 1] = $tmp;
                }
            }
            if (empty($league_err)) {
                Db::commitTransaction(); //Ukončení transakce
                $league_create = "liga založena";
            } else {
                Db::rollbackTransaction();
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