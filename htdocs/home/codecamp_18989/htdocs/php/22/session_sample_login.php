<?php
/*
*  ログイン処理
*
*  セッションの仕組み理解を優先しているため、本来必要な処理も省略しています
*/
// リクエストメソッド確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  // POSTでなければログインページへリダイレクト
  header('Location: session_sample_top.php');
  exit;
}
// セッション開始
session_start();
// POST値取得
$email  = get_post_data('email');  // メールアドレス
$passwd = get_post_data('passwd'); // パスワード
// メールアドレスをCookieへ保存
setcookie('email', $email, time() + 60 * 60 * 24 * 365);
// ユーザIDの取得（本来、データベースからメールアドレスとパスワードに応じたユーザIDを取得しますが、今回は省略しています）
$data[0]['user_id'] = 'codetaro';
// 登録データを取得できたか確認
if (isset($data[0]['user_id'])) {
  // セッション変数にuser_idを保存
  $_SESSION['user_id'] = $data[0]['user_id'];
  // ログイン済みユーザのホームページへリダイレクト
  header('Location: session_sample_home.php');
  exit;
} else {
  // ログインページへリダイレクト
  header('Location: session_sample_top.php');
  exit;
}
//POSTデータから任意データの取得
function get_post_data($key) {
  $str = '';
  if (isset($_POST[$key])) {
    $str = $_POST[$key];
  }
  return $str;
}
?>