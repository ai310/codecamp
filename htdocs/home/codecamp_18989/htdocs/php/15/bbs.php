<?php
$log = date('Y-m-d H:i:s');
$name = '';
$comment = '';
$name_length = '';
$comment_length = '';
$error = array();
$rows = array();
$host     = 'localhost';
$username = 'codecamp18989';   // MySQLのユーザ名
$password = 'YUHOKMAH';       // MySQLのパスワード
$dbname   = 'codecamp18989';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード
$dbh = ''; //database handle

// MySQL用のDSN文字列(data server name)
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

//例外処理
try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    //エラーモードの設定
    $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //プリペアドステートメント設定
    $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//DB接続失敗
} catch (PDOException $e) {
    $error[] =  '接続できませんでした。理由：' . $e -> getMessage();
}

//DB接続が成功したら、次へ進む

// postでデータ送信された場合、以下を処理
if($_SERVER['REQUEST_METHOD'] === 'POST'){
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
    // errorなければ、insert 
    if (count($error) === 0){
        try {
            $sql = 'INSERT INTO post (user_name, user_comment, create_datetime) VALUES (\'' . $name . '\',\'' . $comment . '\',\'' . $log . '\')';
            print $sql;
            $stmt = $dbh -> prepare($sql);
            $stmt -> execute();
        } catch (PDOException $e) {
            $error[] =  '接続できませんでした。理由：' . $e -> getMessage();
        }
    }
}
try {
    // insertが成功したら、select
    $sql = 'SELECT user_name, user_comment, create_datetime from post';
    $stmt = $dbh->prepare($sql);
    $stmt -> bindValue(':user_name', $name, PDO::PARAM_STR);
    $stmt -> bindValue(':user_comment', $comment, PDO::PARAM_STR);
    $stmt -> bindValue(':create_datetime', $log, PDO::PARAM_STR);
    $stmt -> execute();
    $rows = $stmt -> fetchAll();
} catch (PDOException $e) {
    $error[] =  '接続できませんでした。理由：' . $e -> getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = 'UTF-8'>
        <title>ひとこと掲示板</title>
        <style>
            .red {
                color: red;
            }
            .blue {
                color: blue;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>ひとこと掲示板</h1>
        <?php foreach($error as $read) { ?>
            <p class = 'red'><?php print $read; ?></p>
        <?php } ?>
        <form method = 'post'>
            <p>名前:<input type = "text" name = "name">
            ひとこと:<input type = "text" name = "comment">
            <input type = 'submit' name = 'submit' value = '送信'></p>
        </form>
        <?php foreach($rows as $row) { ?>
            <p><?php print '<span class = "blue">' . $row['user_name'] . '</span>' . ':' . '　　' . $row['user_comment'] . '　　' . $row['create_datetime']; ?></p>
        <?php } ?>
    </body>
</html>