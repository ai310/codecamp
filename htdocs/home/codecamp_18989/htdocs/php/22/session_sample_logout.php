<?php
/*
*  ログアウト処理
*
*  セッションの仕組み理解を優先しているため、本来必要な処理も省略しています
*/
// セッション開始
session_start();
// セッション名取得 ※デフォルトはPHPSESSID
$session_name = session_name();
// セッション変数を全て削除
$_SESSION = array();
// ユーザのCookieに保存されているセッションIDを削除
if (isset($_COOKIE[$session_name])) {
  setcookie($session_name, '', time() - 42000);
}
// セッションIDを無効化
session_destroy();
// ログアウトの処理が完了したらログインページへリダイレクト
header('Location: session_sample_top.php');
exit;
?>