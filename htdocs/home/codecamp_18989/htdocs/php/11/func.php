<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>ユーザー定義関数</title>
  </head>
  <body>
    <pre>
    <?php
    print_hello();
    print_hello_name('山田');
    $return_hello_name = return_hello_name('鈴木');
 
    print $return_hello_name . "\n";
 
    function print_hello() {
      print 'print_hello関数: hello' . "\n";
    }
    function print_hello_name($name) {
      print 'print_hello_name関数: hello ' . $name . "\n";
    }
    function return_hello_name($name) {
      $str = 'return_hello_name関数: hello ' . $name . "\n";
      return $str;
    }
    ?>
    </pre>
  </body>
</html>