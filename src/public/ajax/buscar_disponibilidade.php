<?php
require_once "../../src/funcoes_agendamentos.php";

$data = $_GET['data'] ?? null;
$aula = $_GET['aula'] ?? null;

if (!$data) {
  http_response_code(400);
  echo json_encode(["erro" => "Data obrigatória"]);
  exit;
}

$response = [
  "aulas_ocupadas" => getAulasOcupadas($data),
  "equipamentos" => $aula ? getEquipamentosReservados($data, $aula) : []
];

header("Content-Type: application/json");
echo json_encode($response);
