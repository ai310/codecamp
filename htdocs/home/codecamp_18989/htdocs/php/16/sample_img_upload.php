<?php
$host     = 'localhost';
$username = 'codecamp18989';   // MySQLのユーザ名
$password = 'YUHOKMAH';       // MySQLのパスワード
$dbname   = 'codecamp18989';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$charset  = 'utf8';   // データベースの文字コード
 
// MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
 
$img_dir    = './img/';    // アップロードした画像ファイルの保存ディレクトリ
$data       = array();
$err_msg    = array();     // エラーメッセージ
$new_img_filename = '';   // アップロードした新しい画像ファイル名
 
// アップロード画像ファイルの保存
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // HTTP POST でファイルがアップロードされたかどうかチェック
  if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
    // 画像の拡張子を取得
    $extension = pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION);
    // 指定の拡張子であるかどうかチェック
    if ($extension === 'jpg' || $extension === 'jpeg') {
      // 保存する新しいファイル名の生成（ユニークな値を設定する）
      $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
      // 同名ファイルが存在するかどうかチェック
      if (is_file($img_dir . $new_img_filename) !== TRUE) {
        // アップロードされたファイルを指定ディレクトリに移動して保存
        if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {
            $err_msg[] = 'ファイルアップロードに失敗しました';
        }
      } else {
        $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';
      }
    } else {
      $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEGのみ利用可能です。';
    }
  } else {
    $err_msg[] = 'ファイルを選択してください';
  }
}
 
// アップロードした新しい画像ファイル名の登録、既存の画像ファイル名の取得
try {
  // データベースに接続
      $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 
  // エラーがなければ、アップロードした新しい画像ファイル名を保存
  if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    try {
      // SQL文を作成
      $sql = 'INSERT INTO test_upload_img(img_file_name) VALUES(?)';
      // SQL文を実行する準備
      $stmt = $dbh->prepare($sql);
      // SQL文のプレースホルダに値をバインド
      $stmt->bindValue(1, $new_img_filename,    PDO::PARAM_STR);
       // SQLを実行
      $stmt->execute();
    } catch (PDOException $e) {
      throw $e;
    }
  }
 
  // 既存のアップロードされた画像ファイル名の取得
  try {
    // SQL文を作成
    $sql = 'SELECT img_file_name FROM test_upload_img';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();
    // 1行ずつ結果を配列で取得
    foreach ($rows as $row) {
      $data[] = $row;
    }
  } catch (PDOException $e) {
    throw $e;
  }
} catch (PDOException $e) {
  // 接続失敗した場合
  $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
}
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>画像アップロード</title>
  <style>
    table {
      width: 660px;
      border-collapse: collapse;
    }
    table, tr, th, td {
      border: solid 1px;
      padding: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
<?php foreach ($err_msg as $value) { ?>
  <p><?php print $value; ?></p>
<?php } ?>
  <h1>画像アップロード</h1>
  <form method="post" enctype="multipart/form-data">
    <div><input type="file" name="new_img"></div>
    <div><input type="submit" value="アップロード"></div>
  </form>
  <table>
    <tr>
      <th>画像</th>
    </tr>
<?php foreach ($data as $value)  { ?>
  <tr>
    <td><img src="<?php print $img_dir . $value['img_file_name']; ?>"></td>
  <tr>
<?php } ?>
  </table>
</body>
</html>