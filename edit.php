<?php 
session_start();
include "db_conn.php";

// Verifica se o usuário está logado
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];

    // Verifica se os dados foram enviados via POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Coleta os dados do formulário
        $nome = htmlspecialchars($_POST['nome']);
        $telefone = htmlspecialchars($_POST['telefone']);
        $email = htmlspecialchars($_POST['email']);
        $cnpj = htmlspecialchars($_POST['cnpj']);
        $senha = htmlspecialchars($_POST['senha']);
        $senhaConfirmada = htmlspecialchars($_POST['senha_confirmada']);

        // Verifica se as senhas coincidem
        if ($senha !== $senhaConfirmada) {
            header("Location: edit.php?error=As senhas não coincidem!");
            exit();
        }

        // Atualiza a senha, caso tenha sido alterada
        if (!empty($senha)) {
            $senha = password_hash($senha, PASSWORD_DEFAULT);  // Criptografa a senha
        }

        // Atualiza os dados no banco de dados
        if (empty($senha)) {
            // Se a senha não foi alterada, não a atualiza
            $sql = "UPDATE hospitais SET nome = ?, telefone = ?, email = ?, cnpj = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nome, $telefone, $email, $cnpj, $id]);
        } else {
            // Caso contrário, também atualiza a senha
            $sql = "UPDATE hospitais SET nome = ?, telefone = ?, email = ?, cnpj = ?, senha = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nome, $telefone, $email, $cnpj, $senha, $id]);
        }

        // Atualiza os dados na sessão
        $_SESSION['nome'] = $nome;

        // Redireciona para a mesma página com uma mensagem de sucesso
        header("Location: edit.php?success=Dados atualizados com sucesso!");
        exit();
    }

    // Recupera os dados do hospital logado
    $sql = "SELECT nome, telefone, email, cnpj FROM hospitais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $hospital = $stmt->fetch();

    // Se o hospital não for encontrado
    if (!$hospital) {
        header("Location: indexdash.php");
        exit();
    }

    // Atribui os valores ao formulário
    $nome = htmlspecialchars($hospital['nome']);
    $telefone = htmlspecialchars($hospital['telefone']);
    $email = htmlspecialchars($hospital['email']);
    $cnpj = htmlspecialchars($hospital['cnpj']);

} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
        .botao {
            border-radius: 10px;
            width: 420px;
            height: 45px;
            cursor: pointer;
            border: 0;
            background-color: #008202;
            box-shadow: #008202 0 0 8px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 15px;
            transition: all 0.5s ease;
            color: white;
        }

        .botao:hover {
            letter-spacing: 3px;
            background-color: #016603;
            color: hsl(0, 0%, 100%);
            box-shadow: #008202 0px 7px 29px 0px;
        }

        .botao:active {
            letter-spacing: 3px;
            background-color: #008202;
            color: hsl(0, 0%, 100%);
            box-shadow: #008202 0px 0px 0px 0px;
            transform: translateY(10px);
            transition: 100ms;
        }

        #upload {
            background-color: #495057;
            color: white;
            padding: 0.5rem;
            font-family: sans-serif;
            border-radius: 0.3rem;
            cursor: pointer;
            margin-top: 1rem;
            width: 200px;
        }
        #upload:hover {
            background-color: #495057;
            color: hsl(0, 0%, 100%);
            box-shadow: #495057 0px 7px 29px 0px;
        }

        #file-chosen {
            margin-left: 0.3rem;
            font-family: sans-serif;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand" style="color: #628A4C;" gap="10px;">
            <img src="img/logoverde.png" height="55px"> 
            <div style="width: 30px; display: inline-block;"></div>MAPSUS
        </a>
        <ul class="side-menu">
            <a href="indexhosp.php"><img src="./img/dash.png" width="20px"> Dashboard</img></a>
            <li class="divider" data-text="Dados">Dados</li>
            <li><a href="medico.php"><img src="./img/med.png" width="20px" alt=""> MÉDICO </a></li>
            <li><a href="plantão.php"><img src="./img/plan.png" width="20px" alt=""> PLANTÃO </a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <nav>
            
        <div style="width: 1180px; display: inline-block;"></div>

            <div class="profile" style="display: flex; align-items: center;">
                
                <div style="margin-left: 10px;">
                    <p style="font-size: 12px; color: #666;">Bem-vindo, <strong><?php echo $nome; ?></strong>!</p>
                </div>
                <ul class="profile-link">
                    <li><a href="edit.php"><i class='bx bxs-cog'></i> Perfil</a></li>
                    <li><a href="index.php"><i class='bx bxs-log-out-circle'></i> Sair</a></li>
                </ul>
            </div>
        </nav>

        <!-- Formulário de Edição -->
        <div class="d-flex justify-content-center align-items-center vh-100">
            <form class="shadow w-450 p-3" action="edit.php" method="post">
                <h4 class="display-4 fs-1">Editar Hospital</h4><br>

                <!-- Exibe erro -->
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_GET['error']; ?>
                    </div>
                <?php } ?>

                <!-- Exibe sucesso -->
                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_GET['success']; ?>
                    </div>
                <?php } ?>

                <div class="mb-3">
                    <label class="form-label">Nome do Hospital</label>
                    <input type="text" class="form-control" name="nome" value="<?php echo $nome; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" value="<?php echo $telefone; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">CNPJ</label>
                    <input type="text" class="form-control" name="cnpj" value="<?php echo $cnpj; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nova Senha</label>
                    <input type="password" class="form-control" name="senha" placeholder="Digite uma nova senha">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar Nova Senha</label>
                    <input type="password" class="form-control" name="senha_confirmada" placeholder="Confirme a nova senha">
                </div>

                <button type="submit" class="btn botao">Salvar Alterações</button>
            </form>
        </div>
    </section>
</body>
</html>
