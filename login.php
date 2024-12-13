<?php
session_start();

require "conexao.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFile = 'log.txt';
function logMessage($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    if ($conn->connect_error) {
        logMessage("Erro de conexão: " . $conn->connect_error);
        exit("Erro de conexão com o banco de dados.");
    }

    $sql = "SELECT * FROM hospitais WHERE Usuario = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $dados = $result->fetch_assoc();
            logMessage("Tentativa de login para o usuário: $usuario");

            // Verificar a senha usando password_verify para senhas criptografadas
            if (password_verify($senha, $dados['Senha'])) {
                $_SESSION['id'] = $dados['id'];
                $_SESSION['usuario'] = $dados['Usuario'];
                $_SESSION['email'] = $dados['Email'];

                header('Location: indexhosp.php');
                exit;
            } else {
                logMessage("Senha incorreta para o usuário: $usuario");
                exit("Senha incorreta.");
            }
        } else {
            logMessage("Usuário não encontrado: $usuario");
            exit("Usuário não encontrado.");
        }

        $stmt->close();
    } else {
        logMessage("Erro na preparação da consulta SQL.");
        exit("Erro na preparação da consulta SQL.");
    }

    $conn->close();
}
?>
