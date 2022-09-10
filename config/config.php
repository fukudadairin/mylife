<?php
// E_WARNING(実行時の警告（重要）) 以外の全てのエラーを表示する
error_reporting(E_ALL & ~E_WARNING);

date_default_timezone_set('Asia/Tokyo');

define("DB_HOST", "localhost");
define("DB_NAME", "myMoney");
define("DB_USER", "root");
define("DB_PASSWORD", "root");