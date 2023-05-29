<?php

$token_key = get_option('wfic_api_key_fattureincloud');

$curl_wfic = curl_init();

curl_setopt($curl_wfic, CURLOPT_URL, $url);
curl_setopt($curl_wfic, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_wfic, CURLOPT_HTTPGET, true);
curl_setopt($curl_wfic, CURLOPT_HTTPHEADER, array(
    'Accept: application/vnd.api+json',
    'Content-Type: application/vnd.api+json',
    'Authorization: Bearer ' . $token_key . '',
));

$result = curl_exec($curl_wfic);

curl_close($curl_wfic);

if (!$result) {
    die("Connection Failure");
}