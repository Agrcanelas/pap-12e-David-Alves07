<?php
// login.php
require_once 'auth.php';

$auth = new Auth();
$erro = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if($auth->login($email, $senha)) {
        header('Location: index.php');
        exit();
    } else {
        $erro = 'Email ou senha incorretos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Escola de Canelas</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        html, body {
            height: 100%;
            width: 100%;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 30px 35px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 360px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .logo h1 {
            color: #667eea;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 3px;
        }
        
        .escola-nome {
            color: #764ba2;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            font-size: 12px;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 600;
            font-size: 13px;
        }
        
        input {
            width: 100%;
            padding: 11px 13px;
            border: 2px solid #e0e0e0;
            border-radius: 7px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 13px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
        }
        
        button:hover {
            background: #5568d3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .erro {
            background: #fee;
            color: #c33;
            padding: 11px;
            border-radius: 7px;
            margin-bottom: 16px;
            text-align: center;
            border: 1px solid #fcc;
            font-size: 13px;
            font-weight: 500;
        }
        
        .info-box {
            background: #e7f3ff;
            color: #014361;
            padding: 14px;
            border-radius: 7px;
            margin-top: 20px;
            font-size: 12px;
            border: 1px solid #b3d9ff;
            line-height: 1.5;
        }
        
        .info-box strong {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            color: #012a3f;
        }
        
        .info-box div {
            margin: 6px 0;
            padding-left: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🔐 Login</h1>
            <div class="escola-nome">Escola de Canelas</div>
        </div>
        <p class="subtitle">Sistema de Empréstimo de Material de Informática</p>
        
        <?php if($erro): ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="seu@canelas.pt" autocomplete="email">
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required placeholder="••••••••" autocomplete="current-password">
            </div>
            
            <button type="submit">Entrar</button>
        </form>
        
        <div class="info-box">
            <strong>👤 Contas de Teste:</strong>
            <div><strong>Admin:</strong> admin@canelas.pt / admin123</div>
            <div><strong>Aluno:</strong> joao.silva@canelas.pt / 123456</div>
        </div>
    </div>
</body>
</html>