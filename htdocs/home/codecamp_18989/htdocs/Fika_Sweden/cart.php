<?php
$total = 0;
$user_id = '';
$user_name = '';
$item_id = '';
$amount = '';
$amount_length = '';
$amount_change_length = '';
$kind = '';
$carts_id = '';
$img_dir = './img/';
$error = array();
$succeed = array();
$rows = array();
$create_date = date('Y-m-d H:i:s');
$update_date = date('Y-m-d H:i:s');

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
        //'submit'があったとき
        if (isset($_POST['submit']) === TRUE) {
            $kind = $_POST['submit'];
            //カートに入れるがおされたとき
            if ($kind === 'カートに入れる') {
                //which_itemがあったとき
                if (isset($_POST['which_item']) === TRUE) {
                    $item_id = $_POST['which_item'];
                } else {
                    $error[] = '不正な操作です。';
                }
                //amountが送信あったとき
                if (isset($_POST['amount']) === TRUE) {
                    $amount = $_POST['amount'];
                    $amount = preg_replace('/^[ 　]+/', '', $amount);
                    $amount = preg_replace('/[ 　]+$/', '', $amount);
                    $amount_length = mb_strlen($amount);
                    //個数入力と0以上整数チェック
                    if ($amount_length === 0) {
                        $error[] = '個数を入力して下さい。';
                    } else if (preg_match('/^([1-9][0-9]*)$/', $amount) !== 1) {
                        $error[] = '個数は1以上の整数を入力して下さい。';
                    } else if(count($error) === 0) {
                        //ストック十分あるか照合
                        try {
                            $sql = 'SELECT stock, name FROM items WHERE item_id = ?';
                            $stmt = $dbh->prepare($sql);
                            $stmt->bindValue(1, $item_id, PDO::PARAM_INT);
                            $stmt->execute();
                            $stock_data = $stmt->fetch();
                            $stock = $stock_data['stock'];
                            $name = $stock_data['name'];
                        } catch (PDOException $e) {
                            $error[] =  '在庫データ読み込みできませんでした。理由：' . $e->getMessage();
                            throw $e;
                        }
                        if ($amount > $stock) {
                            $error[] = '申し訳ございません。' . $name . 'の在庫が足りません。';
                        }
                    }
                } else {
                    $error[] = '不正な操作です。';
                }
                //エラーがなければ
                if (count($error) === 0) {
                    //同じ商品がカートにあるか確認
                    try {
                        $sql = 'SELECT amount FROM carts WHERE user_id = ? AND item_id = ?';
                        $stmt = $dbh->prepare($sql);
                        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                        $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $carts_data = $stmt->fetch();
                        $stock_incarts = $carts_data['amount'];
                    } catch (PDOException $e) {
                        $error[] =  'カートデータ読み込みできませんでした。理由：' . $e->getMessage();
                        throw $e;
                    }
                }
                //エラーがなければ
                if (count($error) === 0) {
                    if (isset($stock_incarts) === TRUE) {
                        $datetime = date('Y-m-d H:i:s');
                        //同じ商品がカートにあったら、個数update
                        try {
                            $sql = 'UPDATE carts SET amount = ?, update_date = ? WHERE item_id = ? AND user_id = ?';
                            $stmt = $dbh->prepare($sql);
                            $stmt->bindValue(1, $amount + $stock_incarts, PDO::PARAM_INT);
                            $stmt->bindValue(2, $datetime, PDO::PARAM_STR);
                            $stmt->bindValue(3, $item_id, PDO::PARAM_INT);
                            $stmt->bindValue(4, $user_id, PDO::PARAM_INT);
                            $stmt->execute();
                        } catch (PDOException $e) {
                            $error[] = 'carts更新できませんでした。理由：' . $e->getMessage();
                            throw $e;
                        }
                    } else {
                        //同じ商品がカートになかったら、データ書き込み
                        try {
                            $sql = 'INSERT INTO carts (user_id, item_id, amount, create_date, update_date) values (?,?,?,?,?)';
                            $stmt = $dbh->prepare($sql);
                            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                            $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
                            $stmt->bindValue(3, $amount, PDO::PARAM_INT);
                            $stmt->bindValue(4, $create_date, PDO::PARAM_STR);
                            $stmt->bindValue(5, $update_date, PDO::PARAM_STR);
                            $stmt->execute();
                        } catch (PDOException $e) {
                            $error[] = 'cartsへ書き込みできませんでした。理由：' . $e->getMessage();
                            throw $e;
                        }
                    }
                }
            //在庫変更が押されたとき
            } else if ($kind === '変更') {
                if (isset($_POST['amount_change']) === TRUE) {
                    $datetime = date('Y-m-d H:i:s');
                    $carts_id = $_POST['carts_id'];
                    $item_id = $_POST['item_id'];
                    //個数入力チェック
                    $amount_change = $_POST['amount_change'];
                    $amount_change = preg_replace('/^[ 　]+/', '', $amount_change);
                    $amount_change = preg_replace('/[ 　]+$/', '', $amount_change);
                    $amount_change_length = mb_strlen($amount_change);
                    if ($amount_change_length === 0) {
                        $error[] = '個数を入力して下さい。';
                    } else if (preg_match('/^([1-9][0-9]*)$/', $amount_change) !== 1) {
                        $error[] = '個数は1以上の整数を入力して下さい。';
                    } else if (count($error) === 0) {
                        //ストック十分あるか照合
                        try {
                            $sql = 'SELECT stock, name FROM items WHERE item_id = ?';
                            $stmt = $dbh->prepare($sql);
                            $stmt->bindValue(1, $item_id, PDO::PARAM_INT);
                            $stmt->execute();
                            $amount_change_stock_data = $stmt->fetch();
                            $amount_change_stock = $amount_change_stock_data['stock'];
                            $amount_change_name = $amount_change_stock_data['name'];
                        } catch (PDOException $e) {
                            $error[] =  '在庫データ読み込みできませんでした。理由：' . $e->getMessage();
                            throw $e;
                        }
                        //足りないときのエラー
                        if ($amount_change > $amount_change_stock) {
                            $error[] = '申し訳ございません。' . $amount_change_name . 'の在庫が足りません。';
                        //エラーなければ個数update    
                        } else if (count($error) === 0) {
                            $datetime = date('Y-m-d H:i:s');
                            try {
                                $sql = 'UPDATE carts SET amount= ?, update_date= ? WHERE carts_id = ?';
                                $stmt = $dbh->prepare($sql);
                                $stmt->bindValue(1, $amount_change, PDO::PARAM_INT);
                                $stmt->bindValue(2, $datetime, PDO::PARAM_STR);
                                $stmt->bindValue(3, $carts_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $succeed[] = '個数変更が完了しました。';
                            } catch (PDOException $e) {
                                $error[] =  '個数変更できませんでした。理由：' . $e->getMessage();
                            }
                        }
                    }
                } else {
                    $error[] = '不正な操作です。';
                }
            } else if ($kind === '削除') {
                $carts_id = $_POST['carts_id'];
                try {
                    $sql = 'DELETE FROM carts WHERE carts_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $carts_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $succeed[] = '商品削除が完了しました。';
                } catch (PDOException $e) {
                    $error[] =  '商品削除できませんでした。理由：' . $e->getMessage();
                }
            }
        } 
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
            $sql = 'SELECT carts.carts_id, carts.user_id, carts.item_id, carts.amount, items.name, items.price, items.img 
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
        <title>Fika Sweden ショッピングカート</title>
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
            .amount {
                width: 40px;
            }
            table {
                border-collapse: collapse;
                margin: 0px auto;
            }
            td, th{
                border: 1px solid black;
                margin: auto auto;
            }
            img {
                vertical-align: middle;
            }
            .error {
                color: red;
            }
            .succeed {
                color: blue;
            }
            .total {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <header class = "container">
            <h1>Fika Sweden</h1>
        </header>
        <main class = "container contents">
            <?php if (($_SERVER['REQUEST_METHOD']) === 'POST') { ?>
            <p><?php print $user_name; ?>さんのショッピングカートです。</p>
            <?php if (count($error) === 0) { ?>
                <?php foreach ($succeed as $read) { ?>
                    <p class = "succeed"><?php print $read;?></p>
                <?php } ?>
            <?php } ?>
            <?php foreach ($error as $print) { ?>
                <p class = "error"><?php print $print;?></p>
            <?php } ?>
            <table>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>個数</th>
                    <th>合計金額</th>
                    <th>削除</th>
                </tr>
                <?php foreach($rows as $row) { ?>
                <tr>
                    <form method = "post">
                        <td><img src = "<?php print $img_dir . htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <td><?php print htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                        <td><input class = "amount" type = "number" name = "amount_change" value = <?php print htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8'); ?>>個<input type = "submit" name = "submit" value = "変更"></td>
                        <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') * htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                        <input type = "hidden" name = "carts_id" value = <?php print htmlspecialchars($row['carts_id'], ENT_QUOTES, 'UTF-8'); ?>>
                        <input type = "hidden" name = "item_id" value = <?php print htmlspecialchars($row['item_id'], ENT_QUOTES, 'UTF-8'); ?>>
                        <td><input type = "submit" name ="submit" value = "削除"></td>
                    </form>
                </tr>
                <?php 
                    $total += htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') * htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8');
                } ?>
                
            </table>
            <p class = "total">合計金額<?php print $total; ?>円</p>
            <?php } else { ?>
                <p><?php print '不正な操作です。'; ?></p>
            <?php } ?>
            <form method = "post" action = "./finish_shopping.php" >
                <p><input type = "submit" name ="submit" value = "商品を購入する"></p>
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