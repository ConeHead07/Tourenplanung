<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 29.08.2019
 * Time: 14:56
 */
class MyProject_Response_Json {
    public static function send($anyData)
    {
        $json = json_encode( $anyData, JSON_PRETTY_PRINT);

        header('Content-Type: application/json; charset=UTF-8');
        header("Content-Length: ". strlen($json));
        echo $json;

        exit;
    }



    public static function sendError(string $error, array $data = []) {
        self::send(['error'=>$error, 'data' => $data]);
    }
}