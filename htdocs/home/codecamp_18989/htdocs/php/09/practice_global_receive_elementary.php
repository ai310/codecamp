<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>名前</title>
    </head>
    <body>
    <?php
    $my_name = '';
    if (isset ($_POST['my_name']) === TRUE) {
        $my_name = $_POST['my_name'];
    }
    if ($my_name !== '') {
        print 'ようこそ'. htmlspecialchars($_POST['my_name'], ENT_QUOTES, 'UTF-8');
    } else {
        print '名前を入力してください';
    }
    ?>
    </body>
</html>