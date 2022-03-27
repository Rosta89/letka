<?php
$args['file'] = curl_file_create($_FILES['replay']['tmp_name'], $_FILES['replay']['type'], basename($_FILES['replay']['name']));

$url = 'https://ballchasing.com/api/v2/upload?visibility=public';
$decodedData = ballchasing::postApi($url, $args);

$replay_id = basename($decodedData['location']);
$pocet = Db::querySingle("SELECT COUNT(*) FROM MATCHES WHERE SERIES_ID = ?", $_POST["id"]) + 1;
$data = '{
    "title": "' . $_POST["matchname"] . ' ' . $pocet . '. zápas"
  }';
$url = 'https://ballchasing.com/api/replays/' . $replay_id;
ballchasing::patchApi($url, $data);
header("location: index.php?page=match_ballchase&replay=" . $replay_id . "&id=" . $_POST["id"]);
