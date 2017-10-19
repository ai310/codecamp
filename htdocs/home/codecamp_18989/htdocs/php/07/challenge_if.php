<?php
$rand = mt_rand(1, 6);
print '<p>出た数字;'. $rand . '</p>';
if ($rand%2 === 0){
    print '偶数';
} else {
    print '奇数';
}
?>