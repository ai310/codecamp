<?php
// hp
$enemy_hp  = 20;
$player_hp = 50;
// プレイヤー名
$user_name = "ユーザー";
// 敵の攻撃
$attack_enemy  = rand(1, 9);
// プレイヤーの攻撃
$attack_player = rand(1,20);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>サンプル</title>
  <link rel="stylesheet" href="https://codecamp.jp/textbook/sample/sample.css">
</head>
<body>
  <div id="stage" style="display: none">
    <div id="status-window">
      <ul>
        <li>
          <p class="name"><?php echo $user_name; ?></p>
          <p class="hp">HP: <span id="player_hp"><?php echo $player_hp; ?></span></p>
          <p class="mp">MP: 0</p>
          <p class="lv">Lv: 30</p>
        </li>
      </ul>
    </div>
    <img id="suraimu" src="http://codecamp.lesson.codecamp.jp/sample/suraimu.png">
    <div id="suraimu-damage"><?php echo $attack_enemy; ?></div>
    <div id="player-damage"><?php echo $attack_player; ?></div>
    <div id="log-window">
      <p><span class="log" style="display :none"></span></p>
    </div>
  </div>
<script src="https://codecamp.jp/textbook/sample/sample.js" type="text/javascript"></script>
</body>
</html>