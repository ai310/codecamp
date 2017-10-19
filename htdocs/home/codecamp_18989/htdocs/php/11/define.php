<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>定数</title>
  </head>
  <body>
    <?php
    define('TAX', 1.05);  // 消費税
    define('TAX', 1.08);  // 消費税変更
     
    $price = 100;
     
    print $price . '円の税込み価格は' . $price * TAX . '円です';
    ?>
  </body>
</html>