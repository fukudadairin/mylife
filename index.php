<?php
echo "<pre>";
require_once(dirname(__FILE__) . "/config/config.php");
require_once(dirname(__FILE__) . "/function.php");

$pdo  = connect_db();



// 年月セレクトで選択された月を取得、選択されていない状態だと当年当月が選択される
if (isset($_GET["y"])) {
    $target_yyyy = $_GET["y"];
}else{
    $target_yyyy = date("Y");
}

if (isset($_GET["m"])) {
    $target_mm = $_GET["m"];
} else {
    $target_mm = date('m');
}
$target_yyyymm = $target_yyyy."-".$target_mm;

// 一番登録が古い年月を取得
$sql = "SELECT date FROM budget order by date limit 1 ";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$most_oldDate = $stmt->fetch();
$oldYear = date("Y", strtotime($most_oldDate["date"]));
$oldMonth = date("m", strtotime($most_oldDate["date"]));

// 変動費の合計
$sql = "SELECT sum(budget_Amount) FROM budget WHERE YEAR(date) = :year AND MONTH(date) = :month ";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$variable_total = $stmt->fetch();
$variable_total = $variable_total["sum(budget_Amount)"];

// 固定費の合計
$sql = "SELECT (DC + NISA + house_cost) FROM budget_fixed";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fixed_total = $stmt->fetch();
$fixed_total = $fixed_total["(DC + NISA + house_cost)"];



// 特定月>各項目毎の合計を出す
$sql = "SELECT budget_item,sum(budget_Amount) FROM budget WHERE YEAR(date) = :year AND MONTH(date) = :month GROUP BY budget_item "; 
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$budget_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$_SESSION["budget_Amount_sum"] = $budget_list;
$budget_Amount_sum = $_SESSION["budget_Amount_sum"];



// 年別の月カウント
$sql = "SELECT DATE_FORMAT(date, '%Y-%m') as `grouping_column` FROM budget WHERE YEAR(date) = :year GROUP BY grouping_column";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->execute();
$month_count = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$month_count = count($month_count);
var_dump($month_count);
// 各項目毎の平均を出す
$sql = "SELECT budget_item,round(avg(budget_Amount)) FROM budget  WHERE YEAR(date) = :year AND MONTH(date) = :month GROUP BY budget_item "; 
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$budget_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$_SESSION["budget_Amount_avg"] = $budget_list;
$budget_Amount_avg = $_SESSION["budget_Amount_avg"];



// var_dump($budget_list);
// var_dump($_SESSION["budget_list"][6]["budget_Amount"]);
// var_dump(array_sum($budget_list));

echo "</pre>";
?>


<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- reset CSS -->
    <link rel="stylesheet" href="./css/reset.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- original CSS -->
    <link rel="stylesheet" href="./css/style.css">

    <title>my money</title>
</head>

