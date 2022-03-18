<?php

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("location: login.php");
    exit;
}
?>
<h1 class="my-5">Ahoj, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Vítej u nás.</h1>
<p>
    <a href="index.php?page=reset_password" class="btn btn-warning">Resetuj heslo</a>
    <a href="index.php?page=logout" class="btn btn-danger ml-3">Odhlásit se</a>
</p>