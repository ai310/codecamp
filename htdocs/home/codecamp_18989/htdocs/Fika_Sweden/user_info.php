<?php
$user_id = '';
$user_name = '';
$create_date = '';
$rows = array();
$img_dir = './img/';

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
            //ユーザーデータ読み取り
            try {
                $sql = 'SELECT user_name, create_date FROM customers WHERE user_id = ?';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $user_data = $stmt->fetch();
                $user_name = $user_data['user_name'];
                $create_date = $user_data['create_date'];
            } catch (PDOException $e) {
                $error[] =  'ユーザーデータ読み込みできませんでした。理由：' . $e->getMessage();
                throw $e;
            }
            //historyテーブルから、購買履歴をよみとり
            try {
                $sql = 'SELECT img, name, amount, price, create_date FROM history WHERE user_id = ? ORDER BY create_date DESC';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();
            } catch (PDOException $e) {
                $error[] =  'ユーザーデータ読み込みできませんでした。理由：' . $e->getMessage();
                throw $e;
            }
        } else {
            $error[] = '不正な操作です。';
        }
    }
} catch (PDOException $e) {
    $error[] = 'DBへ接続できません。理由:' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>Fika Sweden　ユーザー情報</title>
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
            h1 {
                font-size: 40px;
                margin: 10px 0;
            }
            h2 {
                font-size: 20px;
                margin: 15px 0px;
            }
            table {
                border-collapse: collapse;
            }
            td, th{
                border: 1px solid black;
                padding: 8px;
                text-align: center;
            }
            .contents {
                margin: 15px auto;
                line-height: 40px;
            }
            .footer {
                text-align: center;
                line-height: 20px
            }
            .small-pic {
                vertical-align: middle;
            }
            .main-pic {
                width: 1200px;
            }
        </style>
    </head>
    <body>
        <header class = "container">
            <h1>Fika Sweden</h1>
            <img class = "main-pic" src = "./toppage_img/nature-941214_1280.jpg">
        </header>
        <?php if (($_SERVER['REQUEST_METHOD']) === 'POST') { ?>
        <main class = "container contents">
            <h2>ユーザー登録情報</h2>
            <p>ユーザー名:<?php print $user_name; ?></p>
            <p>登録日時:<?php print date('Y-m-d H:i', strtotime(htmlspecialchars($create_date, ENT_QUOTES, 'UTF-8'))); ?>
            <h2><?php print $user_name; ?>さんの購買履歴</h2>
            <table>
                <tr>
                    <th>日時</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>数量</th>
                </tr>
                <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><?php print date('Y-m-d H:i', strtotime(htmlspecialchars($row['create_date'], ENT_QUOTES, 'UTF-8'))); ?></td>
                    <td><img class = "small-pic" src = "<?php print $img_dir . htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                    <td><?php print htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                    <td><?php print htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . '個'; ?></td>
                </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p><?php print '不正な操作です。'; ?></p>
        <?php } ?>
            <p><a href = "./item_view.php"><button>商品一覧へ戻る</button></a></p>
        </main>
        <footer class = "container footer">
            <p>お問い合わせ</p>
            <p>email:fikasweden@xxxxx</p>
            <p><small>Copyright &copy; Fika Sweden All Rights Reserved.</small></p>
        </footer>
    </body>
</html>