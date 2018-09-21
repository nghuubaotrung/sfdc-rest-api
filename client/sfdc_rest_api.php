<?php

//require_once("/home/ec2-user/Force.com-OAuth-Toolkit-for-PHP/oauth.php");
require_once("oauth.php");

if ( $argc == 2 ) {
  $name = "$argv[1]";
}
else {
  echo "[Usage]: $argv[0] テスト\n";
  exit;
}

// Salesforce REST APIで接続するアプリケーションの必要な情報
$DATABASEDOTCOM_CLIENT_ID = "xxx";
$DATABASEDOTCOM_CLIENT_SECRET = "xxx";
$DATABASEDOTCOM_CLIENT_USERNAME = "xxx";
$DATABASEDOTCOM_CLIENT_AUTHENTICATE_PASSWORD = "xxx";
$DATABASEDOTCOM_HOST = "test.salesforce.com";

$LOGIN_URL = "https://" . $DATABASEDOTCOM_HOST . "/";

// var_dump($LOGIN_URL);

///// Salesforce REST API接続用の設定
$CALLBACK_URL = 'https://***.example.com/oauth2/callback';

/////
// Salesforce REST API接続用のOauthインスタンスを生成
$oauth = new oauth( $DATABASEDOTCOM_CLIENT_ID, $DATABASEDOTCOM_CLIENT_SECRET, $CALLBACK_URL, $LOGIN_URL, $CACHE_DIR);
// var_dump($oauth);
// Salesforce REST API接続にあたりSalesforceへの認証を実行
$oauth->auth_with_password( $DATABASEDOTCOM_CLIENT_USERNAME, $DATABASEDOTCOM_CLIENT_AUTHENTICATE_PASSWORD );

var_dump($oauth);

// カスタムAPIのURLを指定する
$url = $oauth->instance_url . "/services/apexrest/CustomContact/?name=" . $name;
var_dump($url);

$curl = curl_init($url);
// var_dump($url);

curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: OAuth " . $oauth->access_token));

// Salesforce REST API実行結果を保存
$response = json_decode(curl_exec($curl), true);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//var_dump($status);

if ( $status != 200 ) {
    print("Salesforce Custom REST API Access Failed  StatusCode =[" . $status . "]\n");
} else {
    print("Salesforce Custom REST API Access Success StatusCode =[" . $status . "]\n");
}

var_dump( $response );

curl_close($curl);

$oauth->auth_with_refresh_token();

?>
