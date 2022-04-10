<?php
echo $_POST["id"];
if (isset($_POST['link']) && $_POST['link'] != '') {
  //pokud mám link vytvořím si vlastní replay a naplním ho daty staženého replaye
  $myfile = fopen("tempfile.replay", "w");
  $url = 'https://ballchasing.com/api/replays/' . basename($_POST['link']) . '/file';
  $replay = Ballchasing::useApi($url, 0, '');
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
  //nahrávám replay
  $url = 'https://ballchasing.com/api/v2/upload?visibility=public';
  $decodedData = Ballchasing::useApiJson($url, 2, $args);
  if (isset($decodedData['error'])){
    //nahrání se nepovedlo
    echo "Replay už je uložený";
    echo '<br><a href="index.php?page=series&id=' . $_POST["id"] . '"><input type="submit" value = "Zpět"/></a>';
  } else {
    //zjistím ID replaye
    $replayId = $decodedData['id'];
    //smazat uložený dočasný replay
    if (isset($_POST['link'])) {
      unlink("tempfile.replay");
    }

    //najdu si pořadí zápasu a pojmenuji ho podle toho
    $orderOfMatch = Db::querySingle("SELECT COUNT(*) FROM MATCHES WHERE SERIES_ID = ?", $_POST["id"]) + 1;
    //zjistím id série na ballchasing
    $idBCSerie = Ballchasing::getIDSeries($_POST["id"],$_POST["matchname"]);
    $args = '{
        "title": "' . $_POST["matchname"] . ' ' . $orderOfMatch . '. zápas", "group": "' . $idBCSerie . '"
      }';
    $url = 'https://ballchasing.com/api/replays/' . $replayId;
    Ballchasing::useApiJson($url, 1, $args);
    header("location: index.php?page=match_ballchase&replay=" . $replayId . "&id=" . $_POST["id"]);
  }
}
else {
  echo $error;
  echo '<br><a href="index.php?page=series&id=' . $_POST["id"] . '"><input type="submit" value = "Zpět"/></a>';
}