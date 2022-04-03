<?php
$goals = $_POST['goals'];
$players_id = $_POST['players_id'];
$assists = $_POST['assists'];
$saves = $_POST['saves'];
$mvp = $_POST['mvp'];
if (isset($_POST['match_id'])) {
    $match_id = $_POST['match_id'];
    Db::query('UPDATE matches SET HOME_SCORE=?,AWAY_SCORE=? WHERE ID = ?', $_POST["homeGoals"], $_POST["awayGoals"], $match_id);
    for ($i = 0; $i < count($players_id); $i++) {
        Db::query(
            'UPDATE statistics SET PLAYER_ID=?,GOALS=?,ASSISTS=?,SAVES=?,MVPS=? WHERE TEAM_ORDER = ? AND HOME = ? AND MATCH_ID = ?',
            $players_id[$i],
            $goals[$i],
            $assists[$i],
            $saves[$i],
            $mvp[$i],
            $_POST['team_order'][$i],
            $_POST['home_team'][$i],
            $match_id
        );
    }
} else {
    $match_id = Db::getLastId(Db::query('INSERT INTO matches (SERIES_ID,HOME_SCORE,AWAY_SCORE) VALUES (?,?,?)', $_POST["series_id"], $_POST["homeGoals"], $_POST["awayGoals"]));
    for ($i = 0; $i < count($players_id); $i++) {
        Db::query(
            'INSERT INTO statistics (PLAYER_ID,MATCH_ID,GOALS,ASSISTS,SAVES,MVPS,TEAM_ORDER,HOME) VALUES (?,?,?,?,?,?,?,?)',
            $players_id[$i],
            $match_id,
            $goals[$i],
            $assists[$i],
            $saves[$i],
            $mvp[$i],
            $_POST['team_order'][$i],
            $_POST['home_team'][$i]
        );
    }
}
Db::query('UPDATE SERIES se, 
(SELECT SUM(CASE WHEN HOME_SCORE>AWAY_SCORE THEN 1 ELSE 0 END) home,
SUM(CASE WHEN HOME_SCORE<AWAY_SCORE THEN 1 ELSE 0 END) away,SERIES_ID
FROM MATCHES GROUP BY SERIES_ID) ma SET se.HOME_SCORE = home,se.AWAY_SCORE = away 
WHERE ma.SERIES_ID = se.ID AND se.ID = ?', $_POST["series_id"]);
header("location: index.php?page=series&id=" . $_POST["series_id"]);
