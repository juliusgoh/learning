<?php
$pair = "BTCUSDT";
$krakenAPIUrl = "https://api.kraken.com/0/public/AssetPairs?pair=$pair";
$liquidAPIURL = "https://api.liquid.com/products";

$krakenResult = httpCurl('get',array(),$krakenAPIUrl);
$krakenRes = json_decode($krakenResult['response']);
$liquidResult = httpCurl('get',array(),$liquidAPIURL);
$liquidRes = json_decode($liquidResult['response']);
$krakenKeyPair = $krakenRes->result;
$liquidKeyPair = array_filter($liquidRes, function($x ) use($pair){
    if($x->currency_pair_code == $pair)
    {
        return true;
    }
});
echo "<pre>";
print_r($liquidKeyPair);
print_r($krakenKeyPair);
function httpCurl($type, $fields, $url)
{ 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

    switch ($type)
    {
        case "post":
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            break;
        case "get":
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "delete":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        default:
            break;
    }

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return array("httpcode" => (string)$httpcode, "response" => $response);
}
