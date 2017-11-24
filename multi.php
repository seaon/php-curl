<?php

// url resource
$url[] = 'https://www.baidu.com/';
$url[] = 'https://www.baidu.com/';
$url[] = 'https://www.baidu.com/';

// 创建批处理cURL句柄
$mh = curl_multi_init();

foreach ($url as $key => $value) {
    // 创建cURL资源
    $ch[$key] = curl_init($value);
    curl_setopt($ch[$key], CURLOPT_NOBODY, true);
    curl_setopt($ch[$key], CURLOPT_HEADER, true);
    curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, false);

    // 增加2个句柄
    curl_multi_add_handle($mh, $ch[$key]);
}

$active = null;
// 执行批处理句柄
do {
    $mrc = curl_multi_exec($mh, $active);
} while ($mrc == CURLM_CALL_MULTI_PERFORM);

while ($active && $mrc == CURLM_OK) {
    if (curl_multi_select($mh) == -1) {
        usleep(1);
    }
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
}


// 获取资源
$result = [];
foreach ($ch as $key => $value) {
    echo curl_getinfo($ch[$key], CURLINFO_HTTP_CODE);
    echo curl_getinfo($ch[$key], CURLINFO_EFFECTIVE_URL);

    $error = curl_error($ch[$key]);
    if (!empty($error)) {
        $result[$key]['code']  = $error;
        $result[$key]['msg']  = curl_strerror($ch[$key]);
        $result[$key]['data']  = '';
    } else {
        $result[$key]['data']  = curl_multi_getcontent($ch[$key]);  // get results
    }

    // 关闭全部句柄
    curl_multi_remove_handle($mh, $ch[$key]);
}

curl_multi_close($mh);
