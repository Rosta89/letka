<?php
$series_id = $_GET["id"];
$replay = $_GET["replay"];
$url = 'https://ballchasing.com/api/replays/' . $replay;
$decodedData = Ballchasing::useApiJson($url, 0);
if ($decodedData['status'] == 'ok') {
    Db::beginTransaction();
    $colors[0] = 'blue';
    $colors[1] = 'orange';
    $match_id = Db::getLastId(Db::insert(
        'matches',
        array(
            'SERIES_ID' => $series_id,
            'HOME_SCORE' => $decodedData[$colors[0]]['stats']['core']['goals'],
            'AWAY_SCORE' => $decodedData[$colors[1]]['stats']['core']['goals'],
            'BALLCHASING' => $_GET["replay"]
        )
    ));

    for ($j = 0; $j < 2; $j++) {
        for ($i = 0; $i < 3; $i++) {
            $player_id =  Db::queryOne('SELECT ID FROM PLAYERS WHERE ' . $decodedData[$colors[$j]]['players'][$i]['id']['platform'] . '  = ?', $decodedData[$colors[$j]]['players'][$i]['id']['id'])['ID'];
            $team = Db::queryOne('SELECT CASE WHEN se.HOME_TEAM = plt.TEAM_ID THEN 1 ELSE 0 END HOME
            FROM PLAYERS_2_TEAMS plt 
            JOIN SERIES se ON (se.HOME_TEAM = plt.TEAM_ID OR se.AWAY_TEAM = plt.TEAM_ID) 
            WHERE plt.PLAYER_ID = ? AND se.ID = ?', $player_id, $series_id)['HOME'];
            if ($decodedData[$colors[$j]]['players'][$i]['stats']['core']['mvp'] == 'true') {
                $mvp = 1;
            } else {
                $mvp = 0;
            }
            Db::insert(
                'statistics',
                array(
                    'PLAYER_ID' => $player_id,
                    'MATCH_ID' => $match_id,
                    'GOALS' => $decodedData[$colors[$j]]['players'][$i]['stats']['core']['goals'],
                    'ASSISTS' => $decodedData[$colors[$j]]['players'][$i]['stats']['core']['assists'],
                    'SAVES' => $decodedData[$colors[$j]]['players'][$i]['stats']['core']['saves'],
                    'MVPS' => $mvp,
                    'TEAM_ORDER' => $i,
                    'HOME' => $team
                )
            );
        }
    }
    Db::update('SERIES', array(
        'HOME_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE>AWAY_SCORE AND SERIES_ID = ' . $series_id . ''),
        'AWAY_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE<AWAY_SCORE AND SERIES_ID = ' . $series_id . ''),
    ), 'WHERE ID = ' . $series_id . '');
    Db::commitTransaction();
    header("location: index.php?page=series&id=" . $series_id);
} elseif ($decodedData['status'] == 'pending') {
    echo "Čeká se na zpracování (stránka se automaticky refreshne za 3s)";
    echo '<meta http-equiv="refresh" content="3" >';

    echo "<br /><center><input type='submit' name='submitAdd' value='Refresh' onclick='window.location.reload(true);'></center>";
} else {
    echo "Něco se posralo";
    echo $decodedData['status'];
}
