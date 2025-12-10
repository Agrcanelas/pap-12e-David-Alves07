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
        $stmt = $db->query("SELECT * FROM materiais ORDER BY tipo, CAST(SUBSTRING(nome, LOCATE(' ', nome) + 1) AS UNSIGNED), nome");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
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
        
    default:
        echo json_encode(['error' => 'Ação inválida']);
}
?>