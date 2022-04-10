<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comp_create = $comp_err = "";
    if ($_POST['players'] <= 0) {
        $comp_err = "vyber počet hráčů nad 0";
    } else if ($_POST['competition_name'] != "") {
        if (Db::query("SELECT NAME from competitions where NAME = ?", $_POST['competition_name'])) {
            $comp_err = "competition již existuje";
        } else {
            Db::insert('competitions', array(
                'NAME' => $_POST['competition_name'],
                'PLAYERS_COUNT' => $_POST['players']
            ));
            $comp_create = "vytvořeno";
        }
    } else {
        $comp_err = "nezadal si jméno";
    }
}

?>


<form action="index.php?page=competition_create" method="post">
    <?php
    if (!empty($comp_err)) {
        echo '<div class="alert alert-danger">' . $comp_err . '</div>';
    }
    if (!empty($comp_create)) {
        echo '<div class="alert alert-success">' . $comp_create . '</div>';
    }
    ?>
    <span>Jméno comptetition :</span>
    <input type="text" name="competition_name" value="<?php if (isset($_POST['competition_name'])) {
                                                            echo $_POST['competition_name'];
                                                        } else {
                                                            echo '';
                                                        }  ?>">
    <span>Počet hráčů :</span>
    <input type="number" name="players" value="<?php if (isset($_POST['players'])) {
                                                    echo $_POST['players'];
                                                } else {
                                                    echo '0';
                                                }  ?>">

    <div class=" form-group mt-2">
        <input type="submit" class="btn btn-primary" value="Vytvořit ligu">
    </div>
</form>
</div>