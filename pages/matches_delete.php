<?php $series_id = $_GET['id'];
$match_id = $_GET['match_id'];
$ballchasingId = Db::querySingle('SELECT BALLCHASING FROM matches WHERE ID = ?', $match_id);
if ($ballchasingId != '') {
    $url = 'https://ballchasing.com/api/replays/' . $ballchasingId;
    Ballchasing::useApi($url, 3);
}
Db::query('DELETE FROM matches WHERE ID = ?', $match_id);
Db::query('DELETE FROM statistics WHERE MATCH_ID = ?', $match_id);
if (Db::queryAll('SELECT * FROM MATCHES WHERE SERIES_ID = ?', $series_id)) {
    Db::update('SERIES', array(
        'HOME_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE>AWAY_SCORE AND SERIES_ID = ' .  $series_id . ''),
        'AWAY_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE<AWAY_SCORE AND SERIES_ID = ' .  $series_id . ''),
    ), 'WHERE ID = ' .  $series_id . '');
} else {
    Db::update('SERIES', array(
        'HOME_SCORE' => NULL,
        'AWAY_SCORE' => NULL
    ), 'WHERE ID = ' . $series_id . '');
}

header("location: index.php?page=series&id=" . $series_id);
