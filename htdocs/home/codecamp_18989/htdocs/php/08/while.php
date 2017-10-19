<?php
$i = 1900;
$date = date('Y'); // 現在の西暦を取得
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ループの使用例</title>
</head>
<body>
  <form action="#">
    生まれ西暦を選択してください
    <select name="born_year">
    <?php
    // 1900年〜現在の西暦までをループで処理する
    while ($i <= $date) {
    ?>
      <option value="<?php print $i; ?>"><?php print $i; ?>年</option>
    <?php
      $i++;
    }
    ?>
    </select>
  </form>
</body>
</html>