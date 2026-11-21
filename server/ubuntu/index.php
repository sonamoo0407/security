
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

    <form id="loginForm">
        <input type="text" id="username"
               placeholder="Username" required>

        <input type="password" id="password"
               placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
</div>

<script>
    //가상 데이터
    const demoUser = {
        username: "labuser",
        password: "lab1234"
    };

    document.getElementById("loginForm")
        .addEventListener("submit", function(event) {

        event.preventDefault();

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        const outcome =
            username === demoUser.username &&
            password === demoUser.password
            ? "success"
            : "fail";

        const form = document.createElement("form");
        form.method = "POST";
        form.action = "auth.php";

        const field = document.createElement("input");
        field.type = "hidden";
        field.name = "outcome";
        field.value = outcome;

        form.appendChild(field);
        document.body.appendChild(form);
        form.submit();
    });
</script>

</body>
</html>
