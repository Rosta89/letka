<?php
class Ballchasing
{           
    public static function useApi($url, $type, $args = '', $decode = true) {   
        $token = 'jChIV7kuI63iaw0fcVpc2FxQLQJUWC8uaSk4U9kM';
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conn, CURLOPT_URL, $url);
        // 0 get 1 patch 2 post
        if ($type == 1) {
            curl_setopt($conn, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($conn, CURLOPT_POSTFIELDS, $args);
        } elseif ($type == 2){
            curl_setopt($conn, CURLOPT_POST, true);
            curl_setopt($conn, CURLOPT_POSTFIELDS, $args);
        } elseif ($type == 3){
            curl_setopt($conn, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $data = curl_exec($conn);
        //echo $data . "<br>"; //dobrý na debugování chyb api
        if ($decode == true) {
            $data = json_decode($data, true);
        }
        curl_close($conn);
        return $data;
    }

    public static function getIDSeries($seriesID, $matchName) {        
        $series = Db::queryOne('SELECT BALLCHASING, COMPETITION_ANNUAL_ID FROM SERIES WHERE ID = ?', $seriesID);
        //if (is_null($idSeries)) {  // nevím, proč u BC máme prázdný místo null
        if ($series['BALLCHASING']=='') {
            $idCompAnnual = self::getIDCompAnnual($series['COMPETITION_ANNUAL_ID']);
            $url = 'https://ballchasing.com/api/groups';
            $args = '{"name":"' . $matchName . '","parent":"' . $idCompAnnual . '","player_identification":"by-id","team_identification":"by-player-clusters"}';
            $decodedData = self::useApi($url, 2, $args);
            $idSeries = $decodedData['id'];
            Db::query("UPDATE SERIES SET BALLCHASING = ? WHERE ID = ?", $idSeries, $seriesID);
            return $idSeries;
        }    
        return $series['BALLCHASING'];
    }

    public static function getIDCompAnnual($compID) {        
        $compAnnual = Db::queryOne('SELECT BALLCHASING, NAME FROM competition_annuals WHERE ID = ?', $compID);
        //if (is_null($idCompAnnual)) {  // nevím, proč u BC máme prázdný místo null
        if ($compAnnual['BALLCHASING'] == '') {
            $url = 'https://ballchasing.com/api/groups';
            $args = '{"name":"' . $compAnnual['NAME'] . '","player_identification":"by-id","team_identification":"by-player-clusters"}';
            $decodedData = self::useApi($url, 2, $args);
            $idCompAnnual = $decodedData['id'];
            Db::query("UPDATE COMPETITION_ANNUALS SET BALLCHASING = ? WHERE ID = ?", $decodedData['id'], $compID);
            return $decodedData['id'];    
        }
        return $idCompAnnual;
    }
}