<?php
function backtrace()
{
    $backtrace = debug_backtrace();
    $backtrace = array_shift($backtrace);
    return json_encode($backtrace);
}
function curl_get($uri, $req = NULL)
{
    $str = '';
    foreach ($req as $key => $value) {
        $str .= $key . '=' . $value . '&';
    }
    $req = trim($str, '&');
    $url = $uri . '?' . $req;
    $cuh = curl_init();
    curl_setopt($cuh, CURLOPT_URL, $url);
    curl_setopt($cuh, CURLOPT_HEADER, 0);
    curl_setopt($cuh, CURLOPT_TIMEOUT, 90);
    curl_setopt($cuh, CURLOPT_RETURNTRANSFER, 1);
    $rsp = curl_exec($cuh);
    if ($rsp === false) {
        $error = curl_error($cuh);
        $errno = curl_errno($cuh);
        curl_close($cuh);
        RedisLog::info($errno . $error);
    } else {
        curl_close($cuh);
    }
    $rsp = json_decode($rsp, true);
    return $rsp;
}

function curl_post($uri, $req)
{
    $url = $uri;

    $cuh = curl_init();
    curl_setopt($cuh, CURLOPT_URL, $url);
    curl_setopt($cuh, CURLOPT_HEADER, 0);
    curl_setopt($cuh, CURLOPT_TIMEOUT, 90);
    curl_setopt($cuh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($cuh, CURLOPT_POST, 1);
    curl_setopt($cuh, CURLOPT_POSTFIELDS, $req);
    $rsp = curl_exec($cuh);


    if ($rsp === false) {
        $error = curl_error($cuh);
        $errno = curl_errno($cuh);
        curl_close($cuh);
        \think\custom\common\RedisLog::info($errno . $error);
    } else {
        curl_close($cuh);
    }
    $rsp = json_decode($rsp, true);
    return $rsp;
}
