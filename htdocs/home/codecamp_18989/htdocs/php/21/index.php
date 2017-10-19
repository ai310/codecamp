<?php
$drink_name = '';
$drink_name_length = '';
$price = '';
$price_length = '';
$stock = '';
$stock_length = '';
$new_img = '';
$status = '';
$new_img_filename = '';
$img_dir    = './img/';
$create_datetime = date('Y-m-d H:i:s');
$update_datetime = date('Y-m-d H:i:s');

$error = array();
$succeed = array();

$host     = 'localhost';
$username = 'codecamp18989';
$password = 'YUHOKMAH';
$dbname   = 'codecamp18989';
$charset  = 'utf8';

// MySQL用のDSN文字列
$dsn = 'mysql:dbname=' . $dbname . ';host=' . $host . ';charset=' . $charset;

//DB接続
try {
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    $error[] = 'DBへ接続できません。理由:' . $e -> getMessage();
}
//テーブル結合とデータ読み込み
try {
    $sql = 'SELECT drink_master.drink_id, drink_master.drink_name, drink_master.price, drink_master.img, drink_master.status, drink_stock.stock FROM drink_master JOIN drink_stock ON drink_master.drink_id = drink_stock.drink_id WHERE drink_master.status = 1';
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
    $rows = $stmt -> fetchAll();
} catch (PDOException $e) {
    $error[] =  '読み込みできませんでした。理由：' . $e -> getMessage();
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>自動販売機</title>
        <style>
            .container {
                display:flex;
                flex-wrap: wrap; 
            }
            .box {
                flex:1;
                text-align:center;
            }
        </style>
    </head>
    <body>
        <h1>自動販売機</h1>
        <form method = "post" action="./result.php">
            <p>金額<input type = "text" name = "input_price"></p>
            <div class = "container">
                <?php foreach ($rows as $row) { ?>
                    <div class = "box">
                        <img src = "<?php print $img_dir . $row['img']; ?>">
                        <p><?php print htmlspecialchars($row['drink_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?php print $row['price'] . '円'; ?></p>
                        <?php if (($row['stock']) === "0") { ?>
                            <?php print '売り切れ'; ?>
                        <?php } else { ?>
                            <input type = "radio" name = "drink_choice" value = "<?php print $row['drink_id']; ?>">
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <p><input type = "submit" name = "submit_price" value = "購入"></p>
        </form>
    </body>
</html>