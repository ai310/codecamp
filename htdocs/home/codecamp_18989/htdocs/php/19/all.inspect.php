<?php
//setting
    // データベースの接続情報
    define('DB_USER',   'codecamp18989');    // MySQLのユーザ名
    define('DB_PASSWD', 'YUHOKMAH');    // MySQLのパスワード
    define('DB_NAME', 'codecamp18989');  // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
    define('DB_CHARSET', 'SET NAMES utf8mb4');  // MySQLのcharset
    define('DSN', 'mysql:dbname='.DB_NAME.';host=localhost;charset=utf8');  // データベースのDSN情報
     
    define('TAX', 1.08);  // 消費税
     
    define('HTML_CHARACTER_SET', 'UTF-8');  // HTML文字エンコーディング

//controler
    // 設定ファイル読み込み
    require_once './conf/setting.php';
    // 関数ファイル読み込み
    require_once './model/model.php';
     
    $goods_data = array();
    $err_msg    = array();
     
    try {
      // DB接続
      $dbh = get_db_connect();
     
      // 商品の一覧を取得
      $goods_data = get_goods_table_list($dbh);
     
      // 商品の値段を税込みに変換
      $goods_data = price_before_tax_assoc_array($goods_data);
     
      // 特殊文字をHTMLエンティティに変換
      $goods_data = entity_assoc_array($goods_data);
     
    } catch (Exception $e) {
      $err_msg[] = $e->getMessage();
    }
     
    // 商品一覧テンプレートファイル読み込み
    include_once './view/view.php';
 
//model
    /**
    * 税込み価格へ変換する(端数は切り上げ)
    * @param str  $price 税抜き価格
    * @return str 税込み価格
    */
    function price_before_tax($price) {
     
      return ceil($price * TAX);
    }
     
    /**
    * 商品の値段を税込みに変換する(配列)
    * @param array  $assoc_array 税抜き商品一覧配列データ
    * @return array 税込み商品一覧配列データ
    */
    function price_before_tax_assoc_array($assoc_array) {
     
      foreach ($assoc_array as $key => $value) {
        // 税込み価格へ変換(端数は切り上げ)
        $assoc_array[$key]['price'] = price_before_tax($assoc_array[$key]['price']);
      }
     
      return $assoc_array;
    }
     
    /**
    * 特殊文字をHTMLエンティティに変換する
    * @param str  $str 変換前文字
    * @return str 変換後文字
    */
    function entity_str($str) {
     
      return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
    }
     
    /**
    * 特殊文字をHTMLエンティティに変換する(2次元配列の値)
    * @param array  $assoc_array 変換前配列
    * @return array 変換後配列
    */
    function entity_assoc_array($assoc_array) {
     
      foreach ($assoc_array as $key => $value) {
        foreach ($value as $keys => $values) {
          // 特殊文字をHTMLエンティティに変換
          $assoc_array[$key][$keys] = entity_str($values);
        }
      }
     
      return $assoc_array;
    }
 
/**
* DBハンドルを取得
* @return obj $dbh DBハンドル
*/
function get_db_connect() {
 
  try {
    // データベースに接続
    $dbh = new PDO(DSN, DB_USER, DB_PASSWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => DB_CHARSET));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  } catch (PDOException $e) {
    throw $e;
  }
 
  return $dbh;
}
 
/**
* クエリを実行しその結果を配列で取得する
*
* @param obj  $dbh DBハンドル
* @param str  $sql SQL文
* @return array 結果配列データ
*/
function get_as_array($dbh, $sql) {
 
  try {
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();
  } catch (PDOException $e) {
    throw $e;
  }
 
  return $rows;
}
 
/**
* 商品の一覧を取得する
*
* @param obj $dbh DBハンドル
* @return array 商品一覧配列データ
*/
function get_goods_table_list($dbh) {
 
  // SQL生成
  $sql = 'SELECT name, price FROM test_products';
  // クエリ実行
  return get_as_array($dbh, $sql);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>商品一覧</title>
  <style type="text/css">
    table, td, th {
      border: solid black 1px;
    }
    table {
      width: 200px;
    }
    caption {
      text-align: left;
    }
  </style>
</head>
<body>
<?php foreach ($err_msg as $value) { ?>
  <p><?php print $value; ?></p>
<?php } ?>
  <table>
  <caption>商品一覧(税込み)</caption>
    <tr>
      <th>商品名</th>
      <th>価格</th>
    <tr>
<?php foreach ($goods_data as $value) { ?>
    <tr>
      <td><?php print $value['name']; ?></td>
      <td><?php print $value['price']; ?></td>
    </tr>
<?php } ?>
  </table>
 
</body>
</html>