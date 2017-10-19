<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>商品一覧</title>
  <style type="text/css">
    table, td, th {
      border: solid black 1px;
    }
    table {
      width: 200px;
    }
    caption {
      text-align: left;
    }
  </style>
</head>
<body>
<?php foreach ($err_msg as $value) { ?>
  <p><?php print $value; ?></p>
<?php } ?>
  <table>
  <caption>商品一覧(税込み)</caption>
    <tr>
      <th>商品名</th>
      <th>価格</th>
    <tr>
<?php foreach ($goods_data as $value) { ?>
    <tr>
      <td><?php print $value['name']; ?></td>
      <td><?php print $value['price']; ?></td>
    </tr>
<?php } ?>
  </table>
 
</body>
</html>