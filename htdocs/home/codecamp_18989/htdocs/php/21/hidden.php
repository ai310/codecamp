<?php
if (isset($_POST['data'])) {
  $query = htmlspecialchars($_POST['data'], ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
</head>
<body>
  <form method="post">
    <input type="hidden" name="data" value="非表示のデータを表示します" >
    <input type="submit" value="送信">
  </form>
  <?php if (isset($query)) { print $query; } ?>
</body>
</html>