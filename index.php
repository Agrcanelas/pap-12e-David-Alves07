<?php
// index.php
require_once 'auth.php';

$auth = new Auth();
$auth->requireLogin();

$isAdmin = $auth->isAdmin();
$userName = $auth->getUserName();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Empréstimo - Escola de Canelas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container { max-width: 1200px; margin: 0 auto; }
        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-left h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header-left p {
            color: #666;
            font-size: 14px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .user-role {
            font-size: 12px;
            color: #667eea;
            font-weight: 600;
        }
        
        .btn-logout {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .tab {
            background: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            color: #333;
        }
        
        .tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .tab.active {
            background: #667eea;
            color: white;
        }
        
        .content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            min-height: 400px;
        }
        
        .section {
            display: none;
        }
        
        .section.active {
            display: block;
        }
        
        .section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 22px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button.btn-primary {
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        button.btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }
        
        .table th {
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-disponivel { background: #d4edda; color: #155724; }
        .badge-emprestado { background: #fff3cd; color: #856404; }
        .badge-manutencao { background: #f8d7da; color: #721c24; }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1>🖥️ Sistema de Empréstimo</h1>
                <p>Escola de Canelas - Material de Informática</p>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
                    <div class="user-role"><?php echo $isAdmin ? '👑 Administrador' : '👤 Utilizador'; ?></div>
                </div>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
        
        <div class="tabs">
            <button class="tab active" onclick="showSection('materiais')">📦 Materiais</button>
            <button class="tab" onclick="showSection('emprestimos')">📋 Empréstimos</button>
            <?php if($isAdmin): ?>
            <button class="tab" onclick="showSection('novo-emprestimo')">➕ Novo Empréstimo</button>
            <button class="tab" onclick="showSection('usuarios')">👥 Utilizadores</button>
            <?php endif; ?>
        </div>
        
        <div class="content">
            <!-- Seção Materiais -->
            <div id="materiais" class="section active">
                <h2>📦 Materiais Disponíveis</h2>
                <div class="loading">A carregar materiais...</div>
                <table class="table" id="tabelaMateriais" style="display:none;">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <!-- Seção Empréstimos -->
            <div id="emprestimos" class="section">
                <h2>📋 Histórico de Empréstimos</h2>
                <div class="loading">A carregar empréstimos...</div>
                <table class="table" id="tabelaEmprestimos" style="display:none;">
                    <thead>
                        <tr>
                            <th>Utilizador</th>
                            <th>Material</th>
                            <th>Data Empréstimo</th>
                            <th>Prev. Devolução</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <?php if($isAdmin): ?>
            <!-- Seção Novo Empréstimo -->
            <div id="novo-emprestimo" class="section">
                <h2>➕ Registrar Novo Empréstimo</h2>
                <div id="mensagem"></div>
                <div class="form-group">
                    <label>Utilizador</label>
                    <select id="usuario" required>
                        <option value="">A carregar...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Material</label>
                    <select id="material" required>
                        <option value="">A carregar...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Data Prevista de Devolução</label>
                    <input type="date" id="dataDevolucao" required>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea id="observacoes" rows="3" placeholder="Observações opcionais..."></textarea>
                </div>
                <button class="btn-primary" onclick="registrarEmprestimo()">Registar Empréstimo</button>
            </div>
            
            <!-- Seção Usuários -->
            <div id="usuarios" class="section">
                <h2>👥 Utilizadores do Sistema</h2>
                <div class="loading">A carregar utilizadores...</div>
                <table class="table" id="tabelaUsuarios" style="display:none;">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Telefone</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function showSection(id) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            event.target.classList.add('active');
        }
        
        // Carregar materiais
        fetch('api.php?action=listar_materiais')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tabelaMateriais tbody');
                tbody.innerHTML = data.map(m => `
                    <tr>
                        <td>${m.nome}</td>
                        <td>${m.tipo}</td>
                        <td>${m.marca}</td>
                        <td>${m.modelo || '-'}</td>
                        <td><span class="badge badge-${m.status}">${m.status}</span></td>
                    </tr>
                `).join('');
                document.querySelector('#materiais .loading').style.display = 'none';
                document.getElementById('tabelaMateriais').style.display = 'table';
            });
        
        // Carregar empréstimos
        fetch('api.php?action=listar_emprestimos')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tabelaEmprestimos tbody');
                tbody.innerHTML = data.map(e => `
                    <tr>
                        <td>${e.usuario_nome}</td>
                        <td>${e.material_nome}</td>
                        <td>${new Date(e.data_emprestimo).toLocaleDateString('pt-PT')}</td>
                        <td>${new Date(e.data_prevista_devolucao).toLocaleDateString('pt-PT')}</td>
                        <td><span class="badge badge-${e.status}">${e.status}</span></td>
                    </tr>
                `).join('');
                document.querySelector('#emprestimos .loading').style.display = 'none';
                document.getElementById('tabelaEmprestimos').style.display = 'table';
            });
        
        <?php if($isAdmin): ?>
        // Carregar usuários nos selects
        fetch('api.php?action=listar_usuarios')
            .then(r => r.json())
            .then(data => {
                document.getElementById('usuario').innerHTML = '<option value="">Selecione...</option>' + 
                    data.map(u => `<option value="${u.id}">${u.nome} - ${u.tipo}</option>`).join('');
            });
        
        fetch('api.php?action=listar_materiais')
            .then(r => r.json())
            .then(data => {
                const disponiveis = data.filter(m => m.status === 'disponivel');
                document.getElementById('material').innerHTML = '<option value="">Selecione...</option>' + 
                    disponiveis.map(m => `<option value="${m.id}">${m.nome} - ${m.marca}</option>`).join('');
            });
        
        // Carregar usuários na tabela
        fetch('api.php?action=listar_usuarios')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tabelaUsuarios tbody');
                tbody.innerHTML = data.map(u => `
                    <tr>
                        <td>${u.nome}</td>
                        <td>${u.email}</td>
                        <td>${u.tipo}</td>
                        <td>${u.telefone || '-'}</td>
                    </tr>
                `).join('');
                document.querySelector('#usuarios .loading').style.display = 'none';
                document.getElementById('tabelaUsuarios').style.display = 'table';
            });
        
        function registrarEmprestimo() {
            const dados = {
                usuario_id: document.getElementById('usuario').value,
                material_id: document.getElementById('material').value,
                data_prevista_devolucao: document.getElementById('dataDevolucao').value,
                observacoes: document.getElementById('observacoes').value
            };
            
            if(!dados.usuario_id || !dados.material_id || !dados.data_prevista_devolucao) {
                document.getElementById('mensagem').innerHTML = '<div class="alert alert-error">Preencha todos os campos obrigatórios!</div>';
                return;
            }
            
            fetch('api.php?action=criar_emprestimo', {
                method: 'POST',
                body: JSON.stringify(dados)
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('mensagem').innerHTML = '<div class="alert alert-success">Empréstimo registado com sucesso!</div>';
                    setTimeout(() => location.reload(), 1500);
                }
            });
        }
        <?php endif; ?>
    </script>
</body>
</html>