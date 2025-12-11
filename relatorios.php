<?php
// relatorios.php
require_once 'auth.php';

$auth = new Auth();
$auth->requireLogin();
$auth->requireAdmin();

$userName = $auth->getUserName();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Escola de Canelas</title>
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
        
        .btn-voltar {
            background: #6c757d;
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
        
        .btn-voltar:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #667eea;
            font-size: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-card {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }
        
        .alert-card h2 {
            color: white;
        }
        
        .alert-number {
            font-size: 48px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
            margin-top: 10px;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 10px;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .stat-value {
            color: #333;
            font-weight: 600;
            font-size: 16px;
        }
        
        .export-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .export-section h2 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .export-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .progress-fill {
            height: 100%;
            background: #667eea;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1>📊 Relatórios e Estatísticas</h1>
                <p>Escola de Canelas - Análise de Dados</p>
            </div>
            <a href="index.php" class="btn-voltar">← Voltar ao Sistema</a>
        </div>
        
        <div class="cards">
            <!-- Card de Atrasos -->
            <div class="card alert-card">
                <h2>⚠️ Empréstimos Atrasados</h2>
                <div class="alert-number" id="totalAtrasados">-</div>
                <p>Materiais que ultrapassaram a data de devolução</p>
                <button class="btn-primary" onclick="mostrarAtrasados()">Ver Detalhes</button>
            </div>
            
            <!-- Card de Materiais -->
            <div class="card">
                <h2>📦 Análise de Materiais</h2>
                <div id="estatisticasMateriais" class="loading">A carregar...</div>
            </div>
            
            <!-- Card de Utilizadores -->
            <div class="card">
                <h2>👥 Análise de Utilizadores</h2>
                <div id="estatisticasUsuarios" class="loading">A carregar...</div>
            </div>
        </div>
        
        <!-- Seção de Exportação -->
        <div class="export-section">
            <h2>📥 Exportar Dados para Excel</h2>
            <p style="color: #666; margin-bottom: 15px;">Exporte todos os dados para arquivo Excel (.csv)</p>
            <div class="export-buttons">
                <button class="btn-success" onclick="exportarEmprestimos()">📋 Exportar Empréstimos</button>
                <button class="btn-success" onclick="exportarMateriais()">📦 Exportar Materiais</button>
                <button class="btn-success" onclick="exportarUsuarios()">👥 Exportar Utilizadores</button>
            </div>
        </div>
        
        <!-- Card de Top Materiais -->
        <div class="card">
            <h2>🏆 Top 10 Materiais Mais Requisitados</h2>
            <div class="table-container">
                <table class="table" id="tabelaTopMateriais">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Material</th>
                            <th>Tipo</th>
                            <th>Total Empréstimos</th>
                            <th>Popularidade</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <!-- Card de Top Utilizadores -->
        <div class="card">
            <h2>👤 Top 10 Utilizadores</h2>
            <div class="table-container">
                <table class="table" id="tabelaTopUsuarios">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Empréstimos</th>
                            <th>Atrasos</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <!-- Card de Empréstimos Atrasados (escondido por padrão) -->
        <div class="card" id="cardAtrasados" style="display:none;">
            <h2>⚠️ Lista de Empréstimos Atrasados</h2>
            <div class="table-container">
                <table class="table" id="tabelaAtrasados">
                    <thead>
                        <tr>
                            <th>Utilizador</th>
                            <th>Contacto</th>
                            <th>Material</th>
                            <th>Data Prevista</th>
                            <th>Dias de Atraso</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Verificar e carregar atrasos
        function carregarAtrasos() {
            fetch('api.php?action=verificar_atrasos')
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('totalAtrasados').textContent = data.total_atrasados;
                    }
                });
        }
        
        // Mostrar lista de atrasados
        function mostrarAtrasados() {
            const card = document.getElementById('cardAtrasados');
            card.style.display = 'block';
            card.scrollIntoView({ behavior: 'smooth' });
            
            fetch('api.php?action=listar_atrasados')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.querySelector('#tabelaAtrasados tbody');
                    
                    if(data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nenhum empréstimo atrasado! 🎉</td></tr>';
                    } else {
                        tbody.innerHTML = data.map(e => `
                            <tr>
                                <td>${e.usuario_nome}<br><small style="color:#999;">${e.usuario_tipo}</small></td>
                                <td>${e.usuario_email}<br><small style="color:#999;">${e.usuario_telefone || '-'}</small></td>
                                <td>${e.material_tipo} - ${e.material_nome}</td>
                                <td>${new Date(e.data_prevista_devolucao).toLocaleDateString('pt-PT')}</td>
                                <td><span class="badge badge-danger">${e.dias_atraso} dias</span></td>
                            </tr>
                        `).join('');
                    }
                });
        }
        
        // Carregar relatório de materiais
        function carregarRelatorioMateriais() {
            fetch('api.php?action=relatorio_materiais')
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('estatisticasMateriais');
                    
                    let html = '<div>';
                    data.status_geral.forEach(item => {
                        html += `
                            <div class="stat-item">
                                <span class="stat-label">${item.status}</span>
                                <span class="stat-value">${item.total}</span>
                            </div>
                        `;
                    });
                    html += '</div>';
                    
                    container.innerHTML = html;
                    
                    // Preencher top materiais
                    const tbody = document.querySelector('#tabelaTopMateriais tbody');
                    if(data.mais_requisitados.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nenhum dado disponível</td></tr>';
                    } else {
                        const maxEmprestimos = Math.max(...data.mais_requisitados.map(m => m.total_emprestimos));
                        
                        tbody.innerHTML = data.mais_requisitados.map((m, index) => {
                            const percentagem = maxEmprestimos > 0 ? (m.total_emprestimos / maxEmprestimos * 100) : 0;
                            return `
                                <tr>
                                    <td><strong>${index + 1}º</strong></td>
                                    <td>${m.nome}</td>
                                    <td>${m.tipo}</td>
                                    <td>${m.total_emprestimos}</td>
                                    <td>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: ${percentagem}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    }
                });
        }
        
        // Carregar relatório de utilizadores
        function carregarRelatorioUsuarios() {
            fetch('api.php?action=relatorio_usuarios')
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('estatisticasUsuarios');
                    
                    let html = '<div>';
                    data.por_tipo.forEach(item => {
                        html += `
                            <div class="stat-item">
                                <span class="stat-label">Empréstimos de ${item.tipo}s</span>
                                <span class="stat-value">${item.total}</span>
                            </div>
                        `;
                    });
                    html += '</div>';
                    
                    container.innerHTML = html;
                    
                    // Preencher top utilizadores
                    const tbody = document.querySelector('#tabelaTopUsuarios tbody');
                    if(data.top_usuarios.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nenhum dado disponível</td></tr>';
                    } else {
                        tbody.innerHTML = data.top_usuarios.map((u, index) => `
                            <tr>
                                <td><strong>${index + 1}º</strong></td>
                                <td>${u.nome}</td>
                                <td>${u.tipo}</td>
                                <td>${u.total_emprestimos || 0}</td>
                                <td>${u.total_atrasos || 0}</td>
                            </tr>
                        `).join('');
                    }
                });
        }
        
        // Função para converter JSON para CSV
        function jsonParaCsv(dados, colunas) {
            let csv = colunas.join(';') + '\n';
            
            dados.forEach(row => {
                const valores = colunas.map(col => {
                    let valor = row[col] || '';
                    // Escapar aspas e adicionar aspas se contiver vírgula ou ponto e vírgula
                    if(typeof valor === 'string' && (valor.includes(';') || valor.includes('"'))) {
                        valor = '"' + valor.replace(/"/g, '""') + '"';
                    }
                    return valor;
                });
                csv += valores.join(';') + '\n';
            });
            
            return csv;
        }
        
        // Função para fazer download do CSV
        function downloadCsv(csv, nomeArquivo) {
            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = nomeArquivo;
            link.click();
        }
        
        // Exportar empréstimos
        function exportarEmprestimos() {
            fetch('api.php?action=exportar_emprestimos')
                .then(r => r.json())
                .then(data => {
                    const colunas = ['id', 'utilizador', 'tipo_utilizador', 'material', 'tipo_material', 'numero_serie', 'data_pedido', 'data_prevista_devolucao', 'data_devolucao', 'status', 'observacoes'];
                    const csv = jsonParaCsv(data, colunas);
                    downloadCsv(csv, 'emprestimos_' + new Date().toISOString().split('T')[0] + '.csv');
                    alert('✅ Empréstimos exportados com sucesso!');
                });
        }
        
        // Exportar materiais
        function exportarMateriais() {
            fetch('api.php?action=exportar_materiais')
                .then(r => r.json())
                .then(data => {
                    const colunas = ['id', 'nome', 'tipo', 'numero_serie', 'status', 'data_cadastro'];
                    const csv = jsonParaCsv(data, colunas);
                    downloadCsv(csv, 'materiais_' + new Date().toISOString().split('T')[0] + '.csv');
                    alert('✅ Materiais exportados com sucesso!');
                });
        }
        
        // Exportar utilizadores
        function exportarUsuarios() {
            fetch('api.php?action=exportar_usuarios')
                .then(r => r.json())
                .then(data => {
                    const colunas = ['nome', 'email', 'telefone', 'tipo', 'ano', 'turma', 'numero_processo', 'nif', 'tel_encarregado', 'data_cadastro'];
                    const csv = jsonParaCsv(data, colunas);
                    downloadCsv(csv, 'utilizadores_' + new Date().toISOString().split('T')[0] + '.csv');
                    alert('✅ Utilizadores exportados com sucesso!');
                });
        }
        
        // Carregar tudo ao iniciar
        window.onload = function() {
            carregarAtrasos();
            carregarRelatorioMateriais();
            carregarRelatorioUsuarios();
        }
    </script>
</body>
</html>