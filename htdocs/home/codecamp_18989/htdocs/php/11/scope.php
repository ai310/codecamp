<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>スコープ</title>
  </head>
  <body>
    <?php
    $str = 'スコープテスト'; // 関数外で変数定義(グローバル変数)
 
    function test_scope() {
      print $str; // 関数内の変数を参照
    }
 
    test_scope();
    ?>
  </body>
</html>