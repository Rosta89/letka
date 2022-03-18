<?php
require_once('db.php');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $homegoals = $_GET['homegoals'];
    $homeassists = $_GET['homeassists'];
    $homesaves = $_GET['homesaves'];
    $homemvps = $_GET['homemvps'];

    $awaygoals = $_GET['awaygoals'];
    $awayassists = $_GET['awayassists'];
    $awaysaves = $_GET['awaysaves'];
    $awaymvps = $_GET['awaymvps'];


    $variable = $_GET['variable'];
    $matchid = $_GET['id'];
    $homeScore = $_GET['homescore'];
    $awayScore = $_GET['awayscore'];
    $sql = ('SELECT home,away FROM fixtures' . $variable . ' WHERE id = ?');
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_id);

        // Set parameters
        $param_id = $matchid;
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $homeTeam, $awayTeam);
                if (mysqli_stmt_fetch($stmt)) {
                }
            }
        }
        mysqli_stmt_close($stmt);
    }

    if ($homeScore > $awayScore) {
        $homePoints = 3;
        $homeWins = 1;
        $homeLoses = 0;
        $awayWins = 0;
        $awayLoses = 1;
        $awayPoints = 0;
    } else {
        $homePoints = 0;
        $homeWins = 0;
        $homeLoses = 1;
        $awayPoints = 3;
        $awayLoses = 0;
        $awayWins = 1;
    }
    $sql = ('UPDATE league' . $variable . ' SET points = points + ?, wins = wins + ?, loses = loses + ? WHERE name = ?');
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "iiis", $param_points, $param_wins, $param_loses, $param_name);

        // Set parameters
        $param_points = $homePoints;
        $param_wins = $homeWins;
        $param_loses = $homeLoses;
        $param_name = $homeTeam;
        if (mysqli_stmt_execute($stmt)) {
        } else {
            echo "mas tam chybu bratre";
        }
        mysqli_stmt_close($stmt);
    }
    $sql = ('UPDATE league' . $variable . ' SET points = points + ?, wins = wins + ?, loses = loses + ? WHERE name = ?');
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "iiis", $param_points, $param_wins, $param_loses, $param_name);

        // Set parameters
        $param_points = $awayPoints;
        $param_wins = $awayWins;
        $param_loses = $awayLoses;
        $param_name = $awayTeam;
        if (mysqli_stmt_execute($stmt)) {
        } else {
            echo "mas tam chybu bratre";
        }
        mysqli_stmt_close($stmt);
    }
    $sql = ('UPDATE fixtures' . $variable . ' SET homeScore = ?, awayScore = ? WHERE id = ?');
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "sss", $param_homeScore, $param_awayScore, $param_id);

        // Set parameters
        $param_homeScore = $homeScore;
        $param_awayScore = $awayScore;
        $param_id = $matchid;
        if (mysqli_stmt_execute($stmt)) {
        } else {
            echo "mas tam chybu bratre";
        }
    }
    $pomocna = 0;
    $sql = "SELECT captain,player1,player2,player3,player4 FROM teams WHERE name = ?;";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_id);

        // Set parameters
        $param_id =  $homeTeam;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $teamcaptain, $teamplayer1, $teamplayer2, $teamplayer3, $teamplayer4);
            mysqli_stmt_fetch($stmt);
        } else {
            $send_err = "Stala se chyba, zkus to znovu";
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    $homeplayers[$pomocna++] = $teamcaptain;
    $homeplayers[$pomocna++] = $teamplayer1;
    $homeplayers[$pomocna++] = $teamplayer2;
    if (strlen($teamplayer3) > 1) {
        $homeplayers[$pomocna++] = $teamplayer3;
    }
    if (strlen($teamplayer4) > 1) {
        $homeplayers[$pomocna++] = $teamplayer4;
    }



    for ($i = 0; $i < count($homeplayers); $i++) {
        $sql = ('UPDATE stats' . $variable . ' SET goals = goals + ?, assists = assists + ?, saves = saves + ?, mvps = mvps + ?, points = points + ? WHERE name = ?');
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iiiiis", $param_goals, $param_assists, $param_saves, $param_mvps, $param_points, $param_name);

            // Set parameters
            $param_goals = $homegoals[$i];
            $param_assists = $homeassists[$i];
            $param_saves = $homesaves[$i];
            $param_mvps = $homemvps[$i];
            $param_points = (($homegoals[$i] * 2) + $homeassists[$i] + $homesaves[$i] + ($homemvps[$i] * 2));
            $param_name = $homeplayers[$i];;
            if (mysqli_stmt_execute($stmt)) {
            } else {
                echo "mas tam chybu bratre";
            }
            mysqli_stmt_close($stmt);
        }
    }
    $pomocna = 0;
    $sql = "SELECT captain,player1,player2,player3,player4 FROM teams WHERE name = ?;";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_id);

        // Set parameters
        $param_id =  $awayTeam;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $teamcaptain, $teamplayer1, $teamplayer2, $teamplayer3, $teamplayer4);
            mysqli_stmt_fetch($stmt);
        } else {
            $send_err = "Stala se chyba, zkus to znovu";
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    $awayplayers[$pomocna++] = $teamcaptain;
    $awayplayers[$pomocna++] = $teamplayer1;
    $awayplayers[$pomocna++] = $teamplayer2;
    if (strlen($teamplayer3) > 1) {
        $awayplayers[$pomocna++] = $teamplayer3;
    }
    if (strlen($teamplayer4) > 1) {
        $awayplayers[$pomocna++] = $teamplayer4;
    }

    for ($i = 0; $i < count($awayplayers); $i++) {
        $sql = ('UPDATE stats' . $variable . ' SET goals = goals + ?, assists = assists + ?, saves = saves + ?, mvps = mvps + ?, points = points + ? WHERE name = ?');
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iiiiis", $param_goals, $param_assists, $param_saves, $param_mvps, $param_points, $param_name);

            // Set parameters
            $param_goals = $awaygoals[$i];
            $param_assists = $awayassists[$i];
            $param_saves = $awaysaves[$i];
            $param_mvps = $awaymvps[$i];
            $param_points = (($awaygoals[$i] * 2) + $awayassists[$i] + $awaysaves[$i] + ($awaymvps[$i] * 2));
            $param_name = $awayplayers[$i];;
            if (mysqli_stmt_execute($stmt)) {
            } else {
                echo "mas tam chybu bratre";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
