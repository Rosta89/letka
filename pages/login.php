<?php

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("location: index.php?page=welcome");
    exit;
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $login_err = "Špatné jméno nebo heslo.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $login_err = "Špatné jméno nebo heslo.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $result = Db::queryAll("SELECT id, username, password FROM users WHERE username = ?", $username);
        if (count($result) == 1) {
            if (password_verify($username . $password, $result[0]['password'])) {
                // Password is correct, so start a new session
                session_start();

                // Store data in session variables
                $_SESSION["logged_in"] = true;
                $_SESSION["id"] = $result[0]['id'];
                $_SESSION["username"] = $result[0]['username'];

                // Redirect to login page
                header("location: index.php?page=welcome");
                exit();
            } else {
                // Password is not valid, display a generic error message
                $login_err = "Špatné jméno nebo heslo.";
            }
            $login_err = "Špatné jméno nebo heslo.";
        }
    } else {
        echo "Zadejte jméno i heslo.";
    }
}
?>

<div class="mt-5">
    <div class="containerInput" style="display: flex;">
        <form action="index.php?page=login" method="post">
            <h2>Přihlášení</h2>
            <?php
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }
            ?>
            <div class="inputBox">
                <span>Jméno</span>
                <div class="box">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 172 172" style=" fill:#000000;">
                            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                                <path d="M0,172v-172h172v172z" fill="none"></path>
                                <g fill="#ffffff">
                                    <path d="M86,17.2c-19.00027,0 -34.4,15.39973 -34.4,34.4v5.73333c0,19.00027 15.39973,34.4 34.4,34.4c19.00027,0 34.4,-15.39973 34.4,-34.4v-5.73333c0,-19.00027 -15.39973,-34.4 -34.4,-34.4zM63.62656,112.10234c-16.82733,4.39747 -32.70069,12.67676 -38.55443,20.29063c-5.24027,6.8112 -0.25182,16.6737 8.34245,16.6737h105.15964c8.59427,0 13.58271,-9.86796 8.34245,-16.68489c-5.85373,-7.61387 -21.72163,-15.88196 -38.54323,-20.27943c-6.02,5.16573 -13.82504,8.29766 -22.37344,8.29766c-8.55413,0 -16.35344,-3.13766 -22.37344,-8.29766z"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                </div>
            </div>
            <div class="inputBox">
                <span>Heslo</span>
                <div class="box">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 172 172" style=" fill:#000000;">
                            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                                <path d="M0,172v-172h172v172z" fill="none"></path>
                                <g fill="#ffffff">
                                    <path d="M86,11.46667c-22.09818,0 -40.13333,18.03515 -40.13333,40.13333v11.46667h-11.46667c-6.33533,0 -11.46667,5.13133 -11.46667,11.46667v68.8c0,6.33533 5.13133,11.46667 11.46667,11.46667h103.2c6.33533,0 11.46667,-5.13133 11.46667,-11.46667v-68.8c0,-6.33533 -5.13133,-11.46667 -11.46667,-11.46667h-11.46667v-11.46667c0,-21.37626 -16.99027,-38.59356 -38.09531,-39.71901c-0.64841,-0.26118 -1.33911,-0.4016 -2.03802,-0.41432zM86,22.93333c15.90235,0 28.66667,12.76431 28.66667,28.66667v11.46667h-57.33333v-11.46667c0,-15.90235 12.76431,-28.66667 28.66667,-28.66667zM51.6,97.46667c6.33533,0 11.46667,5.13133 11.46667,11.46667c0,6.3296 -5.13133,11.46667 -11.46667,11.46667c-6.33533,0 -11.46667,-5.13707 -11.46667,-11.46667c0,-6.33533 5.13133,-11.46667 11.46667,-11.46667zM86,97.46667c6.33533,0 11.46667,5.13133 11.46667,11.46667c0,6.3296 -5.13133,11.46667 -11.46667,11.46667c-6.33533,0 -11.46667,-5.13707 -11.46667,-11.46667c0,-6.33533 5.13133,-11.46667 11.46667,-11.46667zM120.4,97.46667c6.33533,0 11.46667,5.13133 11.46667,11.46667c0,6.3296 -5.13133,11.46667 -11.46667,11.46667c-6.33533,0 -11.46667,-5.13707 -11.46667,-11.46667c0,-6.33533 5.13133,-11.46667 11.46667,-11.46667z"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                </div>
            </div>
            <label>
                <input type="checkbox"> Zapamatovat heslo
            </label>
            <div class="inputBox">
                <div class="box">
                    <input type="submit" value="Přihlásit se">
                </div>
            </div>
            <p><a href="index.php?page=registration_user" class="forgot">Registrace</a></p>
            <p><a href=" #" class="forgot">Zapomenuté heslo</a></p>
        </form>
    </div>
</div>