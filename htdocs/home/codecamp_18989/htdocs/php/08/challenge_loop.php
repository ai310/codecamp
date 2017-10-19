<?php
for ($i = 1; $i <= 100; $i++) {
    if ($i%15 === 0){
        print '<p>FizzBuzz</p>';
    } else if ($i%3 === 0){
        print '<p>Fizz</p>';
    } else if ($i%5 === 0){
        print '<p>Buzz</p>';
    } else {
        print '<p>'.$i.'</p>';
    }
}
?>