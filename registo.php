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
    $tipo = $_POST['tipo'] ?? '';
    $telefone = trim($_POST['telefone'] ?? '');
    $ano = trim($_POST['ano'] ?? '');
    $turma = trim($_POST['turma'] ?? '');
    $numero_processo = trim($_POST['numero_processo'] ?? '');
    $nif = trim($_POST['nif'] ?? '');
    $tel_encarregado = trim($_POST['tel_encarregado'] ?? '');

    if($nome === '' || $email === '' || $senha === '' || $tipo === '') {
        $erro = 'Preenche todos os campos obrigatórios!';
    } elseif($senha !== $confirmar) {
        $erro = 'As senhas não coincidem!';
    } elseif(strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres!';
    } else {
        if($auth->register($nome, $email, $senha, $tipo, $telefone, $ano, $turma, $numero_processo, $nif, $tel_encarregado)) {
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
            max-width: 420px;
        }
        .logo { text-align: center; margin-bottom: 15px; }
        .logo h1 { color: #667eea; font-size: 26px; font-weight: 700; margin-bottom: 3px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 25px; font-size: 12px; }
        .form-group { margin-bottom: 16px; }
        .form-row { display: flex; gap: 10px; }
        .form-row .form-group { flex: 1; }
        label { display: block; margin-bottom: 6px; color: #333; font-weight: 600; font-size: 13px; }
        input, select {
            width: 100%;
            padding: 11px 13px;
            border: 2px solid #e0e0e0;
            border-radius: 7px;
            font-size: 14px;
            font-family: inherit;
        }
        input:focus, select:focus {
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
        #camposAluno { display: none; }
        #camposAluno h4 { color: #667eea; margin: 15px 0 10px; font-size: 14px; }
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
                <label>Nome Completo</label>
                <input type="text" name="nome" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                </div>
                <div class="form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" placeholder="912345678">
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo" id="tipo" required onchange="toggleCamposAluno()">
                        <option value="">Selecione...</option>
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                        <option value="funcionario">Funcionário</option>
                    </select>
                </div>
            </div>

            <div id="camposAluno">
                <h4>Dados do Aluno</h4>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ano</label>
                        <select name="ano">
                            <option value="">Selecione...</option>
                            <option value="7">7º Ano</option>
                            <option value="8">8º Ano</option>
                            <option value="9">9º Ano</option>
                            <option value="10">10º Ano</option>
                            <option value="11">11º Ano</option>
                            <option value="12">12º Ano</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Turma</label>
                        <input type="text" name="turma" placeholder="Ex: A, B, C" maxlength="10">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Número de Processo</label>
                        <input type="text" name="numero_processo" placeholder="Ex: P2024001">
                    </div>
                    <div class="form-group">
                        <label>NIF</label>
                        <input type="text" name="nif" placeholder="9 dígitos" maxlength="9">
                    </div>
                </div>

                <div class="form-group">
                    <label>Telefone do Encarregado de Educação</label>
                    <input type="text" name="tel_encarregado" placeholder="912345678">
                </div>
            </div>

            <button type="submit">Criar Conta</button>
        </form>

        <div class="link-login">
            Já tens conta? <a href="login.php">Login</a>
        </div>
    </div>

    <script>
        function toggleCamposAluno() {
            const tipo = document.getElementById('tipo').value;
            document.getElementById('camposAluno').style.display = (tipo === 'aluno') ? 'block' : 'none';
        }
    </script>
</body>
</html>