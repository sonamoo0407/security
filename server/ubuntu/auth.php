<?php
session_start();

// POST 요청만 허용
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method Not Allowed");
}

// 입력값 확인
$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

// 실습용 계정
$demoUsername = "user";
$demoPasswordHash = password_hash("1234", PASSWORD_DEFAULT);

// 서버 측 인증
$loginSuccess =
    ($username === $demoUsername) &&
    password_verify($password, $demoPasswordHash);

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Login Result</title>
</head>
<body>
<?php if ($loginSuccess): ?>
    <h2>Login Successful!</h2>
    <p>인증에 성공했습니다.</p>
<?php else: ?>
    <h2>Login Failed!</h2>
    <p>아이디 또는 비밀번호가 올바르지 않습니다.</p>
<?php endif; ?>

<a href="index.php">Back to Login</a>
</body>
</html>
