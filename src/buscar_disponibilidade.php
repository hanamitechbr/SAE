<?php
require "../database.php"; // Importa a conexão com o banco

header('Content-Type: application/json'); // Define o retorno como JSON

// Captura a data enviada via GET
$data = $_GET['data'] ?? '';
$equipamento_informatica = $_GET['lab-informatica'];

if (empty($data)) {
    echo json_encode(['error' => 'Data não fornecida']);
    exit;
}

// Converte a data recebida para o formato do banco (YYYY-MM-DD)
$data_para_db = date('Y-m-d', strtotime($data));

// Query para buscar aulas já agendadas na data escolhida
$sql = "
  SELECT a.aula, p.nome AS nome_professor
  FROM agendamentos a
  JOIN professores p ON a.professor_id = p.id
  WHERE a.data = :data
  ORDER BY a.aula;
";

$pdo                  = db(); // Conexão com o banco
$aulas_ocupadas       = [];   // Array para armazenar aulas ocupadas
$total_aulas_ocupadas = 0;    // Contador de aulas ocupadas
$total_aulas          = 6;    // Número total de aulas possíveis no dia

try {
    // Prepara a query
    $stmt = $pdo->prepare($sql);
    // Corrigido: variável correta é $data_para_db
    $stmt->bindValue(':data', $data_para_db, PDO::PARAM_STR);
    $stmt->execute();

    // Percorre os resultados e marca as aulas ocupadas
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $aula_num = (int) $row['aula'];

        // Armazena a aula ocupada e o professor responsável
        $aulas_ocupadas[$aula_num] = [
            'ocupada'   => true,
            'professor' => $row['nome_professor'],
        ];
        $total_aulas_ocupadas++;
    }

    // Verifica se todas as aulas do dia estão ocupadas
    $dia_totalmente_ocupado = ($total_aulas_ocupadas >= $total_aulas);

    // Prepara a resposta em JSON
    $resposta = [
        'dia_bloqueado'  => $dia_totalmente_ocupado, // true se todas ocupadas
        'aulas_ocupadas' => $aulas_ocupadas,         // lista das aulas ocupadas
        'total_ocupadas' => $total_aulas_ocupadas,   // número de aulas ocupadas
    ];

    echo json_encode($resposta);
} catch (\PDOException $e) {
    // Trata erros de banco de dados
    http_response_code(500);
    echo json_encode(['error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
