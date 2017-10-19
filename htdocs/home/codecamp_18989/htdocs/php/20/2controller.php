<?php
// 関数ファイル読み込み
require_once './model/2model.php';

$log = date('Y-m-d H:i:s');
$name = '';
$comment = '';
$name_length = '';
$comment_length = '';
$sql = '';
$error = array();
$rows = array();

// DB接続
$dbh = get_db_connect();

// postでデータ送信された場合、以下を処理
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset ($_POST['name']) === TRUE) {
        $name = $_POST['name'];
    }
    $name_length = get_length($name);
    if ($name_length === 0) {
        $error[] = '・名前を入力してください';
    }
    if ($name_length > 20) {
        $error[] = '・名前は20文字以内で入力してください';
    }
    if (isset ($_POST['comment']) === TRUE) {
        $comment = $_POST['comment'];
    }
    $comment_length = get_length($comment);
    if ($comment_length === 0) {
        $error[] = '・ひとことを入力してください';
    }
    if ($comment_length > 100) {
        $error[] = '・ひとことは100文字以内で入力してください';
    }
    // errorなければ、insert 
    if (count($error) === 0){
        $sql = 'INSERT INTO post (user_name, user_comment, create_datetime) VALUES (\'' . $name . '\',\'' . $comment . '\',\'' . $log . '\')';
        $sql = insert($sql);
    }
}
$sql = 'SELECT user_name, user_comment, create_datetime from post';
$rows = select($sql,$dbh);

// 商品一覧テンプレートファイル読み込み
include_once './view/view.php';
?>