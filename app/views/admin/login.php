<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Porsche Next', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #000;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .login-box { 
            background: #0a0a0a;
            padding: 60px 50px;
            border: 1px solid rgba(255,255,255,0.1);
            width: 100%;
            max-width: 450px;
        }
        h2 { 
            text-align: center;
            margin-bottom: 40px;
            color: #fff;
            font-size: 1.8rem;
            font-weight: 300;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .form-group { margin-bottom: 30px; }
        label { 
            display: block;
            margin-bottom: 10px;
            color: rgba(255,255,255,0.8);
            font-weight: 300;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        input { 
            width: 100%;
            padding: 15px;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.95rem;
            transition: 0.3s;
            font-family: inherit;
        }
        input:focus { 
            outline: none;
            border-color: #fff;
        }
        .btn { 
            width: 100%;
            padding: 15px;
            background: #fff;
            color: #000;
            border: 1px solid #fff;
            font-size: 0.9rem;
            cursor: pointer;
            font-weight: 400;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: inherit;
        }
        .btn:hover { 
            background: transparent;
            color: #fff;
        }
        .error { 
            background: rgba(255,0,0,0.1);
            color: #ff6b6b;
            padding: 12px 15px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid rgba(255,0,0,0.3);
            font-size: 0.9rem;
            font-weight: 300;
        }
        .info { 
            margin-top: 30px;
            padding: 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            font-weight: 300;
            letter-spacing: 0.3px;
        }
        .info strong { color: #fff; font-weight: 400; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Access</h2>
        <?php if (isset($error) && $error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="info">
            <strong>Default:</strong><br>
            Username: admin<br>
            Password: admin123
        </div>
    </div>
</body>
</html>
