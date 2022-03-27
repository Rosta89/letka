<?php
$series_id = $_GET["id"];
$replay = $_GET["replay"];
$url = 'https://ballchasing.com/api/replays/' . $replay;
$decodedData = ballchasing::getApi($url);
if ($decodedData['status'] == 'ok') {    
    $colors[0] = 'blue';
    $colors[1] = 'orange';
    $match_id = DB::getLastId(Db::query(
        'INSERT INTO matches (SERIES_ID,HOME_SCORE,AWAY_SCORE) VALUES (?,?,?)',
        $series_id,
        $decodedData[$colors[0]]['stats']['core']['goals'],
        $decodedData[$colors[1]]['stats']['core']['goals']
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
            Db::query(
                'INSERT INTO statistics (PLAYER_ID,MATCH_ID,GOALS,ASSISTS,SAVES,MVPS,TEAM_ORDER,HOME) VALUES (?,?,?,?,?,?,?,?)',
                $player_id,
                $match_id,
                $decodedData[$colors[$j]]['players'][$i]['stats']['core']['goals'],
                $decodedData[$colors[$j]]['players'][$i]['stats']['core']['assists'],
                $decodedData[$colors[$j]]['players'][$i]['stats']['core']['saves'],
                $mvp,
                $i,
                $team
            );
        }
    }
    Db::query(
        'UPDATE matches SET HOME_SCORE=?,AWAY_SCORE = ? WHERE SERIES_ID = ?',
        $series_id,
        $decodedData[$colors[1 - $team]]['stats']['core']['goals'],
        $decodedData[$colors[$team]]['stats']['core']['goals']
    );
    Db::query('UPDATE SERIES se, 
    (SELECT SUM(CASE WHEN HOME_SCORE>AWAY_SCORE THEN 1 ELSE 0 END) home,
    SUM(CASE WHEN HOME_SCORE<AWAY_SCORE THEN 1 ELSE 0 END) away,SERIES_ID
    FROM MATCHES GROUP BY SERIES_ID) ma SET se.HOME_SCORE = home,se.AWAY_SCORE = away 
    WHERE ma.SERIES_ID = se.ID AND se.ID = ?', $series_id);
    header("location: index.php?page=series&id=" . $series_id);
}
elseif ($decodedData['status'] == 'pending')
{
    echo "Čeká se na zpracování (stránka se automaticky refreshne za 10s)";
    echo '<meta http-equiv="refresh" content="10" >';

    echo "<br /><center><input type='submit' name='submitAdd' value='Refresh' onclick='window.location.reload(true);'></center>";

}
else {
    echo "Něco se posralo";
    echo $decodedData['status'];
}
