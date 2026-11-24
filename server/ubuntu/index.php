
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Network Lab Login</title>

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

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 300px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        input, button {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            box-sizing: border-box;
        }

        button {
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Network Lab Login</h2>

    <form method="post" action="auth.php">
        <input type="text" name="username" placeholder="Username">

        <input type="password" name="password" placeholder="Password">

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
