<?php
include '../database.php';

header('Content-Type: application/json');

// Get parameters
$data        = $_GET['data'] ?? '';
$periodo     = $_GET['periodo'] ?? '';
$equipamento = $_GET['equipamento'] ?? '';

if (empty($data) || empty($periodo)) {
    echo json_encode(['error' => 'Data e período são obrigatórios']);
    exit;
}

try {
    if (empty($equipamento)) {
        // If no equipment selected, return empty array
        echo json_encode([]);
        exit;
    }

    // Determine equipment type and name
    $equipamento_tipo = '';
    $equipamento_nome = '';

    if ($equipamento === 'laboratorio') {
        $equipamento_tipo = 'laboratorio';
        $equipamento_nome = $_GET['laboratorio'] ?? '';
    } elseif ($equipamento === 'guardiao') {
        $equipamento_tipo = 'guardiao';
        $equipamento_nome = $_GET['guardiao'] ?? '';
    }

    if (empty($equipamento_nome)) {
        // If specific equipment not selected, return empty array
        echo json_encode([]);
        exit;
    }

    // Query to get occupied aulas for the specific equipment
    $stmt = $pdo->prepare("
        SELECT a.aula, p.nome_professor, e.nome_equip
        FROM agendamentos a
        JOIN equipamentos e ON a.equipamento_id = e.id
        JOIN professores p ON a.professor_id = p.id
        WHERE a.data = ? AND a.periodo = ? AND e.tipo = ? AND e.nome_equip = ?
    ");

    $stmt->execute([$data, $periodo, $equipamento_tipo, $equipamento_nome]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group aulas by professor and equipment
    $result = [];
    foreach ($agendamentos as $agendamento) {
        $aulas = explode(',', $agendamento['aula']); // Assuming aulas are stored as comma-separated
        foreach ($aulas as $aula) {
            $result[] = [
                'aula'           => trim($aula),
                'nome_professor' => $agendamento['nome_professor'],
                'nome_equip'     => $agendamento['nome_equip'],
            ];
        }
    }

    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao buscar agendamentos: ' . $e->getMessage()]);
}
