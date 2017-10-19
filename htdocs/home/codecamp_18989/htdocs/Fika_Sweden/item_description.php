<?php
$item_id = '';
$name = '';
$price = '';
$recommendation_id = '';
$comment = '';
$img = '';
$stock = '';
$down_name = '';
$down_price = '';
$down_item_id = '';
$down_comment = '';
$down_img = '';
$down_stock = '';
$row = array();
$down_row = array();
$error = array();
$img_dir    = './img/';

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
    //getで商品情報を受け取る
    if (($_SERVER['REQUEST_METHOD']) === 'GET') {
        if (isset($_GET['item_id']) === TRUE) {
        $item_id = $_GET['item_id'];
        } else {
            $error[] = "不正な操作です。";
        } 
        //メイン商品データ読み取り
        try {
            $sql = 'SELECT item_id, name, price, recommendation_id, comment, img, stock FROM items WHERE item_id = ?';
            $stmt = $dbh -> prepare($sql);
            $stmt->bindValue(1, $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
        } catch (PDOException $e) {
            $error[] =  'メイン商品データ、読み込みできませんでした。理由：' . $e -> getMessage();
            throw $e;
        }
        $item_id = $row['item_id'];
        $name = $row['name'];
        $price = $row['price'];
        $recommendation_id = $row['recommendation_id'];
        $comment = $row['comment'];
        $stock = $row['stock'];
        $img = $row['img'];
        //おすすめ商品データ読み取り
        try {
            $sql = 'SELECT item_id, name, price, comment, img, stock FROM items WHERE item_id = ?';
            $stmt = $dbh -> prepare($sql);
            $stmt->bindValue(1, $recommendation_id, PDO::PARAM_INT);
            $stmt->execute();
            $down_row = $stmt->fetch();
        } catch (PDOException $e) {
            $error[] =  'おすすめ商品データ、読み込みできませんでした。理由：' . $e -> getMessage();
            throw $e;
        }
        $down_name = $down_row['name'];
        $down_price = $down_row['price'];
        $down_item_id = $down_row['item_id'];
        $down_comment = $down_row['comment'];
        $down_stock = $down_row['stock'];
        $down_img = $down_row['img'];
    } else {
        $error[] = "不正な操作です。";
    }
} catch (PDOException $e) {
    $error[] = 'DBへ接続できません。理由:' . $e -> getMessage();
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>Fika Sweden 商品詳細</title>
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
                width: 700px;
            }
            .footer {
                text-align: center;
                line-height: 20px;
            }
            h1 {
                font-size: 40px;
            }
            h2 {
                font-size: 20px;
                margin: 15px 0px;
            }
            .amount {
                width: 40px;
            }
            .footer {
                text-align: center;
                line-height: 20px
            }
        </style>
    </head>
    <body>
        <header class = "container">
            <h1>Fika Sweden</h1>
        </header>
        <main>
            <article class = "container">
                <h2>＜商品詳細＞</h2>
                <div class = "contents">
                    <p><?php print $name; ?>の詳細は、下記をご覧下さい！</p>
                    <?php if (count($error) !== 0) { ?>
                        <?php foreach($error as $read) { ?>
                            <p><?php print $read; ?></p>
                        <?php } ?>
                    <?php } ?>
                    <form method = "post" action = "./cart.php">
                        <p><img src = "<?php print $img_dir . htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>"></p>
                        <p><?php print htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?php print htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>円</p>
                        <p><?php print htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'); ?></p>
                        <input type = "hidden" name = "which_item" value = <?php print htmlspecialchars($item_id, ENT_QUOTES, 'UTF-8'); ?>>
                        <p><input class = "amount" type = "number" name = "amount" placeholder = "0">個</p>
                        <?php if (($stock) === "0") { ?>
                            <?php print '売り切れ'; ?>
                        <?php } else { ?>
                            <input type = "submit" name = "submit" value = "カートに入れる">
                        <?php } ?>
                    </form>
                        <a href = "./item_view.php"><button>商品一覧へ</button></a>
                </div>
            </article>
            <article class = "container">
                <h2>＜おすすめ商品のお知らせ＞</h2>
                <div class = "contents">
                    <p><?php print htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>を購入した方には、こちらもおすすめです！</p>
                    <form method = "post" action = "./cart.php">
                        <p><img src = "<?php print $img_dir . htmlspecialchars($down_img, ENT_QUOTES, 'UTF-8'); ?>"></p>
                        <p><?php print htmlspecialchars($down_name, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?php print htmlspecialchars($down_price, ENT_QUOTES, 'UTF-8'); ?>円</p>
                        <p><?php print htmlspecialchars($down_comment, ENT_QUOTES, 'UTF-8'); ?></p>
                        <input type = "hidden" name = "which_item" value = <?php print htmlspecialchars($down_item_id, ENT_QUOTES, 'UTF-8'); ?>>
                        <p><input class = "amount" type = "number" name = "amount" placeholder = "0">個</p>
                        <?php if (($down_stock) === "0") { ?>
                            <?php print '売り切れ'; ?>
                        <?php } else { ?>
                            <input type = "submit" name = "submit" value = "カートに入れる">
                        <?php } ?>
                    </form>
                        <a href = "./item_view.php"><button>商品一覧へ</button></a>
                </div>
            </article>
        </main>
        <footer class = "container footer">
            <p>お問い合わせ</p>
            <p>email:fikasweden@xxxxx</p>
            <p><small>Copyright &copy; Fika Sweden All Rights Reserved.</small></p>
        </footer>
    </body>
</html>