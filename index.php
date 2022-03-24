<?php
ob_start();
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Web</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/style2.css" rel="stylesheet" />

</head>

<body>
    <?php
    $token = 'jChIV7kuI63iaw0fcVpc2FxQLQJUWC8uaSk4U9kM';
    require_once 'pages/db.php';
    Db::connect('localhost', 'letka', 'root', '');
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php?page=home">Web</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 menu_main">

                    <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?page=home">Hlavní stránka</a></li>
                    <?php for ($i = 0; $i < 2; $i++) { ?>
                        <li class="nav-item"><a class="nav-link"> <?php if ($i == 0) {
                                                                        echo "Tabulka";
                                                                    } else {
                                                                        echo "Zápasy";
                                                                    } ?></a>
                            <ul class="submenu">
                                <?php
                                $a = "";
                                $result = Db::queryAll('SELECT co.NAME,co.ID,ca.ID ca_ID, ca.NAME ca_name 
                            FROM competitions co 
                            INNER JOIN competition_annuals ca ON co.ID = ca.COMPETITION_ID 
                            ORDER BY co.ID ASC, ca.ANNUAL ASC');
                                if ($result) {
                                    foreach ($result as $row) {
                                        if ($a != $row['NAME']) {
                                            if ($a != "") {
                                                echo '</ul>';
                                                echo "</li>";
                                            }
                                            $a = $row['NAME'];
                                            echo "<li class='nav-item'><a class='nav-link' href='#'></a>";
                                            echo $row['NAME'];
                                            echo "<ul class='submenu2'>";
                                        }
                                        if ($i == 0) {
                                            echo "<li class='nav-item'><a class='nav-link' href='index.php?page=table&id=";
                                        } else {
                                            echo "<li class='nav-item'><a class='nav-link' href='index.php?page=fixtures&id=";
                                        }
                                        echo $row['ca_ID'];
                                        echo "'>";
                                        echo $row['ca_name'];
                                        echo '</a></li>';
                                    }
                                    echo '</ul>';
                                    echo "</li>";
                                }
                                ?>
                        </li>
                </ul>
                </li>
            <?php } ?>



            <?php if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
            ?>
                <li class="nav-item"><a class="nav-link"> Admin</a>
                    <ul class="submenu">
                        <li class="nav-item"><a class="nav-link" href="index.php?page=logout">Odhlásit se</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?page=league_create">Vytvořit ligu</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?page=team_create">Vytvořit team</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?page=player_create">Vytvořit hráče</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php?page=administration">Administrace</a></li>
                    </ul>
                </li>
            <?php
            } else {
            ?>
                <li class="nav-item"><a class="nav-link" href="index.php?page=registration_user">Registrace</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=login">Login</a></li>
            <?php
            }
            ?>
            </ul>
            </div>
        </div>
    </nav>
    <div class="container">

        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 'home';
        }

        $include = include 'pages/' . $page . '.php';
        if (!$include) {
            echo ('Podstránka nenalezena');
        }

        ?>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>

</html>