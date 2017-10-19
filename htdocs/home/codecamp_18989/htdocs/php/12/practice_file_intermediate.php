<?php
$filename = './tokyo.csv';
$data = '';
$csv  = array();

if(is_readable($filename)) {
    if(($fp = fopen($filename, 'r')) !== FALSE) {
        while (($data = fgetcsv($fp)) !== FALSE) {
            $csv[] = $data;
        }
fclose($fp);
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
        <p>以下にファイルから読み込んだ住所データを表示</p>
        <p>住所データ</p>
        <table border=1>
            <tr>
                <th>郵便番号</th>
                <th>都道府県</th>
                <th>市区町村</th>
                <th>町域</th>
            </tr>
            <?php foreach($csv as $row) { ?>
            <tr>
                <td><?php print $row[2] ?></td>
                <td><?php print $row[6] ?></td>
                <td><?php print $row[7] ?></td>
                <td><?php print $row[8] ?></td>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>
