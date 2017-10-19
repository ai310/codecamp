<?php
$drink_name = '';
$drink_name_length = '';
$price = '';
$price_length = '';
$img_dir    = './img/';    // アップロードした画像ファイルの保存ディレクトリ(先に作成をしておく)
$new_img_filename = '';   // アップロードした新しい画像ファイル名
$error = array();
$rows = array ();
$data = array();
$create_datetime = date('Y-m-d H:i:s');

// DB接続用
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

    //error処理とinsert
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        //drink_name
        if(isset($_POST['drink_name']) === TRUE) {
            $drink_name = $_POST['drink_name'];
            // 空欄置き換え
            $drink_name = str_replace(array(" ", "　"), "", $drink_name);
            $drink_name_length = mb_strlen($drink_name);
            if($drink_name_length === 0) {
                $error[] = 'ドリンク名を入力して下さい。';
            }
        }
        //price
        if(isset($_POST['price']) === TRUE) {
            $price = $_POST['price'];
            // 半角数字＆正数
            if (is_numeric($price) === FALSE) {
                $error[] = '数字を入力してください。';
            } else if ($price <= 0) {
                $error[] = '1円以上の整数を入力してください';
            }
            // 空欄置き換え
            $price = str_replace(array(" ", "　"), "", $price);
            $price_length = mb_strlen($price);
            if($price_length === 0) {
                $error[] = '値段を入力して下さい。';
            }
        }

         // アップロード画像ファイルの保存
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
        
        // データ追加
        if(count($error) === 0) {
            try {
                $sql = 'insert into test_drink_master (drink_name, price, img, create_datetime) values (\'' . $drink_name . '\',\'' . $price . '\',\'' . $new_img_filename . '\',\'' . $create_datetime . '\')';
                $stmt = $dbh -> prepare($sql);
                $stmt -> execute();
            } catch (PDOException $e) {
                $error[] =  '接続できませんでした。理由：' . $e -> getMessage();
            }
        }
    }
    //データよみとり
    try {
        $sql = 'select drink_name, price, img from test_drink_master';
        $stmt = $dbh -> prepare($sql);
        $stmt -> bindValue(':drink_name', $drink_name, PDO::PARAM_STR);
        $stmt -> bindValue(':price', $price, PDO::PARAM_STR);
        $stmt -> bindValue(':img', $new_img_filename, PDO::PARAM_STR);
        $stmt -> execute();
        $rows = $stmt -> fetchAll();
    } catch (PDOException $e) {
        $error[] =  '接続できませんでした。理由：' . $e -> getMessage();
    }
} catch (PDOException $e) {
    $error[] = '接続できません。理由:' . $e -> getMessage();
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
            </tr>
            <?php foreach($rows as $value) { ?>
            <tr>
                <td><img src = "<?php print $img_dir . $value['img']; ?>"></td>
                <td><?php print $value['drink_name']; ?></td>
                <td><?php print $value['price'] . '円'; ?></td>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>