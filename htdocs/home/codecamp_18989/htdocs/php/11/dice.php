<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>サイコロ</title>
  </head>
  <body>
    <pre>
    <?php
 
    print 'サイコロを1個振る:' . throw_dice(1) . "\n";
    print 'サイコロを1個振る:' . throw_dice(1) . "\n";
    print 'サイコロを2個振る:' . throw_dice(2) . "\n";
    print 'サイコロを2個振る:' . throw_dice(2) . "\n";
    print 'サイコロを3個振る:' . throw_dice(3) . "\n";
    print 'サイコロを3個振る:' . throw_dice(3) . "\n";
 
    function throw_dice($num) {
        $sum = 0;
        for ($i = 0; $i < $num; $i++) {
            $sum += mt_rand(1,6);
        }
        return $sum;
    }
    ?>
    </pre>
  </body>
</html>