<?php
// api.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Verificar se está logado
if(!isset($_SESSION['user_id']) && $action !== 'login') {
    echo json_encode(['error' => 'Não autenticado']);
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
$isAdmin = $_SESSION['is_admin'] ?? 0;

switch($action) {
    case 'listar_materiais':
$stmt = $db->query("SELECT * FROM materiais WHERE status = 'disponivel' ORDER BY tipo, CAST(SUBSTRING(nome, LOCATE(' ', nome) + 1) AS UNSIGNED), nome");        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'listar_materiais_disponiveis':
        $stmt = $db->query("SELECT * FROM materiais WHERE status = 'disponivel' ORDER BY tipo, nome");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'listar_usuarios':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        $stmt = $db->query("SELECT * FROM usuarios ORDER BY tipo, nome");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'pedir_emprestimo':
        // Utilizador faz pedido
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            // Verificar se o material está disponível
            $stmt = $db->prepare("SELECT status FROM materiais WHERE id = ?");
            $stmt->execute([$data->material_id]);
            $material = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($material['status'] !== 'disponivel') {
                echo json_encode(['success' => false, 'error' => 'Material não está disponível']);
                break;
            }
            
            $stmt = $db->prepare("INSERT INTO emprestimos (usuario_id, material_id, data_prevista_devolucao, observacoes, status) VALUES (?, ?, ?, ?, 'pendente')");
            $stmt->execute([$userId, $data->material_id, $data->data_prevista_devolucao, $data->observacoes ?? '']);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        }
        break;
        
    case 'criar_emprestimo':
        // Admin cria empréstimo direto (já aprovado)
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            $stmt = $db->prepare("INSERT INTO emprestimos (usuario_id, material_id, data_prevista_devolucao, observacoes, status, aprovado_por, data_aprovacao) VALUES (?, ?, ?, ?, 'ativo', ?, NOW())");
            $stmt->execute([$data->usuario_id, $data->material_id, $data->data_prevista_devolucao, $data->observacoes ?? '', $userId]);
            
            // Atualizar status do material
            $stmt = $db->prepare("UPDATE materiais SET status = 'emprestado' WHERE id = ?");
            $stmt->execute([$data->material_id]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        }
        break;
        
    case 'aprovar_pedido':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            $stmt = $db->prepare("UPDATE emprestimos SET status = 'ativo', aprovado_por = ?, data_aprovacao = NOW() WHERE id = ?");
            $stmt->execute([$userId, $data->pedido_id]);
            
            // Atualizar status do material
            $stmt = $db->prepare("UPDATE materiais SET status = 'emprestado' WHERE id = (SELECT material_id FROM emprestimos WHERE id = ?)");
            $stmt->execute([$data->pedido_id]);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'recusar_pedido':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            $stmt = $db->prepare("UPDATE emprestimos SET status = 'recusado' WHERE id = ?");
            $stmt->execute([$data->pedido_id]);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'devolver':
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            // Verificar se é admin ou se é o próprio utilizador
            $stmt = $db->prepare("SELECT usuario_id, material_id FROM emprestimos WHERE id = ?");
            $stmt->execute([$data->emprestimo_id]);
            $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$isAdmin && $emprestimo['usuario_id'] != $userId) {
                echo json_encode(['success' => false, 'error' => 'Sem permissão']);
                break;
            }
            
            $stmt = $db->prepare("UPDATE emprestimos SET status = 'devolvido', data_devolucao = NOW() WHERE id = ?");
            $stmt->execute([$data->emprestimo_id]);
            
            $stmt = $db->prepare("UPDATE materiais SET status = 'disponivel' WHERE id = ?");
            $stmt->execute([$emprestimo['material_id']]);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'listar_emprestimos':
        if($isAdmin) {
            // Admin vê tudo
            $stmt = $db->query("
                SELECT e.*, 
                       u.nome as usuario_nome, 
                       u.tipo as usuario_tipo,
                       u.ano as usuario_ano,
                       u.turma as usuario_turma,
                       m.nome as material_nome, 
                       m.tipo as material_tipo,
                       m.numero_serie as material_numero_serie
                FROM emprestimos e
                JOIN usuarios u ON e.usuario_id = u.id
                JOIN materiais m ON e.material_id = m.id
                ORDER BY 
                    CASE e.status 
                        WHEN 'pendente' THEN 1 
                        WHEN 'ativo' THEN 2 
                        ELSE 3 
                    END,
                    e.data_pedido DESC
            ");
        } else {
            // Utilizador vê apenas os seus
            $stmt = $db->prepare("
                SELECT e.*, 
                       u.nome as usuario_nome, 
                       u.tipo as usuario_tipo,
                       m.nome as material_nome, 
                       m.tipo as material_tipo,
                       m.numero_serie as material_numero_serie
                FROM emprestimos e
                JOIN usuarios u ON e.usuario_id = u.id
                JOIN materiais m ON e.material_id = m.id
                WHERE e.usuario_id = ?
                ORDER BY e.data_pedido DESC
            ");
            $stmt->execute([$userId]);
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'estatisticas':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        $stats = [];
        
        // Total de materiais
        $stmt = $db->query("SELECT COUNT(*) as total FROM materiais");
        $stats['total_materiais'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Materiais disponíveis
        $stmt = $db->query("SELECT COUNT(*) as total FROM materiais WHERE status = 'disponivel'");
        $stats['materiais_disponiveis'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Materiais emprestados
        $stmt = $db->query("SELECT COUNT(*) as total FROM materiais WHERE status = 'emprestado'");
        $stats['materiais_emprestados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pedidos pendentes
        $stmt = $db->query("SELECT COUNT(*) as total FROM emprestimos WHERE status = 'pendente'");
        $stats['pedidos_pendentes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de utilizadores
        $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE is_admin = 0");
        $stats['total_usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode($stats);
        break;
  
        case 'adicionar_material':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            $stmt = $db->prepare("INSERT INTO materiais (nome, tipo, numero_serie, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data->nome, $data->tipo, $data->numero_serie, $data->status]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        }
        break;
        
    case 'editar_material':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            $stmt = $db->prepare("UPDATE materiais SET nome = ?, tipo = ?, numero_serie = ?, status = ? WHERE id = ?");
            $stmt->execute([$data->nome, $data->tipo, $data->numero_serie, $data->status, $data->id]);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'eliminar_material':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            // Verificar se o material está emprestado
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM emprestimos WHERE material_id = ? AND status = 'ativo'");
            $stmt->execute([$data->id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['total'] > 0) {
                echo json_encode(['success' => false, 'error' => 'Material está emprestado, não pode ser eliminado']);
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM materiais WHERE id = ?");
            $stmt->execute([$data->id]);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'adicionar_usuario':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, telefone, tipo, ano, turma, numero_processo, nif, tel_encarregado) VALUES (?, ?, MD5(?), ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data->nome, 
                $data->email, 
                $data->senha, 
                $data->telefone, 
                $data->tipo, 
                $data->ano ?? null, 
                $data->turma ?? null, 
                $data->numero_processo ?? null, 
                $data->nif ?? null, 
                $data->tel_encarregado ?? null
            ]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        }
        break;
        
    case 'editar_usuario':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            // Se não enviou senha, não atualizar
            if(!empty($data->senha)) {
                $stmt = $db->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = MD5(?), telefone = ?, tipo = ?, ano = ?, turma = ?, numero_processo = ?, nif = ?, tel_encarregado = ? WHERE id = ?");
                $stmt->execute([
                    $data->nome, 
                    $data->email, 
                    $data->senha, 
                    $data->telefone, 
                    $data->tipo, 
                    $data->ano ?? null, 
                    $data->turma ?? null, 
                    $data->numero_processo ?? null, 
                    $data->nif ?? null, 
                    $data->tel_encarregado ?? null,
                    $data->id
                ]);
            } else {
                $stmt = $db->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo = ?, ano = ?, turma = ?, numero_processo = ?, nif = ?, tel_encarregado = ? WHERE id = ?");
                $stmt->execute([
                    $data->nome, 
                    $data->email, 
                    $data->telefone, 
                    $data->tipo, 
                    $data->ano ?? null, 
                    $data->turma ?? null, 
                    $data->numero_processo ?? null, 
                    $data->nif ?? null, 
                    $data->tel_encarregado ?? null,
                    $data->id
                ]);
            }
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'eliminar_usuario':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        if($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            // Verificar se o usuário tem empréstimos ativos
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM emprestimos WHERE usuario_id = ? AND status = 'ativo'");
            $stmt->execute([$data->id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['total'] > 0) {
                echo json_encode(['success' => false, 'error' => 'Utilizador tem empréstimos ativos, não pode ser eliminado']);
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$data->id]);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'obter_material':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        $id = $_GET['id'] ?? null;
        if($id) {
            $stmt = $db->prepare("SELECT * FROM materiais WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        }
        break;
        
    case 'obter_usuario':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        $id = $_GET['id'] ?? null;
        if($id) {
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        }
        break;

case 'verificar_atrasos':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        // Atualizar status para atrasado automaticamente
        $stmt = $db->query("
            UPDATE emprestimos 
            SET status = 'atrasado' 
            WHERE status = 'ativo' 
            AND data_prevista_devolucao < CURDATE()
        ");
        
        // Contar empréstimos atrasados
        $stmt = $db->query("SELECT COUNT(*) as total FROM emprestimos WHERE status = 'atrasado'");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode(['success' => true, 'total_atrasados' => $total]);
        break;
        
    case 'listar_atrasados':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        $stmt = $db->query("
            SELECT e.*, 
                   u.nome as usuario_nome, 
                   u.email as usuario_email,
                   u.telefone as usuario_telefone,
                   u.tipo as usuario_tipo,
                   m.nome as material_nome, 
                   m.tipo as material_tipo,
                   DATEDIFF(CURDATE(), e.data_prevista_devolucao) as dias_atraso
            FROM emprestimos e
            JOIN usuarios u ON e.usuario_id = u.id
            JOIN materiais m ON e.material_id = m.id
            WHERE e.status = 'atrasado'
            ORDER BY dias_atraso DESC
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'relatorio_materiais':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        // Materiais mais requisitados
        $stmt = $db->query("
            SELECT m.nome, m.tipo, COUNT(e.id) as total_emprestimos
            FROM materiais m
            LEFT JOIN emprestimos e ON m.id = e.material_id
            GROUP BY m.id
            ORDER BY total_emprestimos DESC
            LIMIT 10
        ");
        $mais_requisitados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Status geral
        $stmt = $db->query("
            SELECT status, COUNT(*) as total 
            FROM materiais 
            GROUP BY status
        ");
        $status_geral = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'mais_requisitados' => $mais_requisitados,
            'status_geral' => $status_geral
        ]);
        break;
        
    case 'relatorio_usuarios':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        // Top utilizadores
        $stmt = $db->query("
            SELECT u.nome, u.tipo, COUNT(e.id) as total_emprestimos,
                   SUM(CASE WHEN e.status = 'atrasado' THEN 1 ELSE 0 END) as total_atrasos
            FROM usuarios u
            LEFT JOIN emprestimos e ON u.id = e.usuario_id
            WHERE u.is_admin = 0
            GROUP BY u.id
            ORDER BY total_emprestimos DESC
            LIMIT 10
        ");
        $top_usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Empréstimos por tipo de utilizador
        $stmt = $db->query("
            SELECT u.tipo, COUNT(e.id) as total
            FROM usuarios u
            JOIN emprestimos e ON u.id = e.usuario_id
            GROUP BY u.tipo
        ");
        $por_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'top_usuarios' => $top_usuarios,
            'por_tipo' => $por_tipo
        ]);
        break;
        
    case 'exportar_emprestimos':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        $stmt = $db->query("
            SELECT 
                e.id,
                u.nome as utilizador,
                u.tipo as tipo_utilizador,
                m.nome as material,
                m.tipo as tipo_material,
                m.numero_serie,
                e.data_pedido,
                e.data_prevista_devolucao,
                e.data_devolucao,
                e.status,
                e.observacoes
            FROM emprestimos e
            JOIN usuarios u ON e.usuario_id = u.id
            JOIN materiais m ON e.material_id = m.id
            ORDER BY e.data_pedido DESC
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'exportar_materiais':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        $stmt = $db->query("
            SELECT 
                id,
                nome,
                tipo,
                numero_serie,
                status,
                data_cadastro
            FROM materiais
            ORDER BY tipo, nome
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'exportar_usuarios':
        if(!$isAdmin) {
            echo json_encode(['error' => 'Sem permissão']);
            break;
        }
        
        $stmt = $db->query("
            SELECT 
                nome,
                email,
                telefone,
                tipo,
                ano,
                turma,
                numero_processo,
                nif,
                tel_encarregado,
                data_cadastro
            FROM usuarios
            WHERE is_admin = 0
            ORDER BY tipo, nome
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    default:
        echo json_encode(['error' => 'Ação inválida']);
}
?>