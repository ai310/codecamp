<?php
$filename = './access_log.txt';
$log = date('Y-m-d H:i:s') . "\t" .
      $_SERVER['REMOTE_ADDR'] . "\t" .
      $_SERVER['HTTP_USER_AGENT'] . "\n";
if (($fp = fopen($filename, 'a')) !== FALSE) {
  if (fwrite($fp, $log) === FALSE) {
    print 'アクセスログ書き込み失敗: ' . $filename;
  }
}
fclose($fp);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>トップページ</title>
</head>
<body>
  <h1>ここはPHPを学ぶページです</h1>
  <a href="">入り口</a>
</body>
</html>