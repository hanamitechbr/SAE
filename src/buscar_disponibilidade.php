<?php
require "../database.php";

header('Content-Type: application/json');

$data = $_GET['data'] ?? '';

if (empty($data)) {
    echo json_encode(['error' => 'Data não fornecida']);
    exit;
}

$data_para_db = date('Y-m-d', strtotime($data));

$sql = "
  SELECT a.aula, p.nome AS nome_professor
  FROM agendamentos a
  JOIN professores p ON a.professor_id = p.id
  WHERE a.data = :data
  ORDER BY a.aula;
";

$pdo                  = db();
$aulas_ocupadas       = [];
$total_aulas_ocupadas = 0;
$total_aulas          = 6;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':data', $data_para_bd, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $aula_num = (int) $row['aula'];

        // Armazena a aula e o nome do professor para retorno
        $aulas_ocupadas[$aula_num] = [
            'ocupada'   => true,
            'professor' => $row['nome_professor'],
        ];
        $total_aulas_ocupadas++;
    }

    $dia_totalmente_ocupado = ($total_aulas_ocupadas >= $total_aulas);

    // 6. Prepara a resposta em JSON
    $resposta = [
        'dia_bloqueado'  => $dia_totalmente_ocupado,
        'aulas_ocupadas' => $aulas_ocupadas,
        'total_ocupadas' => $total_aulas_ocupadas,
    ];

    echo json_encode($resposta);
} catch (\PDOException $e) {
    // Trata erros de banco de dados
    http_response_code(500);
    echo json_encode(['error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
