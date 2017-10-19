<!DOCTYPE html>
<html lang="ja">
<head>
  <title></title>
  <meta charset="utf-8">
</head>
<body>
<?php
// 0〜1のランダムな数字を取得
$rand = mt_rand(1, 10);
 
// 6以上の場合
if ($rand >= 6) {
  print '当たり';
// 6未満の場合
} else {
  print 'はずれ';
}
?>
</body>
</html>