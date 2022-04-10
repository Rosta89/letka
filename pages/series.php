<?php
$seriesID = $_GET['id'];
$result = Db::queryOne("SELECT s.*, th.NAME HOME_NAME, ta.NAME AWAY_NAME 
FROM series s 
JOIN teams th ON th.ID=s.HOME_TEAM 
JOIN teams ta ON ta.ID=s.AWAY_TEAM
WHERE s.ID = ?", $seriesID);
$matches = Db::queryAll("SELECT * FROM matches WHERE series_ID = ?", $seriesID);
if ($result) { ?>
    <div class="containerInput">
        <div class="text-center mt-5">
            <table class="content-table">
                <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="page" value="matches">
                    <input type="hidden" name="id" value="<?=$seriesID?>">
                    <thead>
                        <tr>
                            <th class="col-md-5">Team1</th>
                            <th class="col-md-2">Skóre</th>
                            <th class="col-md-5">Team2</th>
                            <th class="col-md-5">Ballchasing</th>
                            <th class="col-md-2">Smazat</th>
                        </tr>
                    </thead>
                    <tbody>                        
                        <tr>
                        <td><a href="index.php?page=team&id=<?=$result['HOME_TEAM']?>"><?=$result['HOME_NAME']?></a></td>
                        <?php if (is_null($result['HOME_SCORE'])) {
                            echo '<td>-:-</td>';
                        } else {
                            echo '<td>' . $result['HOME_SCORE'] . ':' . $result['AWAY_SCORE'] . '</td>';
                        }?>
                        <td><a href="index.php?page=team&id=<?=$result['AWAY_TEAM']?>"><?=$result['AWAY_NAME']?></a></td>
                        <?php if ($result['BALLCHASING']!='') {?>
                                <td><a href="https://ballchasing.com/group/<?=$result['BALLCHASING']?>">Odkaz</a></td>
                        <?php } else {
                                echo "<td>Neexistuje</td>";
                        }?>
                        <tr>
                        <?php foreach ($matches as $row) {?>
                            <tr>
                            <td><a href="index.php?page=team&id=<?=$result['HOME_TEAM']?>"><?=$result['HOME_NAME']?></a></td>
                            <td><a href="index.php?page=matches&id=<?=$seriesID?>&match_id=<?=$row['ID']?>"><?=$row['HOME_SCORE']?>:<?=$row['AWAY_SCORE']?></a></td>
                            <td><a href="index.php?page=team&id=<?=$result['AWAY_TEAM']?>"><?=$result['AWAY_NAME']?></a></td>
                            <?php if ($row['BALLCHASING']!='') {?>
                                <td><a href="https://ballchasing.com/replay/<?=$row['BALLCHASING']?>">Odkaz</a></td>
                            <?php } else {
                                echo "<td>Neexistuje</td>";
                            }?>
                            <td><a href="index.php?page=matches_delete&id=<?=$seriesID?>&match_id=<?=$row['ID']?>">smazat</a></td>
                            <tr>
                        <?php
                        }
                        ?>     
                    </tbody></table>                
                    <div class=" form-group mt-2">
                        <input type="submit" class="btn btn-primary" value="Přidat zápas">
                    </div>
                </form>
                <form action="index.php?page=match_upload" method="post" enctype="multipart/form-data">
                    Odkaz na replay:
                    <input type="text" name="link">
                    <input type="hidden" name="id" value="<?=$seriesID?>">
                    <input type="hidden" name="matchname" value="<?php echo $result['HOME_NAME'] . ' - ' . $result['AWAY_NAME'] ?>">
                    <input type="submit" value="Upload Replay" name="submit">
                </form>
                <form action="index.php?page=match_upload" method="post" enctype="multipart/form-data">
                    Vyber replay:
                    <input type="file" name="replay">
                    <input type="hidden" name="id" value="<?=$seriesID?>">
                    <input type="hidden" name="matchname" value="<?php echo $result['HOME_NAME'] . ' - ' . $result['AWAY_NAME'] ?>">
                    <input type="submit" value="Upload Replay" name="submit">
                </form>
        </div>
    </div>
<?php } 
