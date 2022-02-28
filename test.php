<?php
echo "<pre>";

$MCjson = file_get_contents("myLife.json");
$MCjson = mb_convert_encoding($MCjson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$MCobj = json_decode($MCjson,true);

var_dump($MCobj);
// echo $MCobj['Category'];
echo $MCobj['Category'][0]['1日目'];   

echo "</pre>";
?>

<!-- 
<script>
    var sample = JSON.parse('');
    jsonをparseしてJavaScriptの変数に代入
    console.log(sample, "sample");
</script> -->