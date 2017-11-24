<?php

namespace Sio;

class Tool
{
    const SYSTEM = 0;

    public static function returnJson(int $code, string $msg, $data = [], string $level = '')
    {
        $data = ['code' => $code, 'msg' => $msg, 'data' => $data];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $json;
    }
}
