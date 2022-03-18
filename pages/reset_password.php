<?php

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("location: index.php?page=login");
    exit;
}

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Prosím zadej nové heslo.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Heslo musí obsahovat minimálně 6 znaků.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Prosím potvrď heslo.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Hesla se neshodují.";
        }
    }

    // Check input errors before updating the database
    if (empty($new_password_err) && empty($confirm_password_err)) {
        // Prepare an update statement

        if (Db::query("UPDATE users SET password = ? WHERE id = ?", password_hash($_SESSION["username"] . $new_password, PASSWORD_DEFAULT), $_SESSION["id"]) == 1) {
            session_destroy();
            header("location: index.php?page=login");
            exit();
        } else {
            echo "Něco je špatně. Zkus to znovu později.";
        }
    }
}
?>

<div class="mt-5">
    <h2>Resetování hesla</h2>
    <p>Vyplň.</p>
    <form action="index.php?page=resetpassword" method="post">
        <div class="form-group mt-2">
            <label>Nové heslo</label>
            <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
            <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
        </div>
        <div class="form-group mt-2">
            <label>Potvrď nové heslo</label>
            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group mt-2">
            <input type="submit" class="btn btn-primary" value="Uložit">
            <a class="btn btn-link ml-2" href="index.php?page=welcome">Zpět</a>
        </div>
    </form>
</div>