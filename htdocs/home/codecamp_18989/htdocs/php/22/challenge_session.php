<?php
$message = array();
$now = date('Y-m-d H:i:s');

session_start();

print session_id();
print session_name();

if (isset($_SESSION['count'])) {
    $_SESSION['count']++;
    $message[] = $now . '現在時刻';
    $message[] = $_SESSION['last_visit'] . '前回アクセス日時';
    $_SESSION['last_visit'] = $now;
} else {
    $_SESSION['count'] = 1;
    $_SESSION['last_visit'] = $now;
    $message[] = $now . '現在時刻';
}
$message[] =  $_SESSION['count'] . '回目の訪問です';

if (isset($_POST['submit'])) {
    // // セッション名取得 ※デフォルトはPHPSESSID
    // $session_name = session_name();
    // セッション変数を全て削除
    $_SESSION = array();
    // // ユーザのCookieに保存されているセッションIDを削除
    // if (isset($_COOKIE[$session_name])) {
    //   setcookie($session_name, '', time() - 42000);
    // }
    // セッションIDを無効化
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Session</title>
    </head>
    <body>
    <form method = 'post'>
        <?php foreach($message as $read) { ?>
            <p><?php print $read; ?> </p>   
        <?php } ?>
        <input type = "submit" name = "submit" value = "履歴を削除">
    </form>
  </body>
</html>