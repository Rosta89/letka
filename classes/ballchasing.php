<?php
class ballchasing
{
    
    public static function getApi($url) {        
        $ch = self::setCurl($url);
        $decodedData = json_decode(curl_exec($ch), true);
        //$decodedData = curl_exec($ch);
        curl_close($ch);
        return $decodedData;
    }

    public static function patchApi($url, $data) {        
        $ch = self::setCurl($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
        curl_close($ch);
    }

    public static function postApi($url, $args) {        
        $ch = self::setCurl($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $decodedData = json_decode(curl_exec($ch), true);
        //$decodedData = curl_exec($ch);
        curl_close($ch);
        return $decodedData;
    }

    private static function setCurl($url) {
        $token = 'jChIV7kuI63iaw0fcVpc2FxQLQJUWC8uaSk4U9kM';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        return $ch;
    }
}