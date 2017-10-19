<?php
// 設定ファイル読み込み
require_once './conf/setting.php';
// 関数ファイル読み込み
require_once './model/model.php';
 
$goods_data = array();
$err_msg    = array();
 
try {
  // DB接続
  $dbh = get_db_connect();
 
  // 商品の一覧を取得
  $goods_data = get_goods_table_list($dbh);
 
  // 商品の値段を税込みに変換
  $goods_data = price_before_tax_assoc_array($goods_data);
 
  // 特殊文字をHTMLエンティティに変換
  $goods_data = entity_assoc_array($goods_data);
 
} catch (Exception $e) {
  $err_msg[] = $e->getMessage();
}
 
// 商品一覧テンプレートファイル読み込み
include_once './view/view.php';
 
?>