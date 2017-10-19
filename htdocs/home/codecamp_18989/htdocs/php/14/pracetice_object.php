<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>課題</title>
  </head>
  <body>
    <?php
    // Catクラス定義
    class Cat {
      public $name;
      public $height;
      public $weight;
      function show() {
        print "{$this->name}の身長は{$this->height}cm、体重は{$this->weight}kgです。<br>";
      }
    }
 
    // $toranekoインスタンス
    $toraneko = new Cat();
 
    // この下に記述
    $toraneko ->name = 'たま';
    $toraneko ->height = 80;
    $toraneko ->weight = 30;
    $toraneko ->show();
    ?>
  </body>
</html>