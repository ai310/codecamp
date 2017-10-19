<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Session</title>
  </head>
  <body>
    <?php
    session_start();
    print 'セッション名: ' . session_name() . "<br>";
    print 'セッションID: ' . session_id() .  "<br>";
    if (isset($_SESSION['count'])) {
       $_SESSION['count']++;
    } else {
       $_SESSION['count'] = 1;
    }
    print $_SESSION['count'] . '回目の訪問です' .  "<br>";
    ?>
  </body>
</html>