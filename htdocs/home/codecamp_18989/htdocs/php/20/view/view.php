<!DOCTYPE html>
<html>
    <head>
        <meta charset = 'UTF-8'>
        <title>ひとこと掲示板</title>
        <style>
            .red {
                color: red;
            }
            .blue {
                color: blue;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>ひとこと掲示板</h1>
        <?php foreach($error as $read) { ?>
            <p class = 'red'><?php print $read; ?></p>
        <?php } ?>
        <form method = 'post'>
            <p>名前:<input type = "text" name = "name">
            ひとこと:<input type = "text" name = "comment">
            <input type = 'submit' name = 'submit' value = '送信'></p>
        </form>
        <?php foreach($rows as $row) { ?>
            <p><?php print '<span class = "blue">' . $row['user_name'] . '</span>' . ':' . '　　' . $row['user_comment'] . '　　' . $row['create_datetime']; ?></p>
        <?php } ?>
    </body>
</html>