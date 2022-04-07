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



    public static function sendDebug(string $msg, array $data = [])
    {
        try { throw new Exception('DEBUG'); } catch(Exception $e) { $stackTrace = $e->getTrace(); }
        self::send([
            'msg'=>$msg,
            'data' => $data,
            'debugData' => [
                'stackTrace' => $stackTrace,
                'queries' => MyProject_Db_Profiler::getProfiledQueryList(),
                '_POST' => $_POST,
                '_GET' => $_GET,
                '_COOKIE' => $_COOKIE,
                '_FILES' => $_FILES,
            ],
        ]);
    }
}