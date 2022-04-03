<div class="text-center mt-5">
    <a href='index.php?page=competition_create'> Přidat competition </a>
</div>
<?php 
  $date = new DateTime(); //this returns the current date time
  $date->sub(new DateInterval('PT' . 4000 . 'M'));
  $time = $date->format('Y-m-d\TH:i:s\Z');
  $url = 'https://ballchasing.com/api/replays?uploader=76561198160695261&created-after=' . $time;//2022-04-01T00:46:54Z';
  $replay = Ballchasing::useApiJson($url, 0);
  foreach ($replay['list'] as $match) {
    echo $match['replay_title'] . '<br>';
  };
  ?>