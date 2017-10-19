<?php
$filename = './review.txt';
$log = date('Y-m-d H:i:s');
$name = '';
$comment = '';
$name_length = '';
$comment_length = '';
$error = array();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset ($_POST['name']) === TRUE) {
        $name = $_POST['name'];
    }
    $name = str_replace(array(" ", "　"), "", $name);
    $name_length = mb_strlen($name);
    if ($name_length === 0) {
        $error[] = '・名前を入力してください';
    }
    if ($name_length > 20) {
        $error[] = '・名前は20文字以内で入力してください';
    }
    if (isset ($_POST['comment']) === TRUE) {
        $comment = $_POST['comment'];
    }
    $comment = str_replace(array(" ", "　"), "", $comment);
    $comment_length = mb_strlen($comment);
    if ($comment_length === 0) {
        $error[] = '・ひとことを入力してください';
    }
    if ($comment_length > 100) {
        $error[] = '・ひとことは100文字以内で入力してください';
    }
    if (count($error) === 0) {
        if (($fp = fopen($filename, 'a')) !== FALSE) {
            if(fwrite($fp, $name . ':'. "\t" . $comment. "\t" . '-' . $log . "\n") === FALSE) {
                $error[] = 'ファイル書き込み失敗';
            }
        }
    fclose($fp);
    }
}
$data = array();
if (is_readable($filename) === TRUE) {
  if (($fp = fopen($filename, 'r')) !== FALSE) {
    while (($tmp = fgets($fp)) !== FALSE) {
      $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');
    }
    fclose($fp);
  }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = 'UTF-8'>
        <title>ひとこと掲示板</title>
    </head>
    <body>
        <h1>ひとこと掲示板</h1>
        <style>
            .red {
                color:red;
            }
        </style>
        <?php foreach($error as $read) { ?>
            <p class = 'red'><?php print $read; ?></p>
        <?php } ?>
        <form method = 'post'>
            <p>名前:<input type = "text" name = "name">
            ひとこと:<input type = "text" name = "comment">
            <input type = 'submit' name = 'submit' value = '送信'></p>
        </form>
        <?php foreach($data as $read) { ?>
            <p><?php print $read; ?></p>
        <?php } ?>
    </body>
</html>