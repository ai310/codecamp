<?php
/*
*  ログイン済みユーザのホームページ
*
*  セッションの仕組み理解を優先しているため、本来必要な処理も省略しています
*/
// セッション開始
session_start();
// セッション変数からuser_id取得
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  // 非ログインの場合、ログインページへリダイレクト
  header('Location: session_sample_top.php');
  exit;
}
// ユーザ名の取得（本来、データベースからユーザIDに応じたユーザ名を取得しますが、今回は省略しています）
$data[0]['user_name'] = 'コード太郎';
// ユーザ名を取得できたか確認
if (isset($data[0]['user_name'])) {
  $user_name = $data[0]['user_name'];
} else {
  // ユーザ名が取得できない場合、ログアウト処理へリダイレクト
  header('Location: session_sample_logout.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ホーム</title>
</head>
<body>
  <p>ようこそ<?php print $user_name; ?>さん</p>
  <form action="./session_sample_logout.php" method="post">
    <input type="submit" value="ログアウト">
  </form>
</body>
</html>