<?php
    $message = array();
    $userid = $_POST['userid'];
    $userid = trim($userid);
    $userid_regex = '/^[a-zA-Z0-9]{6,8}$/';
        if(preg_match($userid_regex, $userid)) {
            $message[] = $userid . '：ユーザIDは正しい形式で入力されています';
        } else {
            $message[] = $userid.  '：ユーザIDは正しくない形式で入力されています。';
        }
    $age = $_POST['age'];
    $age_regex = '/^[0-9]+$/';
        if(preg_match($age_regex, $age)) {
            $message[] = $age . '：正しい年齢の形式です。';
        } else {
            $message[] = $age . '正しくない年齢の形式です。';
        }
    $email = $_POST['email'];
    $email_regex = '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/';
        if(preg_match($email_regex, $email)) {
            $message[] = $email . '：正しいメールアドレスの形式です。';
        } else {
            $message[] = $email . '：正しくないメールアドレスの形式です。';
        }
    $tel = $_POST['tel'];
    $tel_regex = '/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/';
    if(preg_match($tel_regex, $tel)) {
        $message[] = $tel . '：正しい電話番号の形式です。';
    } else {
        $message[] = $tel . '：正しくない電話番号の形式です。';
    }
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = 'UTF-8'>
        <title>バリデーション</title>
    </head>
    <body>
        <?php foreach ($message as $row) { ?>
        <p><?php print $row; ?></p>
        <?php } ?>
    </body>
</html>