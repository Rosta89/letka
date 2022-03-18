<?php
$token = 'jChIV7kuI63iaw0fcVpc2FxQLQJUWC8uaSk4U9kM';
$args['file'] = curl_file_create($_FILES['replay']['tmp_name'], $_FILES['replay']['type'], basename($_FILES['replay']['name'])); 

$header = array('Authorization: ' . $token);
$url = 'https://ballchasing.com/api/v2/upload?visibility=public';
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
$decodedData = json_decode(curl_exec($ch), true);
curl_close($ch);
$replay_id = basename($decodedData['location']);

$data = '{
    "title": "' . $_POST["matchname"] . '"
  }';
$url = 'https://ballchasing.com/api/replays/' . $replay_id;
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_URL, $url);
curl_exec($ch);
curl_close($ch);
header("location: index.php?page=match_ballchase&replay=" . $replay_id . "&id=" . $_POST["id"]);