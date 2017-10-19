<?php
// 設定ファイル読み込み
require_once './conf/setting.php';

//例外処理
function get_db_connect() {
  try {
    // データベースに接続
    $dbh = new PDO(DSN, DB_USER, DB_PASSWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => DB_CHARSET));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  } catch (PDOException $e) {
    throw $e;
  }
  return $dbh;
}

function get_length($name) {
    $name = str_replace(array(" ", "　"), "", $name);
    $name_length = mb_strlen($name);
    
    return $name_length;
}

function insert($sql) {
    try {
        $stmt = $dbh -> prepare($sql);
        $stmt -> execute();
    } catch (PDOException $e) {
        throw $e;
    }
    
    return $sql;
}

function select($sql,$dbh) {
    try {
        $stmt = $dbh->prepare($sql);
        $stmt -> execute();
        $rows = $stmt -> fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
        return $rows;
}
?>