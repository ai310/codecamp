<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>トランザクション</title>
  </head>
  <body>
    <?php
    $host     = 'localhost';
    $username = 'codecamp18989';   // MySQLのユーザ名
    $password = 'YUHOKMAH';       // MySQLのパスワード
    $dbname   = 'codecamp18989';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
    $charset  = 'utf8';   // データベースの文字コード
 
    // MySQL用のDSN文字列
    $dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
    try {
      // データベースに接続
      $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 
      // トランザクション開始
      $dbh->beginTransaction();
      try {
        // 現在日時を取得
        $now_date = date('Y-m-d H:i:s');
        //
        // 商品情報テーブルにデータ作成
        //
        // SQL文を作成
        $sql = 'insert into test_item_master(id, item_name, price, create_datetime) values(1, "PHP入門", 2000, "' . $now_date . '" );';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQLを実行
        $stmt->execute();
 
        //
        // 在庫情報テーブルにデータ作成
        //
        // SQL文を作成
        $sql = 'insert into test_item_stock(item_id, stock, create_datetime) values(1, 100, "' . $now_date . '" );';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQLを実行
        $stmt->execute();
 
        // コミット処理
        $dbh->commit();
        echo 'データが登録できました';
      } catch (PDOException $e) {
        // ロールバック処理
        $dbh->rollback();
        // 例外をスロー
        throw $e;
      }
    } catch (PDOException $e) {
      echo 'データベース処理でエラーが発生しました。理由：'.$e->getMessage();
    }
    ?>
  </body>
</html>