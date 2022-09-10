<?php

function connect_db()
{
    // データベースに接続
    $param = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST;
    $pdo  = new PDO($param, DB_USER, DB_PASSWORD);
    $pdo->query("SET NAMES utf8;");

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // setAttribute：データベースに接続した後にオプションを指定
    // PDO::ATTR_DEFAULT_FETCH_MODE、デフォルトのフェッチモードを設定
    // PDO::FETCH_ASSOC：連番をトル
    return $pdo;
}
function time_format_dw($date)
{
    $format_date = null;
    $week = array("日", "月", "火", "水", "木", "金", "土");
    $format_date =  "(" . $week[date("w", strtotime($date))]. ")";

    return $format_date;
}