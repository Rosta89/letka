<?php
require_once('db.php');
$team_name = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (empty(trim($_GET["team"]))) {
        $team_name_err = "tym neexistuje";
    } else {
        $sql = "SELECT id, name, captain, contact, player1, player2, player3, player4 FROM teams WHERE name = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_id);

            // Set parameters
            $param_id = trim($_GET["team"]);
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $name, $captain, $contact, $player1, $player2, $player3, $player4);
                    if (mysqli_stmt_fetch($stmt)) {
                    }
                }
            }
        }
    }
}
?>
<div class="mt-5">
    <div class=row>
        <div class="col-md-6 text-center">
            <img src="images/581.png" alt="HTML tutorial">
        </div>
        <div class="text-left col-md-6 " style="margin:auto;">
            <p> Jméno týmu: <?php echo $name;
                            ?>

            </p>
            <p>
                Kapitán: <?php echo $captain;
                            ?>
            </p>
            <p>
                kontakt: <?php echo $contact;
                            ?>
            </p>
            <p>
                Hráči:<?php echo $player1;
                        ?>,
                <?php echo $player2;
                if ((strlen($player3)) > 2) {
                    echo ", ";
                    echo $player3;
                }
                if ((strlen($player4)) > 2) {
                    echo ", ";
                    echo $player4;
                }
                ?>
            </p>
        </div>
    </div>
</div>