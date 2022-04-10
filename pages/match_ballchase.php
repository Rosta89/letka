<?php
$seriesId = $_GET["id"];
$replay = $_GET["replay"];
$url = 'https://ballchasing.com/api/replays/' . $replay;
$decodedData = Ballchasing::useApiJson($url, 0);
if ($decodedData['status'] == 'ok') {
    //nadefinuji barvy, abych to mohl jet v cyklu, mají je stejný, i když týmy pak mají vybraný jiný...
    $colors[0] = 'blue';
    $colors[1] = 'orange';
    for ($j = 0; $j < 2; $j++) {
        for ($i = 0; $i < count($decodedData[$colors[$j]]['players']); $i++) {
            //najdu hráče u nás
            $playerId[] =  Db::querySingle(
                'SELECT ID FROM PLAYERS WHERE ' . $decodedData[$colors[$j]]['players'][$i]['id']['platform'] . '  = ?'
                , $decodedData[$colors[$j]]['players'][$i]['id']['id']);
            //pokud není vyhodí u nás, vyhodí to pak chybu
            if (is_null(end($playerId))) {
                $errors[] = 'Hráč ' . $decodedData[$colors[$j]]['players'][$i]['name'] . ' není v databázi';
            }
            //najde jestli je hráč domácí nebo host (1 domácí, 0 host)
            $team[] = Db::querySingle('SELECT CASE WHEN se.HOME_TEAM = plt.TEAM_ID THEN 1 ELSE 0 END HOME
            FROM PLAYERS_2_TEAMS plt 
            JOIN SERIES se ON (se.HOME_TEAM = plt.TEAM_ID OR se.AWAY_TEAM = plt.TEAM_ID) 
            WHERE plt.PLAYER_ID = ? AND se.ID = ?', end($playerId), $seriesId);
            //pokud není ani u jednoho týmu, tak to dá null a hodí to pak chybu
            if (is_null(end($team))) {
                $errors[] = 'Hráč ' . $decodedData[$colors[$j]]['players'][$i]['name'] . ' není součástí žádného z týmů';
            }
            $goals[] = $decodedData[$colors[$j]]['players'][$i]['stats']['core']['goals'];
            $assists[] = $decodedData[$colors[$j]]['players'][$i]['stats']['core']['assists'];
            $saves[] = $decodedData[$colors[$j]]['players'][$i]['stats']['core']['saves'];
            if ($decodedData[$colors[$j]]['players'][$i]['stats']['core']['mvp'] == 'true') {
                $mvp[] = 1;
            } else {
                $mvp[] = 0;
            }
        }
    }
    //vypisování chyb
    if (isset($errors)) {
        foreach ($errors as $error) {
            echo $error . '<br>';
            $url = 'https://ballchasing.com/api/replays/' . $replay;
            Ballchasing::useApi($url, 3);
        }
        echo '<br><a href="index.php?page=series&id=' . $seriesId . '"><input type="submit" value = "Zpět"/></a>';
    }
    else {        
    Db::beginTransaction();
    //vytvořím zápas, pořadí dávám podle prvního hráče, ve většině případů bude snad fungovat...
    $matchId = Db::getLastId(Db::insert(
        'matches',
        array(
            'SERIES_ID' => $seriesId,
            'HOME_SCORE' => $decodedData[$colors[1-$team[0]]]['stats']['core']['goals'],
            'AWAY_SCORE' => $decodedData[$colors[$team[0]]]['stats']['core']['goals'],
            'BALLCHASING' => $_GET["replay"]
        )
    ));
    //inserty do statistik
    for ($i = 0; $i < count($playerId); $i++) {
        //hledám podle už uložených hráčů z týmu
        $teamOrder = Db::querySingle('SELECT COUNT(*) FROM STATISTICS WHERE MATCH_ID = ? AND HOME = ?', $matchId, $team[$i]);
        Db::insert(
            'statistics',
            array(
                'PLAYER_ID' => $playerId[$i],
                'MATCH_ID' => $matchId,
                'GOALS' => $goals[$i],
                'ASSISTS' => $assists[$i],
                'SAVES' => $saves[$i],
                'MVPS' => $mvp[$i],
                'TEAM_ORDER' => $teamOrder,
                'HOME' => $team[$i]
            )
        );
    }
    //uložení skóre série
    Db::update('SERIES', 
        array(
        'HOME_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE>AWAY_SCORE AND SERIES_ID = ' . $seriesId . ''),
        'AWAY_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE<AWAY_SCORE AND SERIES_ID = ' . $seriesId . ''),
    ), 'WHERE ID = ' . $seriesId);
    Db::commitTransaction();
    header("location: index.php?page=series&id=" . $seriesId);
    }
} elseif ($decodedData['status'] == 'pending') {
    echo "Čeká se na zpracování (stránka se automaticky refreshne za 3s)";
    echo '<meta http-equiv="refresh" content="3" >';
    echo "<br /><center><input type='submit' name='submitAdd' value='Refresh' onclick='window.location.reload(true);'></center>";
} else {
    echo "Něco se posralo";
    echo $decodedData['status'];
}
