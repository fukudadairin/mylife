<?php
echo "<pre>";
require_once(dirname(__FILE__) . "/config/config.php");
require_once(dirname(__FILE__) . "/function.php");

$pdo  = connect_db();


// 年月セレクトで選択された月を取得、選択されていない状態だと当年当月が選択される
if (isset($_GET["y"])) {
    $target_yyyy = $_GET["y"];
} else {
    $target_yyyy = date("Y");
}

if (isset($_GET["m"])) {
    $target_mm = $_GET["m"];
} else {
    $target_mm = date('m');
}
$target_yyyymm = $target_yyyy . "-" . $target_mm;


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



// 各項目毎の合計を出す
$sql = "SELECT budget_item,sum(budget_Amount) FROM budget WHERE YEAR(date) = :year AND MONTH(date) = :month GROUP BY budget_item ";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":year", $target_yyyy, PDO::PARAM_STR);
$stmt->bindValue(":month", $target_mm, PDO::PARAM_STR);
$stmt->execute();
$budget_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);
$_SESSION["budget_Amount_sum"] = $budget_list;
$budget_Amount_sum = $_SESSION["budget_Amount_sum"];

// 【テスト】各項目毎の平均を出す
// $pdo  = connect_db();
// $sql = "SELECT budget_item,AVG(budget_Amount) FROM budget WHERE date=:date GROUP BY budget_item";
// $stmt = $pdo->prepare($sql); //どれを使うのかを決める→SELECT文：INSERT文：UPDATE文：DELETE文：
// $stmt->bindValue(":date", "2022-01-04", PDO::PARAM_STR);
// $stmt->execute();
// $budget_Average = $stmt->fetchAll(PDO::FETCH_UNIQUE);
// var_dump($budget_Average);

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


if (isset($_GET["m"])) {
    $yyyymm = $_GET["m"];
    $modal_month = date("n", strtotime($yyyymm));
} else {
    $yyyymm = date('Y-m');
    $modal_month = date("n", strtotime($yyyymm));
}
$day_count = date("t", strtotime($yyyymm));

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
                            $Amount_syokuhi = $budget_Amount_sum["食費"]["sum(budget_Amount)"];
                            // $Amount_syokuhi = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_syokuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_ryohikoutuhi = $budget_Amount_sum["旅費交通費"]["sum(budget_Amount)"];
                            // $Amount_ryohikoutuhi = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_ryohikoutuhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_suidougasu = $budget_Amount_sum["水道・ガス・光熱費"]["sum(budget_Amount)"];
                            // $Amount_suidougasu = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_suidougasu) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_syoumouhin = $budget_Amount_sum["消耗品"]["sum(budget_Amount)"];
                            // $Amount_syoumouhin = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_syoumouhin) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_tusinhi = $budget_Amount_sum["通信費"]["sum(budget_Amount)"];
                            // $Amount_tusinhi = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_tusinhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_kensyuuhi = $budget_Amount_sum["研修費"]["sum(budget_Amount)"];
                            // $Amount_kensyuuhi = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_kensyuuhi) . "円";
                            ?>
                        </td>

                        <td>
                            <?php
                            $Amount_kousaihi = $budget_Amount_sum["接待交際費"]["sum(budget_Amount)"];
                            // $Amount_kousaihi = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_kousaihi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_iryouhi = $budget_Amount_sum["医療費"]["sum(budget_Amount)"];
                            // $Amount_iryouhi = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_iryouhi) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_setuzei = $budget_Amount_sum["節税関係"]["sum(budget_Amount)"];
                            // $Amount_setuzei = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_setuzei) . "円";
                            ?>
                        </td>
                        <td>
                            <?php
                            $Amount_hensai = $budget_Amount_sum["返済"]["sum(budget_Amount)"];
                            // $Amount_hensai = intval($budget_Amount_sum["食費"]["sum(budget_Amount)"]);
                            echo number_format($Amount_hensai) . "円";
                            ?>
                        </td>
                        <?php

                        ?>
                    </tr>
                    <tr>
                        <th scope="row">平均</th>
                        <td></td>
                        <td>1,000</td>
                        <td>4,000</td>
                        <td>6,000</td>
                        <td>1,000</td>
                        <td>4,000</td>
                        <td>6,000</td>
                        <td>1,000</td>
                        <td>4,000</td>
                        <td>6,000</td>
                        <td>1,000</td>
                        <td>4,000</td>
                        <td>6,000</td>
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