<?php
// index.php
require_once 'auth.php';

$auth = new Auth();
$auth->requireLogin();

$isAdmin = $auth->isAdmin();
$userName = $auth->getUserName();
$userTipo = $_SESSION['user_tipo'];
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
        
        .container { max-width: 1400px; margin: 0 auto; }
        
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
            display: inline-block;
        }
        
        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
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
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .section h2 {
            color: #333;
            font-size: 22px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
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
        
        label .optional {
            font-weight: normal;
            color: #999;
            font-size: 12px;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border 0.3s;
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
        
        button.btn-success {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        button.btn-success:hover {
            background: #218838;
        }
        
        button.btn-warning {
            background: #ffc107;
            color: #333;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        button.btn-warning:hover {
            background: #e0a800;
        }
        
        button.btn-danger {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        button.btn-danger:hover {
            background: #c82333;
        }
        
        button.btn-secondary {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            margin-left: 10px;
        }
        
        button.btn-secondary:hover {
            background: #5a6268;
        }
        
        .table-container {
            overflow-x: auto;
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
            position: sticky;
            top: 0;
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
        .badge-pendente { background: #cce5ff; color: #004085; }
        .badge-aprovado { background: #d4edda; color: #155724; }
        .badge-recusado { background: #f8d7da; color: #721c24; }
        .badge-ativo { background: #d1ecf1; color: #0c5460; }
        .badge-devolvido { background: #d6d8db; color: #383d41; }
        
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
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .btn-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #999;
            font-size: 14px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            overflow: auto;
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            color: #333;
            font-size: 20px;
        }
        
        .close {
            font-size: 28px;
            font-weight: bold;
            color: #999;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            width: 30px;
            height: 30px;
            line-height: 30px;
        }
        
        .close:hover {
            color: #333;
        }
        
        .modal-footer {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .search-box {
            margin-bottom: 15px;
        }
        
        .search-input {
            width: 100%;
            max-width: 400px;
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1>💻 Sistema de Empréstimo</h1>
                <p>Escola de Canelas - Material de Informática</p>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
                    <div class="user-role">
                        <?php 
                            if($isAdmin) {
                                echo '👑 Administrador';
                            } else if($userTipo == 'professor') {
                                echo '👨‍🏫 Professor';
                            } else {
                                echo '🎓 Aluno';
                            }
                        ?>
                    </div>
                </div>
                <a href="logout.php" class="btn-logout">Sair</a>
            </div>
        </div>
        
        <?php if($isAdmin): ?>
        <div class="stats" id="statsContainer">
            <div class="stat-card">
                <h3>📦 Total Materiais</h3>
                <div class="number" id="stat-total">-</div>
            </div>
            <div class="stat-card">
                <h3>✅ Disponíveis</h3>
                <div class="number" id="stat-disponiveis">-</div>
            </div>
            <div class="stat-card">
                <h3>📤 Emprestados</h3>
                <div class="number" id="stat-emprestados">-</div>
            </div>
            <div class="stat-card">
                <h3>⏳ Pedidos Pendentes</h3>
                <div class="number" id="stat-pendentes">-</div>
            </div>
            <div class="stat-card">
                <h3>👥 Utilizadores</h3>
                <div class="number" id="stat-usuarios">-</div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="tabs">
            <button class="tab active" onclick="showSection('materiais')">📦 Materiais</button>
            <?php if($isAdmin): ?>
            <button class="tab" onclick="showSection('pedidos')">⏳ Pedidos Pendentes</button>
            <a href="relatorios.php" class="tab" style="text-decoration: none;">📊 Relatórios</a>
            <?php endif; ?>
            <button class="tab" onclick="showSection('emprestimos')">📋 <?php echo $isAdmin ? 'Todos os Empréstimos' : 'Meus Empréstimos'; ?></button>
            <button class="tab" onclick="showSection('pedir')">➕ Pedir Empréstimo</button>
            <?php if($isAdmin): ?>
            <button class="tab" onclick="showSection('usuarios')">👥 Utilizadores</button>
            <?php endif; ?>
        </div>
        
        <div class="content">
            <!-- Seção Materiais -->
            <div id="materiais" class="section active">
                <div class="section-header">
                    <h2>📦 Materiais</h2>
                    <?php if($isAdmin): ?>
                    <button class="btn-primary" onclick="abrirModalNovoMaterial()">➕ Adicionar Material</button>
                    <?php endif; ?>
                </div>
                
                <div class="search-box">
                    <input type="text" class="search-input" id="searchMateriais" placeholder="🔍 Pesquisar materiais..." onkeyup="filtrarMateriais()">
                </div>
                
                <div class="loading">A carregar materiais...</div>
                <div class="table-container">
                    <table class="table" id="tabelaMateriais" style="display:none;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Número de Série</th>
                                <th>Status</th>
                                <?php if($isAdmin): ?>
                                <th>Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <?php if($isAdmin): ?>
            <!-- Seção Pedidos Pendentes -->
            <div id="pedidos" class="section">
                <h2>⏳ Pedidos Pendentes de Aprovação</h2>
                <div class="loading">A carregar pedidos...</div>
                <div class="table-container">
                    <table class="table" id="tabelaPedidos" style="display:none;">
                        <thead>
                            <tr>
                                <th>Utilizador</th>
                                <th>Tipo</th>
                                <th>Ano/Turma</th>
                                <th>Material</th>
                                <th>Data Pedido</th>
                                <th>Prev. Devolução</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="emptyPedidos" style="display:none;">
                    <div class="empty-state">
                        <div class="empty-state-icon">✅</div>
                        <h3>Nenhum pedido pendente</h3>
                        <p>Não há pedidos à espera de aprovação</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Seção Empréstimos -->
            <div id="emprestimos" class="section">
                <h2>📋 <?php echo $isAdmin ? 'Histórico de Empréstimos' : 'Meus Empréstimos'; ?></h2>
                
                <div class="search-box">
                    <input type="text" class="search-input" id="searchEmprestimos" placeholder="🔍 Pesquisar empréstimos..." onkeyup="filtrarEmprestimos()">
                </div>
                
                <div class="loading">A carregar empréstimos...</div>
                <div class="table-container">
                    <table class="table" id="tabelaEmprestimos" style="display:none;">
                        <thead>
                            <tr>
                                <?php if($isAdmin): ?>
                                <th>Utilizador</th>
                                <th>Tipo</th>
                                <?php endif; ?>
                                <th>Material</th>
                                <th>Nº Série</th>
                                <th>Data Pedido</th>
                                <th>Prev. Devolução</th>
                                <th>Status</th>
                                <?php if($isAdmin): ?>
                                <th>Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <!-- Seção Pedir Empréstimo -->
            <div id="pedir" class="section">
                <h2>➕ Pedir Empréstimo</h2>
                <div id="mensagemPedido"></div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Material</label>
                        <select id="materialPedido" required>
                            <option value="">A carregar...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data Prevista de Devolução</label>
                        <input type="date" id="dataDevolucaoPedido" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Observações / Motivo do Pedido</label>
                    <textarea id="observacoesPedido" rows="3" placeholder="Ex: Preciso para trabalho de grupo de matemática..."></textarea>
                </div>
                
                <button class="btn-primary" onclick="fazerPedido()">Enviar Pedido</button>
                
                <div class="alert alert-info" style="margin-top: 20px;">
                    <strong>ℹ️ Informação:</strong> O seu pedido será enviado para aprovação do administrador. Será notificado quando for aprovado ou recusado.
                </div>
            </div>
            
            <?php if($isAdmin): ?>
            <!-- Seção Usuários -->
            <div id="usuarios" class="section">
                <div class="section-header">
                    <h2>👥 Utilizadores do Sistema</h2>
                    <button class="btn-primary" onclick="abrirModalNovoUsuario()">➕ Adicionar Utilizador</button>
                </div>
                
                <div class="search-box">
                    <input type="text" class="search-input" id="searchUsuarios" placeholder="🔍 Pesquisar utilizadores..." onkeyup="filtrarUsuarios()">
                </div>
                
                <div class="loading">A carregar utilizadores...</div>
                <div class="table-container">
                    <table class="table" id="tabelaUsuarios" style="display:none;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Ano/Turma</th>
                                <th>Nº Processo</th>
                                <th>NIF</th>
                                <th>Telefone</th>
                                <th>Tel. Encarregado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    