<?php
include 'db_conn.php'; // Inclui o arquivo de conexão com o banco de dados
session_start(); // Inicia a sessão para obter o ID do hospital

// Conexão com o banco e processamento dos dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicoId = $_POST['medicoId']; // O ID do médico
    $medicoNome = $_POST['medicoNome']; // O nome do médico
    $especialidade = $_POST['especialidade'];
    $horaInicio = $_POST['horaInicio'];
    $horaSaida = $_POST['horaSaida'];
    $data = $_POST['data'];

    // Inserção no banco de dados
    $sql_insert = "INSERT INTO plantao (medico, especialidade, horaEnt, horaSai, Data, id_hospital) 
                   VALUES (?, ?, ?, ?, ?, ?)"; 
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->execute([$medicoNome, $especialidade, $horaInicio, $horaSaida, $data, $_SESSION['id']]);

    // Retornar sucesso
    echo json_encode(['success' => true]);
}
?>


