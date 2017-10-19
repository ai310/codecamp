<?php

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
        try {
            $sql = 'SELECT user_id, item_id, name, amount, price, create_date FROM history';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } catch (PDOException $e) {
            $error[] =  'ユーザーデータ読み込みできませんでした。理由：' . $e->getMessage();
            throw $e;
        }
} catch (PDOException $e) {
    $error[] = 'DBへ接続できません。理由:' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>購買履歴ページ</title>
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
            }
            table {
                border-collapse: collapse;
            }
            td, th{
                border: 1px solid black;
                line-height: 35px;
                padding: 5px;
            }
            .contents {
                margin: 15px auto;
                line-height: 30px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <header class = "container">
            <h1>Fika Sweden 販売履歴ページ</h1>
            <a href = "./item_registration.php">商品管理ページへ</a>
        </header>
        <main class = "container contents">
            <table>
                <tr>
                    <th>ユーザーID</th>
                    <th>商品ID</th>
                    <th>商品名</th>
                    <th>数量</th>
                    <th>価格</th>
                    <th>販売日時</th>
                </tr>
                <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><?php print htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php print htmlspecialchars($row['item_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php print htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php print htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円'; ?></td>
                    <td><?php print htmlspecialchars($row['create_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <?php } ?>
            </table>
        </main>
    </body>
</html>