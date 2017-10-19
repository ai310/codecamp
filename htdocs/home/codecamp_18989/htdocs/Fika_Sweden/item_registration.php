<?php
$kind = '';
$name = '';
$name_length = '';
$price = '';
$price_length = '';
$stock = '';
$type = '';
$recommendation_id = '';
$recommendation_id_length = '';
$comment = '';
$comment_length = '';
$status = '';
$new_img = '';
$item_id = '';
$new_img_filename = '';
$img_dir    = './img/';
$create_date = date('Y-m-d H:i:s');
$update_date = date('Y-m-d H:i:s');
$error = array();
$succeed = array();
$rows = array();
$category = array('塩系の食べ物','甘い食べ物','アルコール飲料','ノンアルコール飲料');

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
} catch (PDOException $e) {
    $error[] = 'DBへ接続できません。理由:' . $e->getMessage();
}

//post送信されたとき
if (($_SERVER['REQUEST_METHOD']) === 'POST') {
    //'submit'があったとき
    if (isset($_POST['submit']) === TRUE) {
        $kind = $_POST['submit'];
        //'商品を追加'おされたとき
        if ($kind === '商品を追加') {
            //商品名チェック
            if (isset($_POST['name']) === TRUE) {
                $name = ($_POST['name']);
                $name = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $name);
                $name_length = mb_strlen($name);
                if ($name_length === 0) {
                    $error[] = '商品名を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //値段値が入っている＆０以上チェック
            if (isset($_POST['price']) === TRUE) {
                $price = $_POST['price'];
                $price = preg_replace('/^[ 　]+/', '', $price);
                $price = preg_replace('/[ 　]+$/', '', $price);
                $price_length = mb_strlen($price);
                if ($price_length === 0) {
                    $error[] = '値段を入力して下さい。';
                } else if (preg_match('/^([1-9][0-9]*|0)$/', $price) !== 1) {
                    $error[] = '値段は0以上の整数を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //個数値が入っている＆０以上チェック
            if (isset($_POST['stock']) === TRUE) {
                $stock = $_POST['stock'];
                $stock = preg_replace('/^[ 　]+/', '', $stock);
                $stock = preg_replace('/[ 　]+$/', '', $stock);
                $stock_length = mb_strlen($stock);
                if ($stock_length === 0) {
                    $error[] = '在庫数を入力して下さい。';
                } else if (preg_match('/^([1-9][0-9]*|0)$/', $stock) !== 1) {
                    $error[] = '在庫数は0以上の整数を入力して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //おすすめ商品id値が入っているチェック
            if (isset($_POST['recommendation_id']) === TRUE) {
                $recommendation_id = $_POST['recommendation_id'];
                $recommendation_id = preg_replace('/^[ 　]+/', '', $recommendation_id);
                $recommendation_id = preg_replace('/[ 　]+$/', '', $recommendation_id);
                $recommendation_id_length = mb_strlen($recommendation_id);
                if ($recommendation_id_length === 0) {
                    $error[] = 'おすすめ商品idを入力して下さい。';
                } else if (preg_match('/^([1-9][0-9]*|0)$/', $recommendation_id) !== 1) {
                    $error[] = 'おすすめ商品idは0以上の整数を入力して下さい。';
                }
            } else {
                $error = '不正な操作です。';
            }
            //商品説明値が入っているチェック
            if (isset($_POST['comment']) === TRUE) {
                $comment = $_POST['comment'];
                $comment = preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $comment);
                $comment_length = mb_strlen($comment);
                if ($comment_length === 0) {
                    $error[] = '商品説明を入力して下さい。';
                }
            } else {
                $error = '不正な操作です。';
            }
            //分類値が入っているチェック
            if (isset($_POST['type']) === TRUE) {
                $type = $_POST['type'];
                if (preg_match('/^[0-9]+$/', $type) !== 1) {
                    $error[] = '分類を数字で選択して下さい。';
                } else if ($type >= count($category)) {
                    $error[] = '分類を候補の中から選択して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //status値が入っているかチェック
            if (isset($_POST['status']) === TRUE) {
                $status = $_POST['status'];
                if (preg_match('/^[01]$/', $status) !== 1) {
                    $error[] = 'ステータスは公開か未公開を選択して下さい。';
                }
            } else {
                $error[] = '不正な操作です。';
            }
            //アップロード画像ファイルの保存
            if (isset($_FILES['new_img']) === TRUE) {
                if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
                    $extension = pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION);      // 画像の拡張子を取得
                    //MIMEタイプの取得
                    $mime_type = exif_imagetype($_FILES['new_img']['tmp_name']);
                    if (($extension === 'png' || $extension === 'jpeg' || $extension === 'jpg') && ($mime_type === IMAGETYPE_JPEG || $mime_type === IMAGETYPE_PNG)) {
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
            //errorがなければitemsへ、insert
            if (count($error) === 0) {
                try {
                    $sql = 'INSERT INTO items (name, price, stock, recommendation_id, comment, type, status, img, create_date, update_date) values (?,?,?,?,?,?,?,?,?,?)';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $name, PDO::PARAM_STR);
                    $stmt->bindValue(2, $price, PDO::PARAM_INT);
                    $stmt->bindValue(3, $stock, PDO::PARAM_INT);
                    $stmt->bindValue(4, $recommendation_id, PDO::PARAM_INT);
                    $stmt->bindValue(5, $comment, PDO::PARAM_STR);
                    $stmt->bindValue(6, $type, PDO::PARAM_INT);
                    $stmt->bindValue(7, $status, PDO::PARAM_INT);
                    $stmt->bindValue(8, $new_img_filename, PDO::PARAM_STR);
                    $stmt->bindValue(9, $create_date, PDO::PARAM_STR);
                    $stmt->bindValue(10, $update_date, PDO::PARAM_STR);
                    $stmt->execute();
                    $succeed[] = '商品登録が完了しました。';
                } catch (PDOException $e) {
                    $error[] = 'itemsへ書き込みできませんでした。理由：' . $e->getMessage();
                    throw $e;
                }
            }
        //在庫変更が押されたとき
        } else if ($kind === '変更') {
            if (isset($_POST['stock_change']) === TRUE) {
                $datetime = date('Y-m-d H:i:s');
                $item_id = $_POST['item_id'];
                $stock_change = $_POST['stock_change'];
                $stock_change = preg_replace('/^[ 　]+/', '', $stock_change);
                $stock_change = preg_replace('/[ 　]+$/', '', $stock_change);
                if (preg_match('/^([1-9][0-9]*|0)$/', $stock_change) !== 1) {
                    $error[] = '在庫数は0以上の整数を入力して下さい。';
                } else if (count($error) === 0) {
                    try {
                        $sql = 'UPDATE items SET stock= ?, update_date= ? WHERE item_id = ?';
                        $stmt = $dbh->prepare($sql);
                        $stmt->bindValue(1, $stock_change, PDO::PARAM_INT);
                        $stmt->bindValue(2, $datetime, PDO::PARAM_STR);
                        $stmt->bindValue(3, $item_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $succeed[] = '在庫変更が完了しました。';
                    } catch (PDOException $e) {
                        $error[] =  '在庫変更できませんでした。理由：' . $e->getMessage();
                    }
                }
            } else {
                $error[] = '不正な操作です。';
            }
        //ステータスが変更されたとき
        } else if ($kind === '非公開→公開') {
            $datetime = date('Y-m-d H:i:s');
            $item_id = $_POST['item_id'];
            try {
                $sql = 'UPDATE items SET status = 1, update_date = ? WHERE item_id = ?';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $datetime, PDO::PARAM_STR);
                $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
                $stmt->execute();
                $succeed[] = 'ステータス変更が完了しました。';
            } catch (PDOException $e) {
                $error[] =  'ステータス変更できませんでした。理由：' . $e->getMessage();
            }
        } else if ($kind === '公開→非公開') {
            $datetime = date('Y-m-d H:i:s');
            $item_id = $_POST['item_id'];
            try {
                $sql = 'UPDATE items SET status = 0, update_date = ? WHERE item_id = ?';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $datetime, PDO::PARAM_STR);
                $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
                $stmt->execute();
                $succeed[] = 'ステータス変更が完了しました。';
            } catch (PDOException $e) {
                $error[] =  'ステータス変更できませんでした。理由：' . $e->getMessage();
            }
        } else if ($kind === '削除') {
            $item_id = $_POST['item_id'];
            try {
                $sql = 'DELETE FROM items WHERE item_id = ?';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $item_id, PDO::PARAM_INT);
                $stmt->execute();
                $succeed[] = '商品削除が完了しました。';
            } catch (PDOException $e) {
                $error[] =  '商品削除できませんでした。理由：' . $e->getMessage();
            }
        }
    }
}
//テーブル結合とデータ読み取り
try {
    $sql = 'SELECT item_id, name, price, stock, recommendation_id, comment, type, status, img FROM items';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    $error[] =  '読み込みできませんでした。理由：' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>商品管理ページ</title>
        <link rel="stylesheet" href="html5reset-1.6.1.css">
        <style>
            body {
                min-width: 1200px;
                margin: 15px 0px;
            }
            .container {
                width: 1200px;
                margin: 0 auto;
            }
            .contents {
                margin: 15px auto;
                line-height: 30px
            }
            h1 {
                font-size: 40px;
            }
            h2 {
                font-size: 25px;
                margin: 15px 0px;
            }
            .gray {
                background-color: gray;
            }
            table {
                border-collapse: collapse;
                margin: 0px auto;
            }
            td, th{
                border: 1px solid black;
                margin: auto 0px;
                vertical-align: middle;
                text-align: center;
            }
            tr {
                height: 160px;
            }
            .amount {
                width: 40px;
            }
            .error {
                color: red;
            }
            .succeed {
                color: blue;
            }
            img {
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <header class = "container">
            <h1>Fika Sweden 商品管理ページ</h1>
            <a href = "./history.php">販売履歴ページへ</a>
        </header>
        <main class = "container">
            <h2>商品登録</h2>
                <?php if (count($error) === 0) { ?>
                    <?php foreach($succeed as $read) { ?>
                        <p class = "succeed"><?php print $read; ?></p>
                    <?php } ?>
                <?php } ?>
                <?php foreach ($error as $read) { ?>
                    <p class = "error"><?php print $read; ?></p>
                <?php } ?>
            <div class = "contents">
                <form method = "post" enctype = "multipart/form-data">
                    <p>商品名:<input type = "text" name = "name"></p>
                    <p>値段:<input type = "text" name = "price"></p>
                    <p>在庫数:<input type = "text" name = "stock"></p>
                    <p>おすすめ商品:<select name = "recommendation_id"></p>
                        <?php foreach ($rows as $row) { ?>
                            <option value = "<?php print $row['item_id']; ?>"><?php print $row['name']; ?></option>
                        <?php } ?>
                    </select></p>
                    <p>商品説明<textarea name="comment"></textarea></p>
                    <p>分類<select name = "type">
                        <?php foreach ($category as $key => $value) { ?>
                            <option value = "<?php print $key; ?>"><?php print $value; ?></option>
                        <?php } ?>
                    </select></p>
                    <p>ステータス<select name = "status">
                        <option value = "0">非公開</option>
                        <option value = "1">公開</option>
                    </select></p>
                    <p><input type = "file" name = "new_img"></p>
                    <p><input type = "submit" name = "submit" value = "商品を追加"></p>
                </form>
            </div>
            <h2>商品一覧</h2>
                <table>
                    <tr>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>価格</th>
                        <th>商品説明</th>
                        <th>分類</th>
                        <th>おすすめid</th>
                        <th>在庫数</th>
                        <th>ステータス</th>
                        <th>削除</th>
                    </tr>
                    <?php foreach ($rows as $row) { ?>
                        <?php if ($row['status'] === "0") { ?>
                    <tr class = "gray">
                        <?php } else if ($row['status'] === "1") { ?>
                    <tr>
                        <?php } ?>
                        <form method = "post">
                            <td><img src = "<?php print $img_dir . htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                            <td><?php print htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                            <td><?php print htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php print htmlspecialchars($category[$row['type']], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php print htmlspecialchars($row['recommendation_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input class = "amount" type = "text" name = "stock_change" value = "<?php print htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8'); ?>">個<input type = "submit" name = "submit" value = "変更">
                            <input type = "hidden" name = "item_id" value = "<?php print htmlspecialchars($row['item_id'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                            <td>
                        <?php if ($row['status'] === "0") { ?>
                                <input type = "submit" name = "submit" value = "非公開→公開">
                        <?php } else if ($row['status'] === "1") { ?>
                                <input type = "submit" name = "submit" value = "公開→非公開">
                        <?php } ?>
                            </td>
                            <td><input type = "submit" name = "submit" value = "削除"></td>
                        </form>
                    </tr>
                    <?php } ?>
                </table>
        </main>
    </body>
</html>