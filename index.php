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
        
        .section h2 {
            color: #333;
            margin-bottom: 20px;
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
                <h2>📦 Materiais Disponíveis</h2>
                <div class="loading">A carregar materiais...</div>
                <div class="table-container">
                    <table class="table" id="tabelaMateriais" style="display:none;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Número de Série</th>
                                <th>Status</th>
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
                <h2>👥 Utilizadores do Sistema</h2>
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
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
        
        function showSection(id) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            event.target.classList.add('active');
        }
        
        // Carregar estatísticas (admin)
        if(isAdmin) {
            fetch('api.php?action=estatisticas')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('stat-total').textContent = data.total_materiais;
                    document.getElementById('stat-disponiveis').textContent = data.materiais_disponiveis;
                    document.getElementById('stat-emprestados').textContent = data.materiais_emprestados;
                    document.getElementById('stat-pendentes').textContent = data.pedidos_pendentes;
                    document.getElementById('stat-usuarios').textContent = data.total_usuarios;
                });
        }
        
        // Carregar materiais
        fetch('api.php?action=listar_materiais')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tabelaMateriais tbody');
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nenhum material cadastrado</td></tr>';
                } else {
                    tbody.innerHTML = data.map(m => `
                        <tr>
                            <td>${m.nome}</td>
                            <td>${m.tipo}</td>
                            <td>${m.numero_serie || '-'}</td>
                            <td><span class="badge badge-${m.status}">${m.status}</span></td>
                        </tr>
                    `).join('');
                }
                document.querySelector('#materiais .loading').style.display = 'none';
                document.getElementById('tabelaMateriais').style.display = 'table';
            });
        
        // Carregar materiais disponíveis para pedido
        fetch('api.php?action=listar_materiais_disponiveis')
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('materialPedido');
                if(data.length === 0) {
                    select.innerHTML = '<option value="">Nenhum material disponível</option>';
                } else {
                    select.innerHTML = '<option value="">Selecione um material...</option>' + 
                        data.map(m => `<option value="${m.id}">${m.tipo} - ${m.nome} (${m.numero_serie})</option>`).join('');
                }
            });
        
        // Carregar empréstimos
        fetch('api.php?action=listar_emprestimos')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tabelaEmprestimos tbody');
                
                if(data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="${isAdmin ? 8 : 6}" style="text-align:center;">Nenhum empréstimo encontrado</td></tr>`;
                } else {
                    tbody.innerHTML = data.map(e => {
                        let row = '<tr>';
                        
                        if(isAdmin) {
                            const anoTurma = e.usuario_ano && e.usuario_turma ? `${e.usuario_ano}º ${e.usuario_turma}` : '-';
                            row += `<td>${e.usuario_nome}</td>`;
                            row += `<td>${e.usuario_tipo}</td>`;
                        }
                        
                        row += `<td>${e.material_nome}</td>`;
                        row += `<td>${e.material_numero_serie || '-'}</td>`;
                        row += `<td>${new Date(e.data_pedido).toLocaleDateString('pt-PT')}</td>`;
                        row += `<td>${new Date(e.data_prevista_devolucao).toLocaleDateString('pt-PT')}</td>`;
                        row += `<td><span class="badge badge-${e.status}">${e.status}</span></td>`;
                        
                        if(isAdmin && e.status === 'ativo') {
                            row += `<td><button class="btn-success" onclick="devolverMaterial(${e.id}, ${e.material_id})">Devolver</button></td>`;
                        } else if(isAdmin) {
                            row += '<td>-</td>';
                        }
                        
                        row += '</tr>';
                        return row;
                    }).join('');
                }
                
                document.querySelector('#emprestimos .loading').style.display = 'none';
                document.getElementById('tabelaEmprestimos').style.display = 'table';
            });
        
        // Carregar pedidos pendentes (admin)
        if(isAdmin) {
            fetch('api.php?action=listar_emprestimos')
                .then(r => r.json())
                .then(data => {
                    const pendentes = data.filter(e => e.status === 'pendente');
                    const tbody = document.querySelector('#tabelaPedidos tbody');
                    
                    if(pendentes.length === 0) {
                        document.querySelector('#pedidos .loading').style.display = 'none';
                        document.getElementById('emptyPedidos').style.display = 'block';
                    } else {
                        tbody.innerHTML = pendentes.map(e => {
                            const anoTurma = e.usuario_ano && e.usuario_turma ? `${e.usuario_ano}º ${e.usuario_turma}` : '-';
                            return `
                                <tr>
                                    <td>${e.usuario_nome}</td>
                                    <td>${e.usuario_tipo}</td>
                                    <td>${anoTurma}</td>
                                    <td>${e.material_nome}</td>
                                    <td>${new Date(e.data_pedido).toLocaleDateString('pt-PT')}</td>
                                    <td>${new Date(e.data_prevista_devolucao).toLocaleDateString('pt-PT')}</td>
                                    <td>${e.observacoes || '-'}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn-success" onclick="aprovarPedido(${e.id})">✓ Aprovar</button>
                                            <button class="btn-danger" onclick="recusarPedido(${e.id})">✗ Recusar</button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                        
                        document.querySelector('#pedidos .loading').style.display = 'none';
                        document.getElementById('tabelaPedidos').style.display = 'table';
                    }
                });
            
            // Carregar usuários
            fetch('api.php?action=listar_usuarios')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.querySelector('#tabelaUsuarios tbody');
                    tbody.innerHTML = data.filter(u => u.is_admin == 0).map(u => {
                        const anoTurma = u.ano && u.turma ? `${u.ano}º ${u.turma}` : '-';
                        return `
                            <tr>
                                <td>${u.nome}</td>
                                <td>${u.email}</td>
                                <td>${u.tipo}</td>
                                <td>${anoTurma}</td>
                                <td>${u.numero_processo || '-'}</td>
                                <td>${u.nif || '-'}</td>
                                <td>${u.telefone || '-'}</td>
                                <td>${u.tel_encarregado || '-'}</td>
                            </tr>
                        `;
                    }).join('');
                    
                    document.querySelector('#usuarios .loading').style.display = 'none';
                    document.getElementById('tabelaUsuarios').style.display = 'table';
                });
        }
        
        // Definir data mínima para hoje
        const hoje = new Date().toISOString().split('T')[0];
        document.getElementById('dataDevolucaoPedido').setAttribute('min', hoje);
        
        // Fazer pedido de empréstimo
        function fazerPedido() {
            const materialId = document.getElementById('materialPedido').value;
            const dataDevolucao = document.getElementById('dataDevolucaoPedido').value;
            const observacoes = document.getElementById('observacoesPedido').value;
            
            if(!materialId || !dataDevolucao) {
                document.getElementById('mensagemPedido').innerHTML = '<div class="alert alert-error">Preencha todos os campos obrigatórios!</div>';
                return;
            }
            
            const dados = {
                material_id: materialId,
                data_prevista_devolucao: dataDevolucao,
                observacoes: observacoes
            };
            
            fetch('api.php?action=pedir_emprestimo', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(dados)
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('mensagemPedido').innerHTML = '<div class="alert alert-success">✅ Pedido enviado com sucesso! Aguarde aprovação do administrador.</div>';
                    document.getElementById('materialPedido').value = '';
                    document.getElementById('dataDevolucaoPedido').value = '';
                    document.getElementById('observacoesPedido').value = '';
                    setTimeout(() => location.reload(), 2000);
                } else {
                    document.getElementById('mensagemPedido').innerHTML = `<div class="alert alert-error">❌ ${data.error || 'Erro ao enviar pedido'}</div>`;
                }
            });
        }
        
        // Aprovar pedido (admin)
        function aprovarPedido(id) {
            if(!confirm('Aprovar este pedido de empréstimo?')) return;
            
            fetch('api.php?action=aprovar_pedido', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({pedido_id: id})
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('✅ Pedido aprovado com sucesso!');
                    location.reload();
                }
            });
        }
        
        // Recusar pedido (admin)
        function recusarPedido(id) {
            if(!confirm('Recusar este pedido de empréstimo?')) return;
            
            fetch('api.php?action=recusar_pedido', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({pedido_id: id})
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('❌ Pedido recusado!');
                    location.reload();
                }
            });
        }
        
        // Devolver material (admin)
        function devolverMaterial(emprestimoId, materialId) {
            if(!confirm('Confirmar devolução deste material?')) return;
            
            fetch('api.php?action=devolver', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    emprestimo_id: emprestimoId,
                    material_id: materialId
                })
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    alert('✅ Material devolvido com sucesso!');
                    location.reload();
                } else {
                    alert('❌ Erro ao devolver material: ' + (data.error || 'Erro desconhecido'));
                }
            })
            .catch(err => {
                alert('❌ Erro de conexão');
                console.error(err);
            });
        }
    </script>
</body>
</html>