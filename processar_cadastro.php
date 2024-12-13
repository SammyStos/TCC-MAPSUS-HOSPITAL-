<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro</title>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #4CAF50;
        }
        .container {
            background-color: #fff; 
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .icon {
            font-size: 50px;
            color: #4CAF50; 
            margin-bottom: 15px;
        }
        .message {
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="icon"><i class="fas fa-check-circle"></i></div>
    <div class="message">
        <?php
        include 'conexao.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];

            $sql = "INSERT INTO notificacoes (usuario_id, nome, email, lida) VALUES (NULL, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $nome, $email);

            if ($stmt->execute()) {
                echo "Cadastro enviado com sucesso! Aguarde a aprovação no seu email.";
            } else {
                echo "Erro ao enviar cadastro.";
            }

            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</div>

<script>
     //Redireciona para a página de login após 5 segundos(somente se precisar)
    setTimeout(function() {
      window.location.href =  'precadastro.php';
    }, 5000);
</script>

</body>
</html>
