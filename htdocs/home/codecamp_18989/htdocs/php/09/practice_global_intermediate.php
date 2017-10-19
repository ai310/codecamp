<?php
$me = '';
$rival = '';
$result = '';
$janken = array('グー','チョキ','パー');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['which']) === TRUE){
        $me = $_POST['which'];
        $rival = $janken[array_rand($janken)];
        if ($me === $rival){
            $result = 'draw';
        } else if ($me === 'グー' && $rival === 'チョキ' || $me === 'チョキ' && $rival === 'パー' || $me === 'パー' && $rival === 'グー' ){
            $result = 'Win!';
        } else {
            $result = 'lose...';
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = 'UTF-8'>
        <title>課題</title>
    </head>
    <body>
        <h1>じゃんけん大会</h1>
            <p>自分: <?php print $me ?></p>
            <p>相手: <?php print $rival ?> </p>
            <p>結果: <?php print $result ?></p>
        <form method="post">
            <p>グー <input type="radio" name="which" value="グー" <?php if ($me === 'グー') { print 'checked'; } ?>>
            チョキ <input type="radio" name="which" value="チョキ" <?php if ($me === 'チョキ') { print 'checked'; } ?>>
            パー <input type="radio" name="which" value="パー" <?php if ($me === 'パー') { print 'checked'; } ?>></p>
            <input type="submit" name="submit" value="勝負!!">
        </form>
    </body>
</html>