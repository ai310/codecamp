<?php
$hp = 100;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>演算子の例</title>
</head>
<body>
  <p>初期のHP: <?php print $hp; ?><p>
  <p>攻撃</p>
  <?php
  $damage = mt_rand(1, 20);
  $hp = $hp - $damage;
  ?>
  <p><?php print $damage ?>のダメージ</p>
  <p>残りHP: <?php print $hp; ?></p>
  <p>追撃</p>
  <?php
  $damage = mt_rand(1, 20);
  $hp = $hp - $damage;
  ?>
  <p><?php print $damage ?>のダメージ</p>
  <p>残りHP: <?php print $hp; ?></p>
</body>
</html>