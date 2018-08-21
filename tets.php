<?php
include './src/Curl.php';

$ch = new \Sio\Curl();

$UA = [
    'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:53.0) Gecko/20100101 Firefox/53.0',
    'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
    'Mozilla/5.0 (MSIE 9.0; qdesk 2.4.1266.203; Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.114 Safari/537.36 Vivaldi/1.9.818.50',
];

$result = $ch->url('http://tp3.com/')
    ->header(['Content-Type: application/json'])
    ->data(['a'=>123])
    ->post();

var_dump($result['data']);
exit();