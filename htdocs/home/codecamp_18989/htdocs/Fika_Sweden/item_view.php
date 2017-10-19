<?php
$user_id = '';
$img_dir = './img/';
$rows = array();
$category = array('塩系の食べ物','甘い食べ物','アルコール飲料','ノンアルコール飲料');

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
    //商品データ読み取り
    try {
        $sql = 'SELECT item_id, name, price, stock, recommendation_id, comment, type, status, img, create_date FROM items WHERE status = 1 ORDER BY create_date DESC';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error[] =  '商品データ読み込みできませんでした。理由：' . $e->getMessage();
        throw $e;
    }
    //submitされたら
    if (isset($_POST['submit']) === TRUE) {
        $kind = $_POST['submit'];
        //タイプ別検索がおされたとき
        if ($kind === 'タイプ別検索') {
            $type = $_POST['type'];
            //商品データ読み取り
            try {
                $sql = 'SELECT item_id, name, price, stock, recommendation_id, comment, status, img, create_date FROM items WHERE status = 1 AND type =? ORDER BY create_date DESC';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $type, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();
            } catch (PDOException $e) {
                $error[] =  '商品データ読み込みできませんでした。理由：' . $e->getMessage();
                throw $e;
            }
        //全商品を表示がおされたとき
        } else if ($kind === '全商品を表示') {
            //商品データ読み取り
            try {
                $sql = 'SELECT item_id, name, price, stock, recommendation_id, comment, type, status, img, create_date FROM items WHERE status = 1 ORDER BY create_date DESC';
                $stmt = $dbh->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll();
            } catch (PDOException $e) {
                $error[] =  '商品データ読み込みできませんでした。理由：' . $e->getMessage();
                throw $e;
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
        <title>Fika Sweden 商品一覧</title>
        <link rel="stylesheet" href="html5reset-1.6.1.css">
        <style>
            body {
                min-width: 1200px;
                margin: 15px 0px;
            }
            .header {
                display: flex;
            }
            .header-right {
                margin: auto 0px auto auto;
                display: flex;
                
            }
            .container {
                width: 1200px;
                margin: 0 auto;
            }
            .per_item {
                flex: 1;
                text-align: center;
                width: 200px;
                height: 320px;
            }
            .all-items {
                display: flex;
                flex-wrap: wrap; 
            }
            .contents {
                margin: 15px auto;
                line-height: 30px
            }
            .footer {
                text-align: center;
                line-height: 20px
            }
            .last-content {
                margin-bottom: 20px;
            }
            h1 {
                font-size: 40px;
            }
            h2 {
                font-size: 25px;
                margin: 15px 0px;
            }
            .pics {
                display: flex;
            }
            .eachpic {
                flex: 1;
                width: 600px;
                height: 380px;
            }
            .middle {
                margin: auto 0px;
            }
            .amount {
                width: 40px;
            }
            .latest-news {
                overflow: auto;
                width: 1200px;
                height: 100px;
                border: 1px #c0c0c0 solid;
            }
        </style>
    </head>
    <body>
        <header class = "container header">
            <h1>Fika Sweden</h1>
                <div class = "header-right">
                    <p class = "middle">ユーザー名:<?php print $user_name; ?>さん</p>
                    <form class = "middle" method = "post" action = "./user_info.php"> 
                        <input type = "submit" name = "submit" value = "ユーザー情報"> 
                    </form>
                    <form method = "post" action = "./cart.php"> 
                        <input type = "image" src = "cart.png"> 
                    </form> 
                    <a class = "middle" href = "./logout.php">ログアウト</a>
                </div>
        </header>
        <main>
            <article class = "container">
                <h2>ようこそ!Välkommen!</h2>
                <div class = "pics">
                    <img class = "eachpic" src = "./toppage_img/people-2583873_640.jpg">
                    <img class = "eachpic" src = "./toppage_img/midsummer-2263200_640.jpg">
                </div>
                <div class = "contents">
                    <p>Fika Swedenは、「北欧を感じよう」をコンセプトとし、スウェーデンの食材や飲料を販売しています。</p>
                    <p>店名の一部である「Fika(フィーカ)」は、スウェーデン語で、「コーヒーとお菓子を片手に休憩をする」という意味があります。</p>
                    <p>日本語では「お茶をする」が近いかと思いますが、スウェーデンでFikaは、彼らの生活習慣の一部で、コミュニケーションの一部として大切にされている文化です。</p>
                    <p>決して大げさではなく、スウェーデンではFikaの無い1日というのは考えられないそうです。スウェーデンに住む人々には「落ち着いた時間を作る」「休憩は必ずとる」意識が根付いており、誰かとともにコーヒーを飲む時間というのは、なくてはならない絶対のものなのです。</p>
                    <p>毎日忙しいあなたも、北欧の食材や飲み物を片手に、ホッと一息ついてみませんか？</p>
                </div>
            </article>
            <article class = "container">
                <h2>最新情報</h2>
                <div class = "contents latest-news">
                    <?php foreach ($rows as $row) { ?>
                        <p><?php print date('Y/m/d', strtotime(htmlspecialchars($row['create_date'], ENT_QUOTES, 'UTF-8'))) . '    ' .  htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . 'を追加しました。'; ?></p>
                    <?php } ?>
                </div>
            </article>
            <article class = "container last-content">
                <h2>商品一覧</h2>
                    <div class = "contents">
                    <p>・商品がよくわからない時は、"商品詳細へ"をクリック！商品詳細ページにとびます。</p>
                    <p>・タイプ別の商品検索は、下記からどうぞ。
                    <form method = "post">
                        <?php foreach ($category as $key => $value) { ?>
                            <input type = "radio" name = "type" value = <?php print $key; ?>><?php print $value; ?>
                        <?php } ?>
                        <input type = "submit" name = "submit" value = "タイプ別検索">
                        <input type = "submit" name = "submit" value = "全商品を表示"></p>
                    </form>
                </div>
                <div class = "all-items">
                    <?php foreach ($rows as $row) { ?>
                    <div class = 'per_item'>
                        <form method = "post" action = "./cart.php">
                            <img src = "<?php print $img_dir . htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8'); ?>">
                            <p><?php print htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p><?php print htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '円'; ?></p>
                            <input type = "hidden" name = "which_item" value = <?php print htmlspecialchars($row['item_id'], ENT_QUOTES, 'UTF-8'); ?>>
                            <?php if (($row['stock']) === "0") { ?>
                                <?php print '売り切れ'; ?>
                            <?php } else { ?>
                                <p><input class = "amount" type = "number" name = "amount" placeholder = "0">個</p>
                                <input type = "submit" name = "submit" value = "カートに入れる">
                            <?php } ?>
                        </form>
                        <a href = "item_description.php?item_id=<?php print $row['item_id']; ?>"><button>商品詳細へ</button></a>
                    </div>
                    <?php } ?>
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