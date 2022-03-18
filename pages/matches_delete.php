<?php $series_id = $_GET['id'];
$match_id = $_GET['match_id'];
Db::query('DELETE FROM matches WHERE ID = ?', $match_id);
Db::query('DELETE FROM statistics WHERE MATCH_ID = ?', $match_id);
if (Db::queryAll('SELECT * FROM MATCHES WHERE SERIES_ID = ?', $series_id)) {
    Db::query('UPDATE SERIES se, 
    (SELECT 
    SUM(CASE WHEN HOME_SCORE>AWAY_SCORE THEN 1 ELSE 0 END) home,
    SUM(CASE WHEN HOME_SCORE<AWAY_SCORE THEN 1 ELSE 0 END) away,
    SERIES_ID
    FROM MATCHES GROUP BY SERIES_ID) ma SET se.HOME_SCORE = home,se.AWAY_SCORE = away 
    WHERE ma.SERIES_ID = se.ID AND se.ID = ?', $series_id);
} else {
    Db::query('UPDATE SERIES SET HOME_SCORE = NULL,AWAY_SCORE = NULL WHERE ID = ?', $series_id);
}

header("location: index.php?page=series&id=" . $series_id);
