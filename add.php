<?php
session_start();
include "db_conn.php";

if (isset($_SESSION['id'])) {
    $id_hospital = $_SESSION['id'];

    if (isset($_POST['Nome']) && isset($_POST['Especialidade']) && isset($_POST['Crm'])) {
        $nome = $_POST['Nome'];
        $especialidade = $_POST['Especialidade'];
        $crm = $_POST['Crm'];

        $sql_insert = "INSERT INTO medicos (Nome, Especialidade, Crm, id_hospital) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);

        try {
            $stmt_insert->execute([$nome, $especialidade, $crm, $id_hospital]);
            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Campos obrigatórios não preenchidos"]);
    }
}
?>
