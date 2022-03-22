<?php
// 設定
$LOG_FILE = dirname(__FILE__).'/hook.log';
$SECRET_KEY = 'brick-plan'; //githubで設定するSECRETと同内容(好きに決めます)

if (!function_exists('getallheaders')) {
  function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
}

$header = getallheaders();
$hmac = hash_hmac('sha1', file_get_contents("php://input"), $SECRET_KEY);
if ( isset($header['X-Hub-Signature']) && $header['X-Hub-Signature'] === 'sha1='.$hmac ) {
  $payload = json_decode(file_get_contents("php://input"), true);
  //gitpullする対象のフォルダへ移動する
  exec('cd /home/users/2/main.jp-brick-plan/web/test ; git pull');
  file_put_contents($LOG_FILE, date("[Y-m-d H:i:s]")." ".$_SERVER['REMOTE_ADDR']." git pulled: ".$payload['after']." ".$payload['commits'][0]['message']."\n", FILE_APPEND|LOCK_EX);
} else {
  file_put_contents($LOG_FILE, date("[Y-m-d H:i:s]")." invalid access: ".$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND|LOCK_EX);
}

  echo 'webhook';
?>