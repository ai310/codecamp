<?php
$rand1 = mt_rand(0, 2);
$rand2 = mt_rand(0, 2);
print '<p>rand1; ' . $rand1 . '</p>';
print '<p>rand2; ' . $rand2 . '</p>';
if ($rand1 === $rand2){
    print '同じ値';
} else if ($rand1 > $rand2) {
    print 'rand1が大きい';
} else {
    print 'rand2が大きい';
}
?>