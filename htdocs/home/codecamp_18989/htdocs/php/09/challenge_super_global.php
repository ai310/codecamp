<?php
$my_name = '';
$gender= '';
$mail = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['my_name'])){
        $my_name = htmlspecialchars($_POST['my_name'], ENT_QUOTES, 'UTF-8');
        echo '<p>ここに入力したお名前を表示: ' . $my_name . '</p>';
    }
    if(isset($_POST['gender'])){
        $gender = $_POST['gender'];
        echo '<p>ここに選択した性別を表示: ' . $gender . '</p>';
    }
    if(isset($_POST['mail'])){
        $mail = $_POST['mail'];
        echo '<p>ここにメールを受け取るか表示: '.$mail.'</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>課題</title>
</head>
<body>
  <h1>課題</h1>
  <form method="post">
      <p>お名前: <input id="my_name" type="text" name="my_name" value="" <?php if ($my_name === '') { print 'checked'; } ?>></p>
      <p>性別: <input type="radio" name="gender" value="man" <?php if ($gender === 'man') { print 'checked'; } ?>>男
      <input type="radio" name="gender" value="woman" <?php if ($gender === 'woman') { print 'checked'; } ?>>女</p>
      <p><input type="checkbox" name="mail" value="OK" <?php if ($mail === 'OK') { print 'checked'; } ?>>お知らせメールを受け取る</p>
      <input type="submit" name="submit" value="送信">
  </form>
</body>
</html>