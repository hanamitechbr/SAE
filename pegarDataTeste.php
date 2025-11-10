<?php
// conexão apenas para testar
$host = "localhost";
$user = "root";
$pass = "";
$db = "sae";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Falha na conexão");
}

$c = "SELECT data FROM agendamentos WHERE id=31";
$r = $conn->query($c);
$data = [];

if ($r->num_rows > 0) {
  while ($row = $r->fetch_assoc()) {
    $data[] = $row;
  }
}

header('Content-Type: application/json');
echo json_encode($data);
exit;

// echo $dados;