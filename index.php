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
    <div class="p30">
        <form method="POST">
            <table class="table table-bordered">
                <thead>
                    <tr>
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
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                    </tr>
                    <tr>
                        <th scope="row">平均</th>
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
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>4,000</td>
                        <td>6,000</td>
                        <td>1,000</td>
                        <td>4,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>4,000</td>
                        <td>3,000</td>
                        <td>4,000</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>−10,4％</td>
                        <td>−30,4％</td>
                        <td>−10,4％</td>
                        <td>−30,4％</td>
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
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>4,000</td>
                        <td>6,000</td>
                        <td>1,000</td>
                        <td>4,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>4,000</td>
                        <td>3,000</td>
                        <td>4,000</td>
                        <td>3,000</td>
                        <td>テキストが入るテキストが入るテキストが入る</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>−10,4％</td>
                        <td>−30,4％</td>
                        <td>−10,4％</td>
                        <td>−30,4％</td>
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