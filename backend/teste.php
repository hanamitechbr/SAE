<?php
// session_start();
include '../database.php'; // cria $pdo

$totalAulas = 6;
$dataSelecionada = $_GET['data'] ?? null; // ou $_POST['data']

if (!$dataSelecionada) {
    echo json_encode([]);
    exit;
}

$pdo = getConnection();
$sql  = "SELECT aula FROM agendamentos WHERE data = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$dataSelecionada]);

$aulasOcupadas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
$aulasOcupadas = array_map('intval', $aulasOcupadas);

// Calcular aulas disponíveis
$disponiveis = [];
for ($i = 1; $i <= $totalAulas; $i++) {
    if (!in_array($i, $aulasOcupadas)) {
        $disponiveis[] = $i;
    }
}

// Retorna JSON com as aulas livres
header('Content-Type: application/json');
echo json_encode($disponiveis);
exit;
