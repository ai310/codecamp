<?php
$user_id = '';
$item_id = '';
$img = '';
$name = '';
$price = '';
$amount = '';
$stock = '';
$img = '';
$total = 0;
$img_dir = './img/';
$error = array();
$rows = array();

// セッション開始
session_start();
// セッション変数からuser_id取得
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // 非ログインの場合、ログインページへリダイレクト
    header('Location: login.php');
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
        //ユーザーデータ読み取り
        try {
            $sql = 'SELECT user_name FROM customers WHERE user_id = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user_data = $stmt->fetch();
            $user_name = $user_data['user_name'];
        } catch (PDOException $e) {
            $error[] =  'ユーザーデータ読み込みできませんでした。理由：' . $e->getMessage();
            throw $e;
        }
        //テーブル結合とデータ読み取り
        try {
            $sql = 'SELECT carts.carts_id, carts.user_id, carts.item_id, carts.amount, items.name, items.price, items.img, items.stock 
            FROM carts JOIN items ON carts.item_id = items.item_id 
            WHERE carts.user_id = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error[] =  'ショッピングデータ読み込みできませんでした。理由：' . $e->getMessage();
            throw $e;
        }
        foreach ($rows as $row) {
            $stock = $row['stock'];
            $amount = $row['amount'];
            $item_id = $row['item_id'];
            $name = $row['name'];
            $price = $row['price'];
            $img = $row['img'];
            if ($amount > $stock) {
                $error[] = '申し訳ございません。' . $name . 'の在庫が足りません。';
            } else if (count($error) === 0) {
                // トランザクション開始
                $datetime = date('Y-m-d H:i:s');
                $dbh->beginTransaction();
                try {
                    //deleteカート
                    $sql = 'DELETE FROM carts WHERE user_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    //update在庫
                    $sql = 'UPDATE items SET stock = ?, update_date = ? WHERE item_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $stock-$amount, PDO::PARAM_INT);
                    $stmt->bindValue(2, $datetime, PDO::PARAM_STR);
                    $stmt->bindValue(3, $item_id, PDO::PARAM_INT);
                    $stmt->execute();
                    //insert購入履歴
                    $sql = 'INSERT INTO history (user_id, item_id, name, amount, price, create_date, img) values (?,?,?,?,?,?,?)';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                    $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
                    $stmt->bindValue(3, $name, PDO::PARAM_STR);
                    $stmt->bindValue(4, $amount, PDO::PARAM_INT);
                    $stmt->bindValue(5, $price, PDO::PARAM_INT);
                    $stmt->bindValue(6, $datetime, PDO::PARAM_STR);
                    $stmt->bindValue(7, $img, PDO::PARAM_STR);
                    $stmt->execute();
                    // コミット処理
                    $dbh->commit();
                } catch (PDOException $e) {
                    // ロールバック処理
                    $dbh->rollback();
                    // 例外をスロー
                    throw $e;
                }
            }
        }
    } else {
        $error[] = '不正な操作です。';
    }
} catch (PDOException $e) {
    $error[] = 'DBへ接続できません。理由:' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>Fika Sweden　お買い上げ</title>
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
                line-height: 30px;
                text-align: center;
            }
            .footer {
                text-align: center;
                line-height: 20px;
            }
            h1 {
                font-size: 40px;
            }
            .footer {
                text-align: center;
                line-height: 20px
            }
            table {
                border-collapse: collapse;
                margin: 0px auto;
            }
            td, th{
                border: 1px solid black;
                margin:auto 0px;
            }
            .error {
                color: red;
            }
            .total {
                font-weight: bold;
            }
            img {
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <header class = "container">
            <h1>Fika Sweden</h1>
        </header>
        <main class = "container contents">
            <?php if (($_SERVER['REQUEST_METHOD']) === 'POST') { ?>
                <?php foreach ($error as $read) { ?>
                    <p class = "error"><?php print $read;?></p>
                <?php } ?>
                <?php if(count($error) === 0) { ?>
                    <p><?php print $user_name; ?>さん、下記商品のお買い上げありがとうございました！</p>
                    <table>
                        <tr>
                            <th>商品画像</th>
                            <th>商品名</th>
                            <th>価格</th>
                            <th>個数</th>
                            <th>合計金額</th>
                        </tr>
                        <?php foreach($rows as $row) { ?>
                        <tr>
                            <form method = "post">
                                <td><img src = "<?php print $img_dir . $row['img']; ?>"></td>
                                <td><?php print htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                                <td><?php print htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8'); ?>個</td>
                                <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') * htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                            </form>
                        </tr>
                        <?php 
                            $total += htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') * htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8');
                        } ?>
                    </table>
                    <p class = "total">合計金額<?php print $total; ?>円のお買い物が完了しました。</p>
                <?php } ?>
            <?php } else { ?>
                <p><?php print '不正な操作です。'; ?></p>
            <?php } ?>
            <form method = "post" action = "./cart.php">
                <input type = "submit" name = "submit" value = "カートへ戻る">
            </form>
            <p><a href = "./item_view.php"><button>商品一覧へ戻る</button></a></p>
        </main>
        <footer class = "container footer">
            <p>お問い合わせ</p>
            <p>email:fikasweden@xxxxx</p>
            <p><small>Copyright &copy; Fika Sweden All Rights Reserved.</small></p>
        </footer>
    </body>
</html>