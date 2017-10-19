<?php
$i = 0;
$sum = 0;
while ($i <= 100) {
    $i++;
    if ($i%3 ===0){
        $sum = $sum + $i;
    }
}
print '合計値は; ' . $sum;
?>