<?php
$drink_name = '';
$drink_name_length = '';
$price = '';
$price_length = '';
$create_datetime = date('Y-m-d H:i:s');
$error = array();
$rows = array ();

$host     = 'localhost';
$username = 'codecamp18989';   // MySQLのユーザ名
$password = 'YUHOKMAH';       // MySQLのパスワード
$dbname   = 'codecamp18989';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード

// MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

//DB接続
try {
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    $error[] = '接続できません。理由:' . $e -> getMessage();
}

//error処理とinsert
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    //drink_name
    if(isset($_POST['drink_name']) === TRUE) {
        $drink_name = $_POST['drink_name'];
    }
    $drink_name = str_replace(array(" ", "　"), "", $drink_name);
    $drink_name_length = mb_strlen($drink_name);
    if($drink_name_length === 0) {
        $error[] = 'ドリンク名を入力して下さい。';
    }
    //price
    if(isset($_POST['price']) === TRUE) {
        $price = $_POST['price'];
    }
    $price = str_replace(array(" ", "　"), "", $price);
    $price_length = mb_strlen($price);
    if($price_length === 0) {
        $error[] = '値段を入力して下さい。';
    }
    if(count($error) === 0) {
        try {
        $sql = "insert into test_drink_master (drink_name, price, create_datetime) values ('" . $drink_name . "','" . $price . "','" . $create_datetime . "')";
        $stmt = $dbh -> prepare($sql);
        $stmt -> execute();
        } catch (PDOException $e) {
            $error[] =  '接続できませんでした。理由：' . $e -> getMessage();
        }
    }
}
try {
    $sql = 'select drink_name, price, create_datetime from test_drink_master';
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':drink_name', $drink_name, PDO::PARAM_STR);
    $stmt -> bindValue(':price', $price, PDO::PARAM_STR);
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
        <title>ドリンク管理</title>
    </head>
    <body>
        <h1>自動販売機管理ツール</h1>
        <h2>新規商品追加</h2>
        <?php foreach($error as $read) { ?>
            <p ><?php print $read; ?></p>
        <?php } ?>
        <form method = 'post'>
            <p>名前:<input type = 'text' name = 'drink_name'></p>
            <p>値段:<input type = 'text' name = 'price'></p>
            <p><input type = 'submit' name = 'submit' value = '商品を追加'></p>
        </form>
        <h2>商品情報変更</h2>
        <p>商品一覧</p>
        <?php foreach($rows as $row) { ?>
            <p><?php print $row['drink_name'] . $row['price'] . $row['create_datetime']; ?></p>
        <?php } ?>
    </body>
</html>
