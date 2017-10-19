<?php
 
// この変数に調べたい文字を入れる
$str = 'この変数に文字列の長さを調べたい文字を自由に入力してください';
 
$length = mb_strlen($str);
 
$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
$length = htmlspecialchars($length, ENT_QUOTES, 'UTF-8');
 
?>
<!DOCTYPE html>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>mb_strlen</title>
</head>
<body>
   <p>この文字列の長さは「<?php print $length; ?>」文字です。</p>
   <p><?php print $str; ?></p>
</body>
</html>