<?php
echo "<pre>";
require_once(dirname(__FILE__) . "/config/config.php");
require_once(dirname(__FILE__) . "/function.php");

$pdo  = connect_db();



// 年月セレクトで選択された月を取得、選択されていない状態だと当年当月が選択される
if (isset($_POST["y"])) {
    $_SESSION["target_yyyy"] = $_POST["y"];
    $target_yyyy = $_SESSION["target_yyyy"];
} else {
    $_SESSION["target_yyyy"] = date("Y");
    $target_yyyy = $_SESSION["target_yyyy"];
}

if (isset($_POST["m"])) {
    $_SESSION["target_mm"] = $_POST["m"];
    $target_mm = $_SESSION["target_mm"];
} else {
    $_SESSION["target_mm"] = date('m');
    $target_mm = $_SESSION["target_mm"];
}
$_SESSION["target_yyyymm"] = $target_yyyy . "-" . $target_mm;
$target_yyyymm = $_SESSION["target_yyyymm"];
var_dump($target_yyyymm);
var_dump($target_budget_total);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION["target_budget_total"] = $_POST["target_budget_total"];
    $target_budget_total = $_SESSION["target_budget_total"];
    $_SESSION["target_variable_total"] = $_POST["target_variable_total"];
    $target_variable_total = $_SESSION["target_variable_total"];
    $_SESSION["target_fixed_total"] = $_POST["target_fixed_total"];
    $target_fixed_total = $_SESSION["target_fixed_total"];
    $_SESSION["target_syokuhi"] = $_POST["target_syokuhi"];
    $target_syokuhi = $_SESSION["target_syokuhi"];
    $_SESSION["target_ryohikoutuhi"] = $_POST["target_ryohikoutuhi"];
    $target_ryohikoutuhi = $_SESSION["target_ryohikoutuhi"];
    $_SESSION["target_suidougasu"] = $_POST["target_suidougasu"];
    $target_suidougasu = $_SESSION["target_suidougasu"];
    $_SESSION["target_syoumouhin"] = $_POST["target_syoumouhin"];
    $target_syoumouhin = $_SESSION["target_syoumouhin"];
    $_SESSION["target_tusinhi"] = $_POST["target_tusinhi"];
    $target_tusinhi = $_SESSION["target_tusinhi"];
    $_SESSION["target_kensyuuhi"] = $_POST["target_kensyuuhi"];
    $target_kensyuuhi = $_SESSION["target_kensyuuhi"];
    $_SESSION["target_kousaihi"] = $_POST["target_kousaihi"];
    $target_kousaihi = $_SESSION["target_kousaihi"];
    $_SESSION["target_iryouhi"] = $_POST["target_iryouhi"];
    $target_iryouhi = $_SESSION["target_iryouhi"];
    $_SESSION["target_setuzei"] = $_POST["target_setuzei"];
    $target_setuzei = $_SESSION["target_setuzei"];
    $_SESSION["target_hensai"] = $_POST["target_hensai"];
    $target_hensai = $_SESSION["target_hensai"];
}

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

// 1年間>各項目毎の合計を出す
$sql = "SELECT budget_item,sum(budget_Amount) FROM budget GROUP BY budget_item ";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$budget_list_year = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$_SESSION["budget_Amount_sum_year"] = $budget_list_year;
$budget_Amount_sum_year = $_SESSION["budget_Amount_sum_year"];

// 現金一覧リスト
$sql = "SELECT id,budget_item,date,budget_detail,budget_Amount,comment FROM budget WHERE YEAR(date) = :year AND MONTH(date) = :month AND cash_credit='現金' ORDER BY date ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$cash_list = $stmt->fetchAll();
$cash_list_count = count($cash_list);

// 現金の合計
$sql = "SELECT sum(budget_Amount) FROM budget WHERE YEAR(date) = :year AND MONTH(date) = :month AND cash_credit='現金'";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$cash_total = $stmt->fetch();
$cash_total = $cash_total["sum(budget_Amount)"];

// クレジット一覧リスト
$sql = "SELECT id,budget_item,date,budget_detail,budget_Amount,comment FROM budget WHERE YEAR(date) = :year AND MONTH(date) = :month AND cash_credit='クレジット'  ORDER BY date ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$credit_list = $stmt->fetchAll();
$credit_list_count = count($credit_list);

