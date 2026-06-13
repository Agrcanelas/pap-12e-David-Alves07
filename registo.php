<?php
// registo.php
require_once 'auth.php';

$auth = new Auth();
$erro = '';
$sucesso = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    if($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preenche todos os campos!';
    } elseif($senha !== $confirmar) {
        $erro = 'As senhas não coincidem!';
    } elseif(strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres!';
    } else {
        if($auth->register($nome, $email, $senha)) {
            $sucesso = 'Conta criada com sucesso! Já podes fazer login.';
        } else {
            $erro = 'Esse email já está registado!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Escola de Canelas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; width: 100%; }
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
        .logo { text-align: center; margin-bottom: 15px; }
        .logo h1 { color: #667eea; font-size: 26px; font-weight: 700; margin-bottom: 3px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 25px; font-size: 12px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; color: #333; font-weight: 600; font-size: 13px; }
        input {
            width: 100%;
            padding: 11px 13px;
            border: 2px solid #e0e0e0;
            border-radius: 7px;
            font-size: 14px;
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
            margin-top: 8px;
        }
        button:hover { background: #5568d3; }
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
        .sucesso {
            background: #efe;
            color: #2a7;
            padding: 11px;
            border-radius: 7px;
            margin-bottom: 16px;
            text-align: center;
            border: 1px solid #cfc;
            font-size: 13px;
            font-weight: 500;
        }
        .link-login {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
        }
        .link-login a { color: #667eea; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Registo</h1>
            <div class="escola-nome">Escola de Canelas</div>
        </div>
        <p class="subtitle">Cria a tua conta</p>

        <?php if($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if($sucesso): ?>
            <div class="sucesso"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            <div class="form-group">
                <label>Confirmar Senha</label>
                <input type="password" name="confirmar_senha" required>
            </div>
            <button type="submit">Criar Conta</button>
        </form>

        <div class="link-login">
            Já tens conta? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>