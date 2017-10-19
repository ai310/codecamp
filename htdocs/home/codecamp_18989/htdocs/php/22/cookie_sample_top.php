<?php
if (isset($_COOKIE['cookie_check'])) {
  $cookie_check = 'checked';
} else {
  $cookie_check = '';
}
if (isset($_COOKIE['user_name'])) {
  $user_name = $_COOKIE['user_name'];
} else {
  $user_name = '';
}
$cookie_check = htmlspecialchars($cookie_check, ENT_QUOTES, 'UTF-8');
$user_name = htmlspecialchars($user_name  , ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ユーザ名自動入力</title>
  <style>
    .block {
      display: block;
      margin-bottom: 10px;
    }
    .small {
      font-size: 0.8em;
    }
   </style>
</head>
<body>
  <form action="./cookie_sample_login.php" method="post">
    <label for="user_name">ユーザ名</label>
    <input type="text" class="block" id="user_name" name="user_name" value="<?php print $user_name; ?>">
    <label for="passwd">パスワード</label>
    <input type="password" class="block" id="passwd" name="passwd" value="">
    <span class="block small">
      <input type="checkbox" name="cookie_check" value="checked" <?php print $cookie_check; ?> >次回からユーザ名の入力を省略
    </span>
    <input type="submit" value="ログイン">
  </form>
</body>
</html>