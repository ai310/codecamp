<?php
$now = date('Y-m-d H:i:s');

// cookieが設定されていなければ(初回アクセス)、cookieを設定する
if (!isset($_COOKIE['visit_count']) ) {
// cookieを設定
    setcookie('visit_count', 1);
    $message[] = '初めてのアクセスです';
    $message[] = $now . '現在時刻';
    setcookie('last_visit', $now);
} else {
    // cookieがすでに設定されていれば(2回目以降のアクセス)、cookieで設定した数値を加算する
    $count = $_COOKIE['visit_count'] + 1;
    setcookie('visit_count', $count);
    $message[] = '合計' . $count . '回目のアクセスです';
    
    $last_visit = $_COOKIE['last_visit'];
    setcookie('last_visit', $now);
    $message[] = $now . '現在時刻';
    $message[] = $last_visit . '前回アクセス日時';
}
if (isset($_POST['submit'])) {
    // Cookieを削除する
    // setcookie('visit_count', '', $now - 3600);
    // setcookie('last_visit', '', $now - 3600);
    setcookie('visit_count', '');
    setcookie('last_visit', '');
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Cookie</title>
    </head>
    <body>
        <form method = 'post'>
        <?php foreach($message as $read) { ?>
            <p><?php print $read; ?></p>
        <?php } ?>
        <input type = "submit" name = "submit" value = "履歴を削除">
        </form>
    </body>
</html>