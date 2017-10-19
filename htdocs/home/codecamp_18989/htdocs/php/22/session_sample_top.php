<?php
/*
*  ログインページ
*
*  セッションの仕組み理解を優先しているため、本来必要な処理も省略しています
*/
// セッション開始
session_start();
// セッション変数からログイン済みか確認
if (isset($_SESSION['user_id'])) {
  // ログイン済みの場合、ホームページへリダイレクト
  header('Location: session_sample_home.php');
  exit;
}
// Cookie情報からメールアドレスを取得
if (isset($_COOKIE['email'])) {
  $email = $_COOKIE['email'];
} else {
  $email = '';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン</title>
  <style>
    input {
      display: block;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <form action="./session_sample_login.php" method="post">
    <label for="email">メールアドレス</label>
    <input type="text" id="email" name="email" value="<?php print $email; ?>">
    <label for="passwd">パスワード</label>
    <input type="password" id="passwd" name="passwd" value="">
    <input type="submit" value="ログイン">
  </form>
</body>
</html>