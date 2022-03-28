<?php
$playerID = $_GET['id'];
$PlayerName = Db::querySingle("SELECT NAME from players WHERE ID = ?", $playerID);
echo $PlayerName;
$teams = Db::queryAll("SELECT t.name, t.id from players_2_teams pt
JOIN teams t ON t.ID = pt.TEAM_ID
where pt.PLAYER_ID = ?", $playerID);

if ($teams) {
    foreach ($teams as $team) {
        echo ('<p><a href="index.php?page=team&id=' . $team['id'] . '">' . $team['name'] .  '</a></p>');
        $competitions = Db::queryAll("SELECT ca.name, tca.COMPETITION_ANNUAL_ID,
        COUNT(*) MATCHES,
        SUM(GOALS) GOALS,
        SUM(ASSISTS) ASSISTS,
        SUM(SAVES) SAVES,
        SUM(MVPS) MVPS,
        SUM(2*GOALS+ASSISTS+SAVES+2*MVPS) POINTS       
        from teams_2_competition_annuals tca
        JOIN competition_annuals ca ON ca.ID = tca.COMPETITION_ANNUAL_ID
        JOIN series se ON se.COMPETITION_ANNUAL_ID = tca.COMPETITION_ANNUAL_ID
        JOIN matches ma ON ma.SERIES_ID = se.ID
        JOIN statistics s ON s.MATCH_ID = ma.ID
        where tca.TEAM_ID = ? AND s.PLAYER_ID = ?
        GROUP BY ca.NAME, tca.COMPETITION_ANNUAL_ID", $team['id'], $playerID);
        foreach ($competitions as $competition) {
            echo ('<p><a href="index.php?page=table&id=' . $competition['COMPETITION_ANNUAL_ID'] . '">' . $competition['name'] .  '</a> Počet bodů : ' . $competition['POINTS'] . '</p>');
        }
    }
}
