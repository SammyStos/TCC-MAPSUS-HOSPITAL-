<?php
session_start();
include "conexao.php";

// Adicionando cabeçalhos para evitar cache
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];

    // Contar o número total de médicos
    $sql_count_medic = "SELECT COUNT(id) AS total_medicos FROM medicos WHERE id_hospital = ?";
    $stmt_count = $conn->prepare($sql_count_medic);
    $stmt_count->bind_param('i', $id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $total_medic = $result_count->fetch_assoc()['total_medicos'];

    // Contar médicos por especialidade
    $sql_specialty = "SELECT especialidade, COUNT(*) AS quantidade 
                      FROM medicos 
                      WHERE id_hospital = ? 
                      GROUP BY especialidade";
    $stmt_specialty = $conn->prepare($sql_specialty);
    $stmt_specialty->bind_param('i', $id);
    $stmt_specialty->execute();
    $result_specialty = $stmt_specialty->get_result();

    $especialidades = [];
    $quantidades = [];
    while ($row = $result_specialty->fetch_assoc()) {
        $especialidades[] = $row['especialidade'];
        $quantidades[] = $row['quantidade'];
    }

    // Sempre recarregar os dados do hospital no banco de dados
    $sql = "SELECT Nome, pp FROM hospitais WHERE id = ?";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result_user = $stmt->get_result();
        if ($result_user->num_rows === 1) {
            $user = $result_user->fetch_assoc();
            $Nome = htmlspecialchars($user['Nome']);
            $pp = htmlspecialchars($user['pp']);
            $_SESSION['Nome'] = $Nome;  // Sempre atualizar o nome da sessão
            $_SESSION['pp'] = $pp;      // Sempre atualizar a foto de perfil da sessão

            $pp_path = 'upload/' . $pp;
            if (!file_exists($pp_path)) {
                $pp_path = 'upload/default-pp.png';
            }
        } else {
            // Caso o hospital não seja encontrado, redireciona para a página de login
            header("Location: ../indexhosp.php");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        echo "Erro ao buscar dados do usuário: " . $e->getMessage();
        exit();
    }

    // Verificando se o formulário de edição foi enviado para atualizar o nome
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Nome'])) {
        $newName = htmlspecialchars($_POST['Nome']);

        // Atualizando no banco de dados
        $sql_update = "UPDATE hospitais SET Nome = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('si', $newName, $id);

        if ($stmt_update->execute()) {
            // Atualizando o nome na sessão
            $_SESSION['Nome'] = $newName;

            // Redirecionando para garantir que a nova sessão seja usada
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Erro ao atualizar o nome.";
        }
    }
} else {
    header("Location: ../indexhosp.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>MAPSUS</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Especialidade', 'Quantidade'],
                <?php
                foreach ($especialidades as $index => $especialidade) {
                    echo "['$especialidade', $quantidades[$index]],";
                }
                ?>
            ]);

            var options = {
                title: 'Distribuição de Especialidades Médicas',
                is3D: true,
                colors: ['#628A4C', '#e0e0e0', '#9cbb57', '#b3df8a', '#8FBC8F', '#66CDAA', '#3CB371', '#2E8B57']
            };

            var chart = new google.visualization.PieChart(document.getElementById('graficoUsuarios'));
            chart.draw(data, options);
        }
    </script>
</head>
<style>
.white{
    color: white;
}

</style>
<body>

    <section id="sidebar">
        <a href="#" class="brand" style="color: #628A4C;" gap="10px;">
            <img src="img/logoverde.png" height="55px"> 
            <div style="width: 30px; display: inline-block;"></div>MAPSUS
        </a>
        <ul class="side-menu">
            <a href="indexhosp.php" class="active" style="background-color: #628A4C; color: white;"><img src="./img/dashb.png" width="20px">Dashboard</img></a>
            <li class="divider" data-text="Dados">Dados</li>
            <li><a href="medico.php"><img src="./img/med.png" width="20px" alt=""> MÉDICO </a></li>
            <li><a href="plantão.php"><img src="./img/plan.png" width="20px" alt=""> PLANTÃO </a></li>
        </ul>
    </section>

    <section id="content">
        <nav>
            <div style="width: 1200px; display: inline-block;"></div>
            <span class="divider"></span>
            <div class="profile" style="display: flex; align-items: center; position: relative;">
                <div style="margin-left: 10px;">
                    <p style="font-size: 12px; color: #666;">Bem-vindo, <strong><?php echo $Nome; ?></strong></p>
                </div>
                <img src="./img/menu.png" alt="Profile Picture" style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%;" onclick="toggleMenu()">
                
                <ul class="profile-link" id="profileMenu">
                    <li><a href="edit.php"><i class='bx bxs-cog'></i>Config</a></li>
                    <li><a href="cadastrar.php"><i class='bx bxs-log-out-circle'></i> Sair</a></li>
                </ul>
            </div>
        </nav>
        <main>
            <h1 class="title">Dashboard</h1>
            <ul class="breadcrumbs">
                <li><a href="#" style="color: #000;">Home</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Dashboard</a></li>
            </ul>
            <div class="info-data">
                <div class="card">
                    <div class="head">
                        <div>
                            <h2><?php echo $total_medic; ?></h2>
                            <p>médicos cadastrados</p>
                        </div>
                    </div>
                    <span class="progress" data-value="40%"></span>
                </div>
            </div>
            <div class="info-data">
                <div class="card">
                    <div class="head">
                        <div id="graficoUsuarios" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script src="script.js"></script>
    <script>
        function toggleMenu() {
            const menu = document.getElementById('profileMenu');
            menu.classList.toggle('show'); // Adiciona ou remove a classe "show"
        }

        document.addEventListener('click', function(event) {
            const profileMenu = document.getElementById('profileMenu');
            if (!profileMenu.contains(event.target) && !event.target.matches('.profile img')) {
                profileMenu.classList.remove('show');
            }
        });
    </script>
</body>
</html>
