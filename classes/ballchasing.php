<?php
class ballchasing
{
    
    public static function useApi($url, $type, $args = '', $decode = true) {   
        $token = 'jChIV7kuI63iaw0fcVpc2FxQLQJUWC8uaSk4U9kM';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        // 0 get 1 patch 2 post
        if ($type == 1) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        } elseif ($type == 2){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        } elseif ($type == 3){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $data = curl_exec($ch);
        //echo $data . "<br>"; //dobrý na debugování chyb api
        if ($decode == true) {
            $data = json_decode($data, true);
        }
        curl_close($ch);
        return $data;
    }

    public static function getIDSeries($id, $matchName) {        
        $series = Db::queryOne('SELECT BALLCHASING, COMPETITION_ANNUAL_ID FROM SERIES WHERE ID = ?', $id);
        //if (is_null($idSeries)) {  // nevím, proč u BC máme prázdný místo null
        if ($series['BALLCHASING']=='') {
            $idCompAnnual = self::getIDCompAnnual($series['COMPETITION_ANNUAL_ID']);
            $url = 'https://ballchasing.com/api/groups';
            $args = '{"name":"' . $matchName . '","parent":"' . $idCompAnnual . '","player_identification":"by-id","team_identification":"by-player-clusters"}';
            $decodedData = self::useApi($url, 2, $args);
            $idSeries = $decodedData['id'];
            Db::query("UPDATE SERIES SET BALLCHASING = ? WHERE ID = ?", $idSeries, $id);
        } else {
            $idSeries = $series['BALLCHASING'];
        }
        return $idSeries;
    }

    public static function getIDCompAnnual($id) {        
        $compAnnual = Db::queryOne('SELECT BALLCHASING, NAME FROM competition_annuals WHERE ID = ?', $id);
        //if (is_null($idCompAnnual)) {  // nevím, proč u BC máme prázdný místo null
        if ($compAnnual['BALLCHASING'] == '') {
            $url = 'https://ballchasing.com/api/groups';
            $args = '{"name":"' . $compAnnual['NAME'] . '","player_identification":"by-id","team_identification":"by-player-clusters"}';
            $decodedData = self::useApi($url, 2, $args);
            $idCompAnnual = $decodedData['id'];
            Db::query("UPDATE COMPETITION_ANNUALS SET BALLCHASING = ? WHERE ID = ?", $idCompAnnual, $id);
        } else {
            $idCompAnnual = $compAnnual['BALLCHASING'];    
        }
        return $idCompAnnual;
    }
}