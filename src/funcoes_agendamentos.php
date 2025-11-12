<?php
require_once __DIR__ . "/database.php";

/**
 * Retorna aulas ocupadas de uma data
 */
function getAulasOcupadas($data)
{
  $sql = "SELECT DISTINCT aula FROM agendamentos WHERE data = ?";
  $stmt = db()->prepare($sql);
  $stmt->execute([$data]);
  return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Retorna equipamentos reservados (e suas quantidades) em uma data/aula
 */
function getEquipamentosReservados($data, $aula)
{
  $sql = "
    SELECT e.id AS equipamento_id, e.nome_equip, e.tipo,
           COALESCE(SUM(r.quantidade), 0) AS reservados,
           e.quantidade_total
    FROM equipamentos e
    LEFT JOIN reservas_equipamentos r ON e.id = r.equipamento_id
    LEFT JOIN agendamentos a ON a.id = r.agendamento_id
      AND a.data = ? AND a.aula = ?
    GROUP BY e.id
  ";
  $stmt = db()->prepare($sql);
  $stmt->execute([$data, $aula]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Cadastra um novo agendamento com equipamentos
 */
function salvarAgendamento($professor_id, $data, $aulas, $equipamentos)
{
  $pdo = db();
  $pdo->beginTransaction();

  try {
    foreach ($aulas as $aula) {
      $stmt = $pdo->prepare("
        INSERT INTO agendamentos (professor_id, data, aula)
        VALUES (?, ?, ?)
      ");
      $stmt->execute([$professor_id, $data, $aula]);
      $agendamento_id = $pdo->lastInsertId();

      foreach ($equipamentos as $equip_id => $quantidade) {
        $stmt2 = $pdo->prepare("
          INSERT INTO reservas_equipamentos (agendamento_id, equipamento_id, quantidade)
          VALUES (?, ?, ?)
        ");
        $stmt2->execute([$agendamento_id, $equip_id, $quantidade]);
      }
    }

    $pdo->commit();
    return true;
  } catch (Exception $e) {
    $pdo->rollBack();
    error_log("Erro ao salvar agendamento: " . $e->getMessage());
    return false;
  }
}
