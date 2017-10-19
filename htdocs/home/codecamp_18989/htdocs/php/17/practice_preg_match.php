<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>正規表現課題</title>
  </head>
  <body>
    <?php
    // 半角数字であるどうかのチェックする検索するパターンを記述
    $subject = '15000';
    // $subject = '0-0';
    $pattern = '/^[0-9]+$/';  // 左に検索するパターンを記述してください
    if (preg_match($pattern, $subject) ) {
      print $subject.'は、半角数字です。';
    } else {
      print $subject.'は、半角数字ではありません。';
    }
    print '<br>';
 
    // 半角英字であるどうかのチェックする検索するパターンを記述
    $subject = 'CodeCamp';
    $pattern = '/^[a-zA-Z]+$/';  // 左に検索するパターンを記述してください
    if (preg_match($pattern, $subject) ) {
      print $subject.'は、半角英字です。';
    } else {
      print $subject.'は、半角英字ではありません。';
    }
    print '<br>';
 
    // 郵便番号のチェック
    $subject = '160-0023';
    $pattern = '/^[0-9]{3}-[0-9]{4}$/';  // 左に検索するパターンを記述してください
    if (preg_match($pattern, $subject) ) {
      print $subject.'は、郵便番号の形式です。';
    } else {
      print $subject.'は、郵便番号の形式ではありません。';
    }
    print '<br>';
    ?>
  </body>
</html>