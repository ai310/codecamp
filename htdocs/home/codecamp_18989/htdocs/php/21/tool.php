<?php
$drink_name = '';
$drink_name_length = '';
$price = '';
$price_length = '';
$stock = '';
$stock_length = '';
$new_img = '';
$status = '';
$drink_id = '';
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

//post送信されたとき
if (($_SERVER['REQUEST_METHOD']) === 'POST') {
    if (isset($_POST['submit'])) {
         $kind = $_POST['submit'];
        //「商品を追加」がおされたとき
        if ($kind === '商品を追加') {
            //名前値が入っている＆空欄チェック
            if (isset($_POST['drink_name'])) {
                $drink_name = $_POST['drink_name'];
                $drink_name = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $drink_name);
                $drink_name_length = mb_strlen($drink_name);
                if($drink_name_length === 0) {
                    $error[] = 'ドリンク名を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //値段値が入っている＆０以上チェック
            if (isset($_POST['price'])) {
                $price = $_POST['price'];
                $price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8');
                $price = preg_replace('/^[ 　]+/', '', $price);
                $price = preg_replace('/[ 　]+$/', '', $price);
                $price_length = mb_strlen($price);
                if($price_length === 0) {
                    $error[] = '値段を入力して下さい。';
                } else if (preg_match('/^[0-9]+$/', $price) === 0) {
                    $error[] = '値段は0以上の整数を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //個数値が入っている＆０以上チェック
            if (isset($_POST['stock'])) {
                $stock = $_POST['stock'];
                $stock = preg_replace('/^[ 　]+/', '', $stock);
                $stock = preg_replace('/[ 　]+$/', '', $stock);
                $stock_length = mb_strlen($stock);
                if($stock_length === 0) {
                    $error[] = '個数を入力して下さい。';
                } else if (preg_match('/^[0-9]+$/', $stock) === 0) {
                    $error[] = '個数は0以上の整数を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //アップロード画像ファイルの保存
            if (isset($_FILES['new_img'])) {
                if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
                    $extension = pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION);      // 画像の拡張子を取得
                    //画像ファイルの指定
                    $img_file = $_FILES['new_img']['tmp_name'];
                    //MIMEタイプの取得
                    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $img_file);
                    finfo_close($finfo);
                    if (($extension === 'png' || $extension === 'jpeg' || $extension === 'jpg') && ($mime_type === 'image/png' || $mime_type === 'image/jpeg')) {    // 指定の拡張子であるかどうかチェック
                        $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;    // 保存する新しいファイル名の生成（ユニークな値を設定する）
                        if (is_file($img_dir . $new_img_filename) !== TRUE) {   // 同名ファイルが存在するかどうかチェック
                            if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {    // アップロードされたファイルを指定ディレクトリに移動して保存
                                $error[] = 'ファイルアップロードに失敗しました';
                            }
                        } else {
                            $error[] = 'ファイルアップロードに失敗しました。再度お試し下さい。';
                        }
                    } else {
                        $error[] = 'ファイル形式が異なります。画像ファイルはJPEGとPNGのみ利用可能です。';
                    }
                } else {
                    $error[] = 'ファイルを選択して下さい';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //status値が入っているかチェック
            if (isset($_POST['status'])) {
                $status = $_POST['status'];
                if (preg_match('/^[01]+$/', $status) === 0) {
                    $error[] = 'ステータスは公開か未公開を選択して下さい。';
                    } else if ($status === '') {
                        $error[] = '公開か非公開を入力して下さい。';
                    }
            } else {
                $error[] = '不正な操作です。';
            }
            //トランズアクション開始
            $dbh->beginTransaction();
            try{
                //errorがなければdrink_masterへ、insert
                if (count($error) === 0) {
                    try {
                        $sql = 'INSERT INTO drink_master (drink_name, price, img, status, create_datetime, update_datetime) values (?,?,?,?,?,?)';
                        $stmt = $dbh -> prepare($sql);
                        $stmt -> bindValue(1, $drink_name, PDO::PARAM_STR);
                        $stmt -> bindValue(2, $price, PDO::PARAM_INT);
                        $stmt -> bindValue(3, $new_img_filename, PDO::PARAM_STR);
                        $stmt -> bindValue(4, $status, PDO::PARAM_INT);
                        $stmt -> bindValue(5, $create_datetime, PDO::PARAM_STR);
                        $stmt -> bindValue(6, $update_datetime, PDO::PARAM_STR);
                        $stmt -> execute();
                        $drink_id = $dbh-> lastInsertId();
                    } catch (PDOException $e) {
                        $error[] = 'drink_master書き込みできませんでした。理由：' . $e -> getMessage();
                        throw $e;
                    }
                }
                //errorがなければdrink_stockへ、insert
                if (count($error) === 0) {
                    try {
                        $sql = 'INSERT INTO drink_stock (drink_id, stock, create_datetime, update_datetime) values (?,?,?,?)';
                        $stmt = $dbh -> prepare($sql);
                        $stmt -> bindValue(1, $drink_id, PDO::PARAM_INT);
                        $stmt -> bindValue(2, $stock, PDO::PARAM_INT);
                        $stmt -> bindValue(3, $create_datetime, PDO::PARAM_STR);
                        $stmt -> bindValue(4, $update_datetime, PDO::PARAM_STR);
                        $stmt -> execute();
                    } catch (PDOException $e) {
                        $error[] = 'drink_stock書き込みできませんでした。理由：' . $e -> getMessage();
                        throw $e;
                    }
                }
                //コミット処理
                $dbh->commit();
                $succeed[] = 'データ登録完了しました。';
            } catch (PDOException $e) {
                //ロールバック処理
                $dbh->rollback();
                throw $e;
            }
        //在庫変更が押されたとき
        } else if ($kind === '変更') {
            if (isset($_POST['stock_change'])) {
                $datetime = date('Y-m-d H:i:s');
                $drink_id = (int)$_POST['drink_id'];
                $stock_change = $_POST['stock_change'];
                $stock_change = preg_replace('/^[ 　]+/', '', $stock_change);
                $stock_change = preg_replace('/[ 　]+$/', '', $stock_change);
                if (preg_match('/^[0-9]+$/', $stock_change) === 0) {
                    $error[] = '在庫数は0以上の整数を入力して下さい。';
                } else if (count($error) === 0) {
                    $stock_change = (int)$stock_change;
                    try {
                        $sql = 'UPDATE drink_stock SET stock= ?, update_datetime= ? WHERE drink_id = ?';
                        $stmt = $dbh -> prepare($sql);
                        $stmt -> bindValue(1, $stock_change, PDO::PARAM_INT);
                        $stmt -> bindValue(2, $update_datetime, PDO::PARAM_STR);
                        $stmt -> bindValue(3, $drink_id, PDO::PARAM_INT);
                        $stmt -> execute();
                        $succeed[] = '在庫変更が完了しました。';
                    } catch (PDOException $e) {
                        $error[] =  '在庫変更できませんでした。理由：' . $e -> getMessage();
                    }
                }
            } else {
                $error[] = '不正な操作です。';
            }
        //ステータスが変更されたとき
        } else if ($kind === '非公開→公開') {
            $datetime = date('Y-m-d H:i:s');
            $drink_id = (int)$_POST['drink_id'];
            try {
                $sql = 'UPDATE drink_master SET status = 1, update_datetime = ? WHERE drink_id = ?';
                $stmt = $dbh -> prepare($sql);
                $stmt -> bindValue(1, $datetime, PDO::PARAM_STR);
                $stmt -> bindValue(2, $drink_id, PDO::PARAM_INT);
                $stmt -> execute();
                $succeed[] = 'ステータス変更が完了しました。';
            } catch (PDOException $e) {
                $error[] =  'ステータス変更できませんでした。理由：' . $e -> getMessage();
            }
        } else if ($kind === '公開→非公開') {
            $datetime = date('Y-m-d H:i:s');
            $drink_id = (int)$_POST['drink_id'];
            try {
                $sql = 'UPDATE drink_master SET status = 0, update_datetime = ? WHERE drink_id = ?';
                $stmt = $dbh -> prepare($sql);
                $stmt -> bindValue(1, $datetime, PDO::PARAM_STR);
                $stmt -> bindValue(2, $drink_id, PDO::PARAM_INT);
                $stmt -> execute();
                $succeed[] = 'ステータス変更が完了しました。';
            } catch (PDOException $e) {
                $error[] =  'ステータス変更できませんでした。理由：' . $e -> getMessage();
            }
        }
    }
}
//テーブル結合とデータ読み取り
try {
    $sql = 'SELECT drink_master.drink_id, drink_master.drink_name, drink_master.price, drink_master.img, drink_master.status, drink_stock.stock FROM drink_master JOIN drink_stock ON drink_master.drink_id = drink_stock.drink_id';
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
            .gray {
                background-color: gray;
            }
        </style>
    </head>
    <body>
        <h1>自動販売機管理ツール</h1>
        <?php foreach($error as $read) { ?>
            <p><?php print $read; ?></p>
        <?php } ?>
        <?php if (count($error) === 0) { ?>
            <?php foreach($succeed as $read) { ?>
            <p><?php print $read; ?></p>
            <?php } ?>
        <?php } ?>
        <h2>新規商品追加</h2>
        <form method = "post" enctype = "multipart/form-data">
            <p>名前:<input type = "text" name = "drink_name"></p>
            <p>値段:<input type = "text" name = "price"></p>
            <p>個数:<input type = "text" name = "stock"></p>
            <p><input type = "file" name = "new_img"></p>
            <p><select name = "status">
                <option value = "0">非公開</option>
                <option value = "1">公開</option>
            </select></p>
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
                <th>ステータス</th>
            </tr>
            <?php foreach ($rows as $row) { ?>
                <?php if ($row['status'] === "0") { ?>
            <tr class = "gray">
                <?php } else if ($row['status'] === "1") { ?>
            <tr>
                <?php } ?>
                <form method = "post">
                    <td><img src = "<?php print $img_dir . htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                    <td><?php print htmlspecialchars($row['drink_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                    <td><input type = "text" name = "stock_change" value = "<?php print $row['stock']; ?>">個<input type = "submit" name = "submit" value = "変更">
                    <input type = "hidden" name = "drink_id" value = "<?php print $row['drink_id']; ?>"></td>
                    <td>
                <?php if ($row['status'] === "0") { ?>
                        <input type = "submit" name = "submit" value = "非公開→公開">
                <?php } else if ($row['status'] === "1") { ?>
                        <input type = "submit" name = "submit" value = "公開→非公開">
                    <?php } ?>
                    </td>
                </form>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>