// クレジットの合計
$sql = "SELECT sum(budget_Amount) FROM budget WHERE YEAR(date) = :year AND MONTH(date) = :month AND cash_credit='クレジット'";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$credit_total = $stmt->fetch();
$credit_total = $credit_total["sum(budget_Amount)"];



// 年別の月カウント
$sql = "SELECT DATE_FORMAT(date, '%Y-%m') as `grouping_column` FROM budget WHERE YEAR(date) = :year GROUP BY grouping_column";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->execute();
$month_count = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$month_count = count($month_count);
// 各項目毎の平均を出す
$sql = "SELECT budget_item,round(avg(budget_Amount)) FROM budget  WHERE YEAR(date) = :year AND MONTH(date) = :month GROUP BY budget_item ";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$budget_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$_SESSION["budget_Amount_avg"] = $budget_list;
$budget_Amount_avg = $_SESSION["budget_Amount_avg"];

// 年別の日カウント
for ($a = 1; $a <= $month_count; $a++) :
    $day_count = "";
    $day_count = date("t", strtotime($target_yyyy . "-" . $a));
    $day_count_list[] = $day_count;
endfor;

$day_count_total = array_sum($day_count_list);
$avg_variable = $day_count_total / $month_count;

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
        <form method="POST">
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
                        <option value="<?= date("m", strtotime($thisYear . "-" . $i)) ?>" <?php if ($i == $target_mm) echo "selected"; ?>><?= $i ?></option>
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
                            $avg_syokuhi = $budget_Amount_sum_year["食費"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_syokuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_ryohikoutuhi = $budget_Amount_sum_year["旅費交通費"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_ryohikoutuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_suidougasu = $budget_Amount_sum_year["水道・ガス・光熱費"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_suidougasu) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_syoumouhin = $budget_Amount_sum_year["消耗品"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_syoumouhin) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_tusinhi = $budget_Amount_sum_year["通信費"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_tusinhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_kensyuuhi = $budget_Amount_sum_year["研修費"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_kensyuuhi) . "円";
                            ?>
                        </td>

                        <td>
                            <?php
                            $avg_kousaihi = $budget_Amount_sum_year["接待交際費"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_kousaihi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_iryouhi = $budget_Amount_sum_year["医療費"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_iryouhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_setuzei = $budget_Amount_sum_year["節税関係"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_setuzei) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $avg_hensai = $budget_Amount_sum_year["返済"]["sum(budget_Amount)"] / ($month_count - 1);
                            echo number_format($avg_hensai) . "円";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">目標</th>
                        <td><input class="w100" type="text" name="target_budget_total" value="<?php if ($target_budget_total) {
                                                                                                    echo number_format($target_budget_total) . "円";
                                                                                                } ?>"></td>
                        <td><input class="w100" type="text" name="target_variable_total" value="<?php if ($target_variable_total) {
                                                                                                    echo number_format($target_variable_total) . "円";
                                                                                                } ?>"></td>
                        <td><input class="w100" type="text" name="target_fixed_total" value="<?php if ($target_fixed_total) {
                                                                                                    echo number_format($target_fixed_total) . "円";
                                                                                                } ?>"></td>
                        <td><input class="w100" type="text" name="target_syokuhi" value="<?php if ($target_syokuhi) {
                                                                                                echo number_format($target_syokuhi) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_ryohikoutuhi" value="<?php if ($target_ryohikoutuhi) {
                                                                                                    echo number_format($target_ryohikoutuhi) . "円";
                                                                                                } ?>"></td>
                        <td><input class="w100" type="text" name="target_suidougasu" value="<?php if ($target_suidougasu) {
                                                                                                echo number_format($target_suidougasu) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_syoumouhin" value="<?php if ($target_syoumouhin) {
                                                                                                echo number_format($target_syoumouhin) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_tusinhi" value="<?php if ($target_tusinhi) {
                                                                                                echo number_format($target_tusinhi) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_kensyuuhi" value="<?php if ($target_kensyuuhi) {
                                                                                                echo number_format($target_kensyuuhi) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_kousaihi" value="<?php if ($target_kousaihi) {
                                                                                                echo number_format($target_kousaihi) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_iryouhi" value="<?php if ($target_iryouhi) {
                                                                                                echo number_format($target_iryouhi) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_setuzei" value="<?php if ($target_setuzei) {
                                                                                                echo number_format($target_setuzei) . "円";
                                                                                            } ?>"></td>
                        <td><input class="w100" type="text" name="target_hensai" value="<?php if ($target_hensai) {
                                                                                            echo number_format($target_hensai) . "円";
                                                                                        } ?>"></td>
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
            <div class="t_content_title">クレジット：<?php echo number_format($credit_total) . "円"; ?></div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">出費項目</th>
                        <th scope="col">日付</th>
                        <th scope="col">詳細</th>
                        <th scope="col">金額</th>
                        <th scope="col">メモ</th>
                        <th scope="col">編集</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i <= $credit_list_count - 1; $i++) : ?>
                        <?php
                        $credit_id = "";
                        $credit_budget_item = "";
                        $credit_date = "";
                        $credit_budget_detail = "";
                        $credit_budget_Amount = "";
                        $credit_comment = "";


                        $credit = $credit_list[$i];
                        if ($credit["id"]) {
                            $credit_budget_id = $credit["id"];
                        }
                        if ($credit["budget_item"]) {
                            $credit_budget_item = $credit["budget_item"];
                        }
                        if ($credit["date"]) {
                            $credit_date = date("Y/m/d", strtotime($credit["date"]));
                        }
                        if ($credit["budget_detail"]) {
                            $credit_budget_detail = $credit["budget_detail"];
                        }
                        if ($credit["budget_Amount"]) {
                            $credit_budget_Amount = $credit["budget_Amount"];
                        }
                        if ($credit["comment"]) {
                            $credit_comment = $credit["comment"];
                        }
                        ?>

                        <tr>
                            <th class="text-center" scope="row"><?= $i + 1 ?></th>
                            <td class="text-center"><?= $credit_budget_item ?></td>
                            <td class="text-center"><?= $credit_date ?></td>
                            <td class="text-center"><?= $credit_budget_detail ?></td>
                            <td class="text-center"><?= number_format($credit_budget_Amount) . "円" ?></td>
                            <td class="with-max px-4"><?= $credit_comment ?></td>
                            <td><button type="button" class="btn py-0" data-bs-toggle="modal" data-bs-target="#inputModal" data-day="<?= $yyyymm . "-" . $i ?> " data-target_month="<?= $modal_month ?>/">●</button></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="p30 w50">
            <div class="t_content_title">現金：<?php echo number_format($cash_total) . "円"; ?></div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">出費項目</th>
                        <th scope="col">日付</th>
                        <th scope="col">詳細</th>
                        <th scope="col">金額</th>
                        <th scope="col">メモ</th>
                        <th scope="col">編集</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i <= $cash_list_count - 1; $i++) : ?>
                        <?php
                        $cash_id = "";
                        $cash_budget_item = "";
                        $cash_date = "";
                        $cash_budget_detail = "";
                        $cash_budget_Amount = "";
                        $cash_comment = "";


                        $cash = $cash_list[$i];
                        if ($cash["budget_item"]) {
                            $cash_budget_item = $cash["budget_item"];
                        }
                        if ($cash["date"]) {
                            $cash_date = date("Y/m/d", strtotime($cash["date"]));
                        }
                        if ($cash["budget_detail"]) {
                            $cash_budget_detail = $cash["budget_detail"];
                        }
                        if ($cash["budget_Amount"]) {
                            $cash_budget_Amount = $cash["budget_Amount"];
                        }
                        if ($cash["comment"]) {
                            $cash_comment = $cash["comment"];
                        }
                        ?>

                        <tr>
                            <th class="text-center" scope="row"><?= $i + 1 ?></th>
                            <td class="text-center"><?= $cash_budget_item ?></td>
                            <td class="text-center"><?= $cash_date ?></td>
                            <td class="text-center"><?= $cash_budget_detail ?></td>
                            <td class="text-center"><?= number_format($cash_budget_Amount) . "円" ?></td>
                            <td class="with-max px-4"><?= $cash_comment ?></td>
                            <td><button type="button" class="btn py-0" data-bs-toggle="modal" data-bs-target="#inputModal" data-day="<?= $yyyymm . "-" . $i ?> " data-target_month="<?= $modal_month ?>/">●</button></td>
                        </tr>
                    <?php endfor; ?>
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