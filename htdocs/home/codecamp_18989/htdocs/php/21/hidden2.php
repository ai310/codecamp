<?php
$process_kind = "";
$result_msg = "";
 
if ( isset($_POST['process_kind']) ) {
  $process_kind = $_POST['process_kind'];
}
 
// 送られてきた非表示データに応じて処理を振り分けます。
if ($process_kind === 'insert_item') {
  $result_msg = '商品を追加しました';
} else if ($process_kind === 'update_stock') {
  $result_msg = '在庫数を更新しました';
} else if ($process_kind === 'change_status') {
  $result_msg = 'ステータスを更新しました';
}
 
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
</head>
<body>
<?php if ( !empty($result_msg) ) { ?>
  <p><?php print $result_msg; ?></p>
<?php } ?>
  <h1>商品管理画面</h1>
  <div>
    <h2>新規商品の追加</h2>
    <form method="post" enctype="multipart/form-data">
      <div><label>名前: <input type="text" name="new_name" value=""></label></div>
      <div><label>値段: <input type="text" name="new_price" value=""></label></div>
      <div><label>個数: <input type="text" name="new_stock" value=""></label></div>
      <div><input type="file" name="new_img"></div>
      <input type="hidden" name="process_kind" value="insert_item">
      <div><input type="submit" value="商品追加"></div>
    </form>
  </div>
  <div>
    <h2>商品情報変更</h2>
    <table>
      <tr>
        <th>商品名</th>
        <th>価格</th>
        <th>在庫数</th>
        <th>ステータス</th>
      </tr>
      <tr>
        <form method="post">
          <td>コーラ</td>
          <td>150円</td>
          <td>
            <input type="text"  name="update_stock" value="0">個&nbsp;&nbsp;<input type="submit" value="在庫数を変更">
          </td>
          <input type="hidden" name="process_kind" value="update_stock">
        </form>
        <form method="post">
          <td><input type="submit" value="ステータスを公開から非公開に変更"></td>
          <input type="hidden" name="process_kind" value="change_status">
        </form>
      <tr>
    </table>
  </div>
</body>
</html>