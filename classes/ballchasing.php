<?php
class Ballchasing
{
    public static function useApi($url, $type, $args = '')
    {
        $token = 'jChIV7kuI63iaw0fcVpc2FxQLQJUWC8uaSk4U9kM';
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conn, CURLOPT_URL, $url);
        // 0 get 1 patch 2 post 3 delete
        if ($type == 1) {
            curl_setopt($conn, CURLOPT_CUSTOMREQUEST, 'PATCH');
        } elseif ($type == 2) {
            curl_setopt($conn, CURLOPT_POST, true);
        } elseif ($type == 3) {
            curl_setopt($conn, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        if ($args != '') {
            curl_setopt($conn, CURLOPT_POSTFIELDS, $args);
        }
        $data = curl_exec($conn);
        curl_close($conn);
        return $data;
    }

    public static function useApiJson($url, $type, $args = '')
    {
        $data = self::useApi($url, $type, $args);
        //echo $data . "<br>"; //dobrý na debugování chyb api
        return json_decode($data, true);
    }

    public static function getIDSeries($seriesID, $matchName)
    {
        $series = Db::queryOne('SELECT BALLCHASING, COMPETITION_ANNUAL_ID FROM SERIES WHERE ID = ?', $seriesID);
        //if (is_null($series['BALLCHASING'])) {  // nevím, proč u BC máme prázdný místo null
        if ($series['BALLCHASING'] == '') {
            $idCompAnnual = self::getIDCompAnnual($series['COMPETITION_ANNUAL_ID']);
            $url = 'https://ballchasing.com/api/groups';
            $args = '{"name":"' . $matchName . '","parent":"' . $idCompAnnual . '","player_identification":"by-id","team_identification":"by-player-clusters"}';
            $decodedData = self::useApiJson($url, 2, $args);
            Db::update('series', array(
                'BALLCHASING' => $decodedData['id']
            ), 'WHERE ID = ' . $seriesID . '');
            return $decodedData['id'];
        }
        return $series['BALLCHASING'];
    }

    public static function getIDCompAnnual($compID)
    {
        $compAnnual = Db::queryOne('SELECT BALLCHASING, NAME FROM competition_annuals WHERE ID = ?', $compID);
        //if (is_null($compAnnual['BALLCHASING'])) {  // nevím, proč u BC máme prázdný místo null
        if ($compAnnual['BALLCHASING'] == '') {
            $url = 'https://ballchasing.com/api/groups';
            $args = '{"name":"' . $compAnnual['NAME'] . '","player_identification":"by-id","team_identification":"by-player-clusters"}';
            $decodedData = self::useApiJson($url, 2, $args);
            Db::update('COMPETITION_ANNUALS', array(
                'BALLCHASING' => $decodedData['id']
            ), 'WHERE ID = ' . $compID . '');
            return $decodedData['id'];
        }
        return $compAnnual['BALLCHASING'];
    }
}
