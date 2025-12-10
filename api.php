<?php
// api.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once 'config.php';

$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Verificar se está logado
$user_id = $_SESSION['user_id'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? 0;

switch($action) {
    case 'listar_materiais':
        $stmt = $db->query("
            SELECT m.*, s.numero as sala_numero, s.nome as sala_nome 
            FROM materiais m
            JOIN salas s ON m.sala_id = s.id
            ORDER BY s.numero, m.tipo, m.numero_inventario
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'listar_salas':
        $stmt = $db->query("SELECT * FROM salas ORDER BY numero");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'listar_usuarios':
        $stmt = $db->query("SELECT id, nome, email, telefone, departamento FROM usuarios ORDER BY nome");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'materiais_por_sala':
        $sala_id = $_GET['sala_id'] ?? 0;
        $stmt = $db->prepare("
            SELECT m.*, s.numero as sala_numero, s.nome as sala_nome 
            FROM materiais m
            JOIN salas s ON m.sala_id = s.id
            WHERE m.sala_id = ?
            ORDER BY m.tipo, m.numero_inventario
        ");
        $stmt->execute([$sala_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'criar_transferencia':
        if($method === 'POST' && $user_id) {
            $data = json_decode(file_get_contents("php://input"));
            
            // Buscar sala atual do material
            $stmt = $db->prepare("SELECT sala_id FROM materiais WHERE id = ?");
            $stmt->execute([$data->material_id]);
            $material = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$material) {
                echo json_encode(['success' => false, 'error' => 'Material não encontrado']);
                break;
            }
            
            // Registrar transferência
            $stmt = $db->prepare("
                INSERT INTO transferencias 
                (material_id, professor_id, sala_origem_id, sala_destino_id, motivo) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data->material_id, 
                $user_id, 
                $material['sala_id'], 
                $data->sala_destino_id, 
                $data->motivo ?? ''
            ]);
            
            // Atualizar localização do material
            $stmt = $db->prepare("UPDATE materiais SET sala_id = ? WHERE id = ?");
            $stmt->execute([$data->sala_destino_id, $data->material_id]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        }
        break;
        
    case 'cancelar_transferencia':
        if($method === 'POST' && $user_id) {
            $data = json_decode(file_get_contents("php://input"));
            $transferencia_id = $data->transferencia_id;
            
            // Buscar a transferência
            $stmt = $db->prepare("
                SELECT t.*, m.sala_id as sala_atual
                FROM transferencias t
                JOIN materiais m ON t.material_id = m.id
                WHERE t.id = ? AND t.cancelada = 0
            ");
            $stmt->execute([$transferencia_id]);
            $transferencia = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$transferencia) {
                echo json_encode(['success' => false, 'error' => 'Transferência não encontrada']);
                break;
            }
            
            // Verificar permissão (professor só pode cancelar suas próprias, admin pode cancelar todas)
            if($transferencia['professor_id'] != $user_id && !$is_admin) {
                echo json_encode(['success' => false, 'error' => 'Sem permissão']);
                break;
            }
            
            // Verificar se é recente (últimas 24 horas)
            $data_transferencia = strtotime($transferencia['data_transferencia']);
            $horas_passadas = (time() - $data_transferencia) / 3600;
            
            if($horas_passadas > 24 && !$is_admin) {
                echo json_encode(['success' => false, 'error' => 'Só pode cancelar nas primeiras 24 horas']);
                break;
            }
            
            // Voltar material para sala de origem
            $stmt = $db->prepare("UPDATE materiais SET sala_id = ? WHERE id = ?");
            $stmt->execute([$transferencia['sala_origem_id'], $transferencia['material_id']]);
            
            // Marcar transferência como cancelada
            $stmt = $db->prepare("
                UPDATE transferencias 
                SET cancelada = 1, data_cancelamento = NOW(), cancelada_por = ?
                WHERE id = ?
            ");
            $stmt->execute([$user_id, $transferencia_id]);
            
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'listar_transferencias':
        // Admin vê todas, professor só vê as dele
        if($is_admin) {
            $stmt = $db->query("
                SELECT t.*, 
                       u.nome as professor_nome,
                       m.tipo as material_tipo,
                       m.numero_inventario as material_inventario,
                       so.numero as sala_origem_numero,
                       so.nome as sala_origem_nome,
                       sd.numero as sala_destino_numero,
                       sd.nome as sala_destino_nome,
                       uc.nome as cancelada_por_nome
                FROM transferencias t
                JOIN usuarios u ON t.professor_id = u.id
                JOIN materiais m ON t.material_id = m.id
                JOIN salas so ON t.sala_origem_id = so.id
                JOIN salas sd ON t.sala_destino_id = sd.id
                LEFT JOIN usuarios uc ON t.cancelada_por = uc.id
                ORDER BY t.data_transferencia DESC
                LIMIT 100
            ");
        } else {
            $stmt = $db->prepare("
                SELECT t.*, 
                       u.nome as professor_nome,
                       m.tipo as material_tipo,
                       m.numero_inventario as material_inventario,
                       so.numero as sala_origem_numero,
                       so.nome as sala_origem_nome,
                       sd.numero as sala_destino_numero,
                       sd.nome as sala_destino_nome
                FROM transferencias t
                JOIN usuarios u ON t.professor_id = u.id
                JOIN materiais m ON t.material_id = m.id
                JOIN salas so ON t.sala_origem_id = so.id
                JOIN salas sd ON t.sala_destino_id = sd.id
                WHERE t.professor_id = ?
                ORDER BY t.data_transferencia DESC
                LIMIT 50
            ");
            $stmt->execute([$user_id]);
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'estatisticas':
        // Contar materiais por tipo
        $stmt = $db->query("
            SELECT tipo, COUNT(*) as total 
            FROM materiais 
            GROUP BY tipo
        ");
        $por_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Contar materiais por estado
        $stmt = $db->query("
            SELECT estado, COUNT(*) as total 
            FROM materiais 
            GROUP BY estado
        ");
        $por_estado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Contar materiais por sala
        $stmt = $db->query("
            SELECT s.numero, s.nome, COUNT(m.id) as total
            FROM salas s
            LEFT JOIN materiais m ON s.id = m.sala_id
            GROUP BY s.id
            ORDER BY s.numero
        ");
        $por_sala = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Total de transferências
        $stmt = $db->query("SELECT COUNT(*) as total FROM transferencias WHERE cancelada = 0");
        $total_transferencias = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode([
            'por_tipo' => $por_tipo,
            'por_estado' => $por_estado,
            'por_sala' => $por_sala,
            'total_transferencias' => $total_transferencias
        ]);
        break;
        
    default:
        echo json_encode(['error' => 'Ação inválida']);
}
?>