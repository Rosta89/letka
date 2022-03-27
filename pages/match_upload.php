<?php
if (isset($_POST['link']) && $_POST['link'] != '') {
  //pokud mám link vytvořím si vlastní replay a naplním ho daty staženého replaye
  $myfile = fopen("tempfile.replay", "w");
  $url = 'https://ballchasing.com/api/replays/' . basename($_POST['link']) . '/file';
  $replay = ballchasing::useApi($url, 0, '', false);
  fwrite($myfile, $replay);
  $args['file'] = curl_file_create('tempfile.replay', 'application/octet-stream', 'tempfile.replay');
}
elseif (isset($_FILES['replay']) && file_exists($_FILES['replay']['tmp_name'])) {
  $args['file'] = curl_file_create($_FILES['replay']['tmp_name'], $_FILES['replay']['type'], basename($_FILES['replay']['name']));
}
else {
  $error = "Nenahraný replay";
}
if (!isset($error)){
  $url = 'https://ballchasing.com/api/v2/upload?visibility=public';
  $decodedData = ballchasing::useApi($url, 2, $args);
  if (isset($decodedData['error'])){
    echo "Replay už je uložený";
    echo '<br><a href="index.php?page=series&id=' . $_POST["id"] . '><input type="submit"/>Zpět</a>';
  } else {  
    $replay_id = $decodedData['id'];

    //smazat uložený dočasný replay
    if (isset($_POST['link'])) {
      unlink("tempfile.replay");
    }

    $orderOfMatch = Db::querySingle("SELECT COUNT(*) FROM MATCHES WHERE SERIES_ID = ?", $_POST["id"]) + 1;
    $idBCSerie = ballchasing::getIDSeries($_POST["id"],$_POST["matchname"]);
    $args = '{
        "title": "' . $_POST["matchname"] . ' ' . $orderOfMatch . '. zápas", "group": "' . $idBCSerie . '"
      }';
    $url = 'https://ballchasing.com/api/replays/' . $replay_id;
    ballchasing::useApi($url, 1, $args);
    header("location: index.php?page=match_ballchase&replay=" . $replay_id . "&id=" . $_POST["id"]);
  }
}
else {
  echo $error;
  echo '<br><a href="index.php?page=series&id=' . $_POST["id"] . '><input type="submit"/>Zpět</a>';
}