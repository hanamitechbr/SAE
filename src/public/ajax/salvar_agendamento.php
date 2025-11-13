<?php
require_once "../../src/funcoes_agendamentos.php";

$data = $_POST['data'];
$aulas = $_POST['aulas']; // array
$equipamentos = $_POST['equipamentos']; // ex: [1 => null, 5 => 23]
$professor_id = $_POST['professor_id'];

$resultado = salvarAgendamento($professor_id, $data, $aulas, $equipamentos);

header("Content-Type: application/json");
echo json_encode(["sucesso" => $resultado]);
