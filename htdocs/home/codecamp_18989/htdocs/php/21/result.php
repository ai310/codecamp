<?php
$input_price = '';
$input_price_length = '';
$drink_id = '';
$drink_name = '';
$price = '';
$stock = '';
$history_id = '';
$img_dir    = './img/';
$create_datetime = date('Y-m-d H:i:s');
$rows = array();

$host     = 'localhost';
$username = 'codecamp18989';
$password = 'YUHOKMAH';
$dbname   = 'codecamp18989';
$charset  = 'utf8';

$error = array();

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
//ドリンクと値段チェック
if (($_SERVER['REQUEST_METHOD']) === 'POST') {
    //ドリンク選択されているか
    if (isset($_POST['drink_choice'])) {
        $drink_id = $_POST['drink_choice'];
    } else {
        $error[] = 'ドリンクを選択して下さい。';
    }
    //金額値あるか＆０以上の整数か
    if (isset($_POST['input_price'])) {
        $input_price = $_POST['input_price'];
        $input_price = preg_replace('/^[ 　]+/', '', $input_price);
        $input_price = preg_replace('/[ 　]+$/', '', $input_price);
        $input_price_length = mb_strlen($input_price);
        if($input_price_length === 0) {
            $error[] = '値段を入力して下さい。';
        } else if (preg_match('/^[0-9]+$/', $input_price) === 0) {
            $error[] = '値段は0以上の整数を入力して下さい。';
        } 
    } else {
        $error[] = '不正な操作です。';
    }
    if (count($error) === 0) {
        //テーブル結合と読み込み
        try {
            $sql = 'SELECT drink_master.drink_id, drink_master.drink_name, drink_master.price, drink_stock.stock, drink_master.img, drink_master.status 
            FROM drink_master JOIN drink_stock ON drink_master.drink_id = drink_stock.drink_id 
            WHERE drink_master.drink_id =' . $drink_id;
            $stmt = $dbh -> prepare($sql);
            $stmt -> execute();
            $rows = $stmt -> fetchAll();
        } catch (PDOException $e) {
            $error[] =  '読み込みできませんでした。理由：' . $e -> getMessage();
        } 
        //配列取り出し
        foreach ($rows as $row) {
            $drink_name = $row['drink_name'];
            $price = $row['price'];
            $stock = $row['stock'];
            $status = $row['status'];
            $img = $row['img'];
            if ($price > $input_price) {
                $error[] = '金額が足りません。';
            } else if ($status === '0') {
                $error[] = '購入できません。';
            } else if ($stock <= 0) {
                $error[] = '在庫がありません。';
            }  else if (count($error) === 0) {
                try {
                    $sql = 'UPDATE drink_stock SET stock = ? WHERE drink_id = ?';
                    $stmt = $dbh -> prepare($sql);
                    $stmt -> bindValue(1, $stock-1, PDO::PARAM_STR);
                    $stmt -> bindValue(2, $drink_id, PDO::PARAM_STR);
                    $stmt -> execute();
                } catch (PDOException $e) {
                    $error[] = 'drink_stock更新できませんでした。理由：' . $e -> getMessage();
                }    
            }
            if (count($error) === 0) {
                //drink_historyへ書きこみ
                try {
                    $sql = 'INSERT INTO drink_history (drink_id, create_datetime) values (?,?)';
                    $stmt = $dbh -> prepare($sql);
                    $stmt -> bindValue(1, $drink_id, PDO::PARAM_STR);
                    $stmt -> bindValue(2, $create_datetime, PDO::PARAM_STR);
                    $stmt -> execute();
                } catch (PDOException $e) {
                    $error[] = 'drink_history書き込みできませんでした。理由：' . $e -> getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF=8">
        <title>自動販売機結果</title>
    </head>
    <body>
        <h1>自動販売機結果</h1>
        <?php foreach ($error as $read) { ?>
            <p><?php print $read; ?></p>
        <?php } ?>
        <?php if (($_SERVER['REQUEST_METHOD']) === 'POST') { ?>
            <?php if (count($error) === 0) { ?>
                <p>がしゃん！【<?php print htmlspecialchars($row['drink_name'], ENT_QUOTES, 'UTF-8'); ?>】が買えました！</p>
                <p>おつりは【<?php print $input_price - $price; ?>円】です</p>
                <p><img src = "<?php print $img_dir . $img; ?>"></p>
            <?php } ?>
        <?php } else { ?>
            <p>不正なアクセスです。</p>
        <?php } ?>
        <a href="http://codecamp_18989.lesson6.codecamp.jp//php/21/index.php" class="return">戻る</a>
    </body>
</html>