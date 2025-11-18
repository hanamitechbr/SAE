<?php
require "../database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $professor_id = 1;
    $periodo      = 1;
    $data         = $_POST['data'];
                                                     // Ajuste 1: Se o input for de texto (e não type="date"), garanta que o formato seja Y-m-d (MySQL)
    $data_para_bd = date('Y-m-d', strtotime($data)); // Use a variável que será inserida

    $aulas_selecionadas = $_POST['aulas'] ?? [];

    $sql = "INSERT INTO agendamentos (professor_id, data, aula, periodo) VALUES (:professor_id, :data, :aula, :periodo)";

    $pdo = db();
    try {
        $stmt = $pdo->prepare($sql);
    } catch (\PDOException $e) {
        die("Erro na preparação da consulta" . $e->getMessage());
    }

    $sucesso = 0;
    $erros   = 0;

    $pdo->beginTransaction();

    try {
        foreach ($aulas_selecionadas as $aula) {
            $aula = intval($aula);
            if ($aula < 1 || $aula > 6) {
                continue;
            }

            // Ajuste 2: Mover os bindValue e a execução para dentro do loop
            $stmt->bindValue(':professor_id', $professor_id, PDO::PARAM_INT);
            $stmt->bindValue(':data', $data_para_bd, PDO::PARAM_STR); // Ajuste 3: Usar a data formatada
            $stmt->bindValue(':aula', $aula, PDO::PARAM_INT);         // Usar o valor $aula do loop atual
            $stmt->bindValue(':periodo', $periodo, PDO::PARAM_INT);

            try {
                $stmt->execute();
                $sucesso++;
            } catch (\PDOException $e) {
                if ($e->getCode() == '23000' && strpos($e->getMessage(), '1062') !== false) {
                    echo "Aviso: O professor $professor_id já possui a aula $aula na data $data agendada.<br>";
                    $erros++;
                } else {
                    throw $e;
                }
            }
        } // Fim do loop

        $pdo->commit();
        if ($sucesso) {
            $_SESSION['mensagem']      = "Agendamento cadastrado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";
        } elseif ($erros > 0) {
            $_SESSION['mensagem'] = "Alguns agendamentos foram ignorados por duplicidade: $erros";
            $_SESSION['tipo_mensagem'] = "error";
        }

        header("Location: public/fazer_agendamento.php");
        // echo "Sucesso! Agendamento(s) cadastrado(s) **$sucesso**,<br>";
        // if ($erros > 0) {
        //     echo "Agendamento(s) ignorado(s) por duplicidade: **$erros**.<br>";
        // }
    } catch (\Exception $e) {
        $pdo->rollBack();
        echo "Erro fatal durante o agendamento:" . $e->getMessage();
    }
} else {
    echo "Acesso inválido. Use o formulário.";
}
