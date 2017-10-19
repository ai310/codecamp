<?php
$user_id = '';
$user_name = '';
$user_name_length = '';
$password = '';
$password_length = '';
$create_date = date('Y-m-d H:i:s');
$update_date = date('Y-m-d H:i:s');
$error = array();

//ログインチェック
session_start();
// セッション変数にuser_idがあれば、メイン画面へ
if (isset($_SESSION['user_id'])) {
    header('Location: item_view.php');
    exit;
}

$host     = 'localhost';
$username = 'ai310';
$password = '';
$dbname   = 'c9';
$charset  = 'utf8';

// MySQL用のDSN文字列
$dsn = 'mysql:dbname=' . $dbname . ';host=' . $host . ';charset=' . $charset;

//DB接続
try {
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //post送信されたとき
    if (($_SERVER['REQUEST_METHOD']) === 'POST') {
        //submitがおされたとき
        if (isset($_POST['submit']) === TRUE) {
            //ユーザー名チェック
            if (isset($_POST['user_name']) === TRUE) {
                $user_name = $_POST['user_name'];
                $user_name = preg_replace('/^[ 　]+/', '', $user_name);
                $user_name = preg_replace('/[ 　]+$/', '', $user_name);
                $user_name_length = mb_strlen($user_name);
                if ($user_name_length === 0) {
                    $error[] = 'ユーザー名を入力して下さい。';
                } else if (preg_match('/^[0-9a-zA-Z]{6,10}$/', $user_name) !== 1) {
                    $error[] = 'ユーザー名は、6文字以上10文字以下の半角英数字を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //パスワードチェック
            if (isset($_POST['password']) === TRUE) {
                $password = $_POST['password'];
                $password = preg_replace('/^[ 　]+/', '', $password);
                $password = preg_replace('/[ 　]+$/', '', $password);
                $password_length = mb_strlen($password);
                if ($password_length === 0) {
                    $error[] = 'パスワードを入力して下さい。';
                } else if (preg_match('/^[0-9a-zA-Z]{6,10}$/', $password) !== 1) {
                    $error[] = 'パスワードは、6文字以上10文字以下の半角英数字を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //エラーがなかったら、DBから読み込み
            if (count($error) === 0) {
                try {
                    $sql = 'SELECT user_id FROM customers WHERE user_name = ? AND password = ? ';
                    $stmt = $dbh  ->prepare($sql);
                    $stmt->bindValue(1, $user_name, PDO::PARAM_STR);
                    $stmt->bindValue(2, $password, PDO::PARAM_STR);
                    $stmt->execute();
                    $user_data = $stmt->fetch();
                } catch (PDOException $e) {
                    $error[] = 'データの読み込みができませんでした。理由：' . $e->getMessage();
                    throw $e;
                }
                //マッチしているuser_idがDBにあったらログイン(ユーザー名＝パスワード確認済み)
                if(isset($user_data['user_id']) === TRUE) {
                    $_SESSION['user_id'] = $user_data['user_id'];
                    header('Location: ./item_view.php');
                    exit;
                //なかったらエラー
                } else {
                    $error[] = 'ログインできません。ユーザー名とパスワードを、再度ご確認下さい。';
                }
            }
        } else {
            $error[] = '不正な操作です。';
        }
    }
} catch (PDOException $e) {
    $error[] = 'DBへ接続できません。理由:' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = 'UTF-8'>
        <title>Fika Sweden ログイン</title>
        <link rel="stylesheet" href="html5reset-1.6.1.css">
        <style>
            body {
                min-width: 1200px;
                margin: 15px 0px;
            }
            .error {
                color: red;
            }
            .container {
                width: 1200px;
                margin: 0 auto;
            }
            .footer {
                text-align: center;
                line-height: 25px;
            }
            .townpic {
                width: 1200px;
            }
            h1 {
                font-size: 40px;
            }
            h2 {
                font-size: 25px;
                margin: 15px 0px;
            }
            .contents {
                margin: 15px auto;
                line-height: 30px
            }
        </style>
    </head>
    <body>
        <header class = "container">
            <h1>Fika Sweden</h1>
        </header>
        <main class = "container">
            <h2>Hejsan!(こんにちは!)Fika Swedenです!</h2>
            <img class = "townpic" src = "./toppage_img/sodermalm-2023503_1280.jpg">
            <?php foreach ($error as $read) { ?>
                <p class = "error"><?php print $read;?></p>
            <?php } ?>
            <div class = "contents">
                    <p>＜アカウントをお持ちの方＞</p>
                    <p>ユーザー名とパスワードを入力してログインして下さい。</p>
                <form method = "post">
                    <p>ユーザー名:<input type = "text" name = "user_name" placeholder = "123abc"></p>
                    <p>パスワード:<input type = "password" name = "password"></p>
                    <p><input type = "submit" name = "submit" value = "ログイン"></p>
                </form>
                    <p>＜アカウントをお持ちでない方＞</p>
                    <p>登録ボタンから、ユーザー登録を行って下さい。</p>
                    <p><a href = "./user_registration.php"><button>登録</button></a></p>
            </div>
        </main>
        <footer class = "container footer">
            <p>お問い合わせ</p>
            <p>email:fikasweden@xxxxx</p>
            <p><small>Copyright &copy; Fika Sweden All Rights Reserved.</small></p>
        </footer>
    </body>
</html>