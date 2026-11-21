
<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('잘못된 요청입니다.');
}

$outcome = $_POST['outcome'] ?? '';

if ($outcome === 'success') {
    $title = 'Login Successful!';
    $color = 'green';
    $message = '로그인 성공';
} elseif ($outcome === 'fail') {
    $title = 'Login Failed!';
    $color = 'red';
    $message = '로그인 실패';
} else {
    http_response_code(400);
    $title = 'Invalid Request';
    $color = 'gray';
    $message = '유효하지 않은 요청입니다.';
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Result</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .result-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 300px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        a {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="result-box">
        <h2 style="color: <?= htmlspecialchars($color) ?>">
            <?= htmlspecialchars($title) ?>
        </h2>

        <p><?= htmlspecialchars($message) ?></p>

        <a href="index.php">Back to Login</a>
    </div>
</body>
</html>
