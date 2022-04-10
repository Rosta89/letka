<?php
$goals = $_POST['goals'];
$players_id = $_POST['players_id'];
$assists = $_POST['assists'];
$saves = $_POST['saves'];
$mvp = $_POST['mvp'];
if (isset($_POST['match_id'])) {
    $match_id = $_POST['match_id'];
    Db::update(
        'matches',
        array(
            'HOME_SCORE' => $_POST["homeGoals"],
            'AWAY_SCORE' =>  $_POST["awayGoals"]
        ),
        'WHERE ID = ' . $match_id . ''
    );

    for ($i = 0; $i < count($players_id); $i++) {
        Db::update(
            'statistics',
            array(
                'PLAYER_ID' => $players_id[$i],
                'GOALS' => $goals[$i],
                'ASSISTS' => $assists[$i],
                'SAVES' => $saves[$i],
                'MVPS' => $mvp[$i]
            ),
            ' WHERE TEAM_ORDER = ' . $_POST['team_order'][$i] . ' AND HOME = ' . $_POST['home_team'][$i] . ' AND MATCH_ID = ' . $match_id . ''
        );
    }
} else {
    $match_id = Db::getLastId(Db::insert(
        'matches',
        array(
            'SERIES_ID' => $_POST["series_id"],
            'HOME_SCORE' => $_POST["homeGoals"],
            'AWAY_SCORE' => $_POST["awayGoals"]
        )
    ));
    for ($i = 0; $i < count($players_id); $i++) {
        Db::insert(
            'statistics',
            array(
                'PLAYER_ID' => $players_id[$i],
                'MATCH_ID' => $match_id,
                'GOALS' => $goals[$i],
                'ASSISTS' => $assists[$i],
                'SAVES' => $saves[$i],
                'MVPS' => $mvp[$i],
                'TEAM_ORDER' =>  $_POST['team_order'][$i],
                'HOME' => $_POST['home_team'][$i]
            )
        );
    }
}
Db::update('SERIES', array(
    'HOME_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE>AWAY_SCORE AND SERIES_ID = ' .  $_POST["series_id"] . ''),
    'AWAY_SCORE' => Db::query('SELECT * from MATCHES where HOME_SCORE<AWAY_SCORE AND SERIES_ID = ' .  $_POST["series_id"] . ''),
), 'WHERE ID = ' .  $_POST["series_id"] . '');
header("location: index.php?page=series&id=" . $_POST["series_id"]);
