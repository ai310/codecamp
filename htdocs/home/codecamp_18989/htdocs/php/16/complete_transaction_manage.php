<?php
$drink_name = '';
$drink_name_length = '';
$price = '';
$price_length = '';
$stock = '';
$stock_length = '';
$stock_change = '';
$img_dir    = './img/';    // アップロードした画像ファイルの保存ディレクトリ(先に作成をしておく)
$new_img_filename = '';   // アップロードした新しい画像ファイル名
$error = array();
$rows = array ();
$data = array();
$create_datetime = date('Y-m-d H:i:s');
$update_datetime = date('Y-m-d H:i:s');

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
//post送信
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kind = $_POST['submit'];
    //'変更'がおされたら
    if ($kind === '変更') {
        $datetime = date('Y-m-d H:i:s');
        $drink_id = (int)$_POST['drink_id'];
        $stock_change = (int)$_POST['stock_change'];
        $sql = 'UPDATE test_drink_stock SET stock=' . $stock_change . ',' . 'update_datetime="' . $datetime . '"' .  ' WHERE drink_id = ' . $drink_id;
        $stmt = $dbh -> prepare($sql);
        $stmt -> execute();
    //'商品追加'がおされたら
    } else if ($kind === '商品を追加') {
        //drink_name
        if(isset($_POST['drink_name']) === TRUE) {
            $drink_name = $_POST['drink_name'];
            $drink_name = str_replace(array(" ", "　"), "", $drink_name);
            $drink_name_length = mb_strlen($drink_name);
            if($drink_name_length === 0) {
                $error[] = 'ドリンク名を入力して下さい。';
            }
        }
        //price
        if(isset($_POST['price']) === TRUE) {
            $price = $_POST['price'];
            if (is_numeric($price) === FALSE) {
                $error[] = '数字を入力してください。';
            } else if ($price <= 0) {
                $error[] = '1円以上の整数を入力してください';
            }
            $price = str_replace(array(" ", "　"), "", $price);
            $price_length = mb_strlen($price);
            if($price_length === 0) {
                $error[] = '値段を入力して下さい。';
            }
        }
        //アップロード画像ファイルの保存
        if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
            $extension = pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION);      // 画像の拡張子を取得
                if ($extension === 'png' || $extension === 'jpg') {    // 指定の拡張子であるかどうかチェック
                    $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;    // 保存する新しいファイル名の生成（ユニークな値を設定する）
                        if (is_file($img_dir . $new_img_filename) !== TRUE) {   // 同名ファイルが存在するかどうかチェック
                            if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {    // アップロードされたファイルを指定ディレクトリに移動して保存
                                $error[] = 'ファイルアップロードに失敗しました';
                            }
                        } else {
                            $error[] = 'ファイルアップロードに失敗しました。再度お試しください。';
                        }
                } else {
                    $error[] = 'ファイル形式が異なります。画像ファイルはJPEGとPNGのみ利用可能です。';
                }
        } else {
            $error[] = 'ファイルを選択してください';
        }
        //stock
        if(isset($_POST['stock']) === TRUE) {
            $stock = $_POST['stock'];
            if (is_numeric($stock) === FALSE) {
                $error[] = '数字を入力してください。';
            } else if ($price <= 0) {
                $error[] = '1以上の整数を入力してください';
            }
            $stock = str_replace(array(" ", "　"), "", $stock);
            $stock_length = mb_strlen($stock);
            if($stock_length === 0) {
                $error[] = '在庫を入力して下さい。';
            }
        }
        //データ追加
        if(count($error) === 0) {
            //test_drink_masterへ追加
            try {
                $sql = 'insert into test_drink_master (drink_name, price, img, create_datetime) values (\'' . $drink_name . '\',\'' . $price . '\',\'' . $new_img_filename . '\',\'' . $create_datetime . '\')';
                $stmt = $dbh -> prepare($sql);
                $stmt -> execute();
            } catch (PDOException $e) {
                $error[] =  'test_drink_masterへ書き込みできませんでした。理由：' . $e -> getMessage();
            }
            //test_drink_stockへ追加
            try {
                $sql = 'insert into test_drink_stock (stock, create_datetime, update_datetime) values (\'' . $stock . '\',\'' . $create_datetime . '\',\'' . $update_datetime . '\')';
                $stmt = $dbh -> prepare($sql);
                $stmt -> execute();
            } catch (PDOException $e) {
                $error[] =  'test_drink_stockへ書き込みできませんでした。理由：' . $e -> getMessage();
            }
        }
    }
}  
//テーブル結合とデータ読み込み
try {
    $sql = 'SELECT test_drink_master.drink_id, test_drink_master.drink_name, test_drink_master.price, test_drink_master.img, test_drink_stock.stock FROM test_drink_master JOIN test_drink_stock ON test_drink_master.drink_id = test_drink_stock.drink_id';
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':drink_name', $drink_name, PDO::PARAM_STR);
    $stmt -> bindValue(':price', $price, PDO::PARAM_STR);
    $stmt -> bindValue(':img', $new_img_filename, PDO::PARAM_STR);
    $stmt -> bindValue(':stock', $stock, PDO::PARAM_STR);
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
        <title>ドリンク管理</title>
    </head>
    <body>
        <h1>自動販売機管理ツール</h1>
        <h2>新規商品追加</h2>
        <?php foreach($error as $read) { ?>
            <p ><?php print $read; ?></p>
        <?php } ?>
        <form method = "post" enctype = "multipart/form-data">
            <p>名前:<input type = "text" name = "drink_name"></p>
            <p>値段:<input type ="text" name = "price"></p>
            <p>個数:<input type = "text" name = "stock"></p>
            <p><input type = "file" name = "new_img"></p>
            <p><input type = "submit" name = "submit" value = "商品を追加"></p>
        </form>
        <h2>商品情報変更</h2>
        <p>商品一覧</p>
        <table border = 1>
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
            </tr>
            <?php foreach($rows as $row) { ?>
            <tr>
                <form method = "post">
                <td><img src = "<?php print $img_dir . $row['img']; ?>"></td>
                <td><?php print $row['drink_name']; ?></td>
                <td><?php print $row['price'] . '円'; ?></td>
                <td><input type = "text" name = "stock_change" placeholder = "<?php print $row['stock']; ?>">
                <input type = "hidden" name = "drink_id" value = "<?php print $row['drink_id']; ?>">
                <input type = "submit" name = "submit" value = "変更"></td>
                </form>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>