<body>
    <h1>my money</h1>
    <div class=" float-start px30 pt-3">
        <form>
            <div class="d-flex align-items-center">
                <select class="form-select rounded-pill mb-3 w100px" name="y">
                    <?php
                    $thisYear = date("Y");
                    for ($i = $thisYear; $i >= $oldYear; $i--) :
                    ?>
                        <option value="<?= $i ?>" <?php if ($i == $target_yyyy) echo "selected"; ?>><?= $i ?></option>
                    <?php
                    endfor;
                    ?>
                </select>

                <p class="pl5r10">年</p>

                <select class="form-select rounded-pill mb-3 w80px" name="m">

                    <?php
                    $thisYear = date("Y");
                    for ($i = 01; $i <= 12; $i++) :
                    ?>
                        <option value="<?= date("m", strtotime($thisYear."-".$i)) ?>" <?php if ($i == $target_mm) echo "selected"; ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>



                <p class="pl5r10">月</p>
                <button class="btn textWhite rounded-pill btnLayout mb-3 bgc_update_btn" type="submit">更新</button>

            </div>

        </form>
    </div>
    <div class="p30">
        <form method="POST">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">総合計</th>
                        <th scope="col">変動費</th>
                        <th scope="col">固定費</th>
                        <th scope="col">食費</th>
                        <th scope="col">旅費交通費</th>
                        <th scope="col">水道ガスなど</th>
                        <th scope="col">消耗品</th>
                        <th scope="col">通信費</th>
                        <th scope="col">研修費</th>
                        <th scope="col">接待交際費</th>
                        <th scope="col">医療費</th>
                        <th scope="col">節税関係</th>
                        <th scope="col">返済</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">合計</th>

                        <td>
                            <?php
                            $budget_total = $variable_total + $fixed_total;
                            $budget_total = intval($budget_total);
                            echo number_format($budget_total) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $variable_total = intval($variable_total);
                            echo number_format($variable_total) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $fixed_total = intval($fixed_total);
                            echo number_format($fixed_total) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_syokuhi = $budget_Amount_sum["食費"]["sum(budget_Amount)"];
                            echo number_format($sum_syokuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_ryohikoutuhi = $budget_Amount_sum["旅費交通費"]["sum(budget_Amount)"];
                            echo number_format($sum_ryohikoutuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_suidougasu = $budget_Amount_sum["水道・ガス・光熱費"]["sum(budget_Amount)"];
                            echo number_format($sum_suidougasu) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_syoumouhin = $budget_Amount_sum["消耗品"]["sum(budget_Amount)"];
                            echo number_format($sum_syoumouhin) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_tusinhi = $budget_Amount_sum["通信費"]["sum(budget_Amount)"];
                            echo number_format($sum_tusinhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_kensyuuhi = $budget_Amount_sum["研修費"]["sum(budget_Amount)"];
                            echo number_format($sum_kensyuuhi) . "円";
                            ?>
                        </td>

                        <td>
                            <?php
                            $sum_kousaihi = $budget_Amount_sum["接待交際費"]["sum(budget_Amount)"];
                            echo number_format($sum_kousaihi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_iryouhi = $budget_Amount_sum["医療費"]["sum(budget_Amount)"];
                            echo number_format($sum_iryouhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_setuzei = $budget_Amount_sum["節税関係"]["sum(budget_Amount)"];
                            echo number_format($sum_setuzei) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $sum_hensai = $budget_Amount_sum["返済"]["sum(budget_Amount)"];
                            echo number_format($sum_hensai) . "円";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">平均</th>
                        <td>---</td>
                        <td>1,000</td>
                        <td>---</td>
                        <td>
                            <?php
                            $avg_syokuhi = $budget_Amount_avg["食費"]["round(avg(budget_Amount))"];
                            echo number_format($avg_syokuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_ryohikoutuhi = $budget_Amount_avg["旅費交通費"]["round(avg(budget_Amount))"];
                            echo number_format($avg_ryohikoutuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_suidougasu = $budget_Amount_avg["水道・ガス・光熱費"]["round(avg(budget_Amount))"];
                            echo number_format($avg_suidougasu) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_syoumouhin = $budget_Amount_avg["消耗品"]["round(avg(budget_Amount))"];
                            echo number_format($avg_syoumouhin) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_tusinhi = $budget_Amount_avg["通信費"]["round(avg(budget_Amount))"];
                            echo number_format($avg_tusinhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_kensyuuhi = $budget_Amount_avg["研修費"]["round(avg(budget_Amount))"];
                            echo number_format($avg_kensyuuhi) . "円";
                            ?>
                        </td>

                        <td>
                            <?php
                            $avg_kousaihi = $budget_Amount_avg["接待交際費"]["round(avg(budget_Amount))"];
                            echo number_format($avg_kousaihi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_iryouhi = $budget_Amount_avg["医療費"]["round(avg(budget_Amount))"];
                            echo number_format($avg_iryouhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_setuzei = $budget_Amount_avg["節税関係"]["round(avg(budget_Amount))"];
                            echo number_format($avg_setuzei) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_hensai = $budget_Amount_avg["返済"]["round(avg(budget_Amount))"];
                            echo number_format($avg_hensai) . "円";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">目標</th>
                        <td><input class="w100" type="text"> </td>
                        <td><input class="w100" type="text"> </td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                        <td><input class="w100" type="text"></td>
                    </tr>
                    <tr>
                        <th scope="row">先月比</th>
                        <td>−30,4％</td>
                        <td>−30,4％</td>
                        <td>−30,4％</td>
                        <td>−10,4％</td>
                        <td>−30,4％</td>
                        <td>−10,4％</td>
                        <td>−30,4％</td>
                        <td>−10,4％</td>
                        <td>−30,4％</td>
                        <td>−10,4％</td>
                        <td>−10,4％</td>
                        <td>−10,4％</td>
                        <td>−10,4％</td>
                    </tr>
                </tbody>
            </table>

            <button class="btn btn-primary rounded-pill btnLayout" type="submit">更新</button>

        </form>


    </div>


    <div class="flex">
        <div class="p30 w50">
            <div class="t_content_title">クレジット：104400円</div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">出費項目</th>
                        <th scope="col">日付</th>
                        <th scope="col">詳細</th>
                        <th scope="col">金額</th>
                        <th scope="col">メモ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>

                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>

                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="p30 w50">
            <div class="t_content_title">現金：104400円</div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">出費項目</th>
                        <th scope="col">日付</th>
                        <th scope="col">詳細</th>
                        <th scope="col">金額</th>
                        <th scope="col">メモ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>接待交際費</td>
                        <td>2022年07月09日</td>
                        <td>トウキヨウデンリヨク</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>




    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>