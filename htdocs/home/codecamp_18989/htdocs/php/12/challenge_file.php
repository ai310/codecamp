<?php
$filename = './challenge_log.txt';
$log = date('Y-m-d H:i:s');
$comment = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset ($_POST['comment']) === TRUE) {
        $comment = $_POST['comment'];
    }
    if(($fp = fopen($filename, 'a')) !== FALSE) {
        if(fwrite($fp, $log."\t".$comment."\n") === FALSE){
            print 'ファイル書き込み失敗'.$filename;
        }
        fclose($fp);
    }
}
$data = array();
if(is_readable($filename)){
    if(($fp = fopen($filename, 'r')) !== FALSE) {
        while(($tmp = fgets($fp)) !== FALSE) {
            $data[] = $tmp;
        }
        fclose($fp);
    }
} else {
    $data[] = 'ファイルがありません';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>課題</title>
</head>
<body>
    <h1>課題</h1>
    <form method = 'post'>
        <p>発言: <input type="text" name="comment"></p>
        <input type="submit" value="送信">
    </form>
    <p>発言一覧</p>
    <?php foreach($data as $read) { ?>
    <p><?php print $read ?></p>
    <?php } ?>
</body>
</html>