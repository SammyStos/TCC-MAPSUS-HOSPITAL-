<?php
session_start();
include "db_conn.php";

if (isset($_SESSION['id'])) {
  $id = $_SESSION['id']; 
  // Verificar e excluir plantões com data passada
  $sql_delete_past_plantoes = "DELETE FROM plantao WHERE Data < CURDATE()";
  $stmt_delete_past_plantoes = $conn->prepare($sql_delete_past_plantoes);
  try {
      $stmt_delete_past_plantoes->execute();
   
  } catch (PDOException $e) {
      echo "Erro ao excluir plantões passados: " . $e->getMessage();
  }

  // Consultar todas as especialidades
  $sql_especialidades = "SELECT id, Nome FROM especialidades";
  $stmt_especialidades = $conn->prepare($sql_especialidades);
  $stmt_especialidades->execute();
  $especialidades = $stmt_especialidades->fetchAll(PDO::FETCH_ASSOC);

  $allowed_columns = ['Medico', 'Especialidade', 'HoraEnt', 'HoraSai', 'ID'];
  $sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_columns) ? $_GET['sort'] : 'HoraEnt';
  
  $allowed_orders = ['ASC', 'DESC'];
  $sort_order = isset($_GET['order']) && in_array($_GET['order'], $allowed_orders) ? $_GET['order'] : 'ASC';

  // Recuperar todos os plantões do hospital logado
  $sql_all_plan = "SELECT id, medico, Data, horaEnt, horaSai, especialidade FROM plantao WHERE id_hospital = ?";
  $params = [$id];

    if (!empty($_GET['medico'])) {
        $sql_all_plan .= " AND medico LIKE ?";
        $params[] = '%' . $_GET['medico'] . '%';
    }

    if (!empty($_GET['Especialidade'])) {
        $sql_all_plan .= " AND especialidade LIKE ?";
        $params[] = '%' . $_GET['Especialidade'] . '%';
    }
  
  $sql_all_plan .= " ORDER BY $sort_column $sort_order";

  $stmt_all_plan = $conn->prepare($sql_all_plan);
    $stmt_all_plan->execute($params);
    $plantoes = $stmt_all_plan->fetchAll(PDO::FETCH_ASSOC);



  // Código para carregar os médicos ao abrir o modal, agora filtrando pelo id_hospital
  $medicos = [];
  $sql_medicos = "SELECT id, nome, especialidade FROM medicos WHERE id_hospital = ?";  // Filtra médicos pelo id_hospital
  $stmt_medicos = $conn->prepare($sql_medicos);
  $stmt_medicos->execute([$id]);  // Passa o id do hospital logado para a consulta
  $medicos = $stmt_medicos->fetchAll(PDO::FETCH_ASSOC);

  // Lógica de exclusão
  if (isset($_POST['delete'])) {
      $planId = $_POST['id'];
      $sql_delete_plan = "DELETE FROM plantao WHERE id = ?";
      $stmt_delete = $conn->prepare($sql_delete_plan);
      try {
          $stmt_delete->execute([$planId]);
          echo "<script>alert('Plantão excluído com sucesso!');</script>";
          header("Refresh:0"); 
          exit();
      } catch (PDOException $e) {
          echo "Erro ao excluir o plantão: " . $e->getMessage();
      }
  }

  // Processar edição
  if (isset($_POST['edit'])) {
      $plan_id = $_POST['id'];
      $sql_edit = "SELECT * FROM plantao WHERE id = ?";
      $stmt_edit = $conn->prepare($sql_edit);
      $stmt_edit->execute([$plan_id]);
      $plan_data = $stmt_edit->fetch(PDO::FETCH_ASSOC);
  }

// Processar atualização
if (isset($_POST['update'])) {
    $plan_id = $_POST['id'];
    $medico_id = $_POST['medico'];  // Aqui é o ID do médico
    $horaEnt = $_POST['horaEnt'];
    $horaSai = $_POST['horaSai'];
    $especialidade = $_POST['especialidade'];

    // Recuperar o nome do médico a partir do ID
    $sql_medico_nome = "SELECT nome FROM medicos WHERE id = ?";
    $stmt_medico_nome = $conn->prepare($sql_medico_nome);
    $stmt_medico_nome->execute([$medico_id]);
    $medico = $stmt_medico_nome->fetch(PDO::FETCH_ASSOC);
    $nomeMedico = $medico['nome'];  // Aqui você armazena o nome do médico

    // Update the plantão in the database
    // Atualizar o plantão no banco de dados com o ID do médico, e não o nome
$sql_update = "UPDATE plantao SET medico = ?, horaEnt = ?, horaSai = ?, especialidade = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->execute([$medico_id, $horaEnt, $horaSai, $especialidade, $plan_id]);


    // Redirect to refresh the page
    header("Location: plantão.php");
    exit();
}

}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estiloini.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="./style.css">
    <title>MAPSUS</title>
</head>
<body>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

/* Navbar */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: white;
    padding: 20px 20px;
	box-shadow: #495057 0px 7px 29px 0px;
	
}

.navbar-logo {
    display: flex;
    align-items: center;
}

.navbar-logo img {
    height: 60px;
    margin-right: 10px;
}

table {
  margin: 50px;
  border: 1px solid black;
  border-collapse: collapse;
  background-color: #f5f5f5;
  width: 90%;
  margin-bottom: 20px;
  user-select: none;
}

th, td {
  padding: 8px;
  text-align: left;
  border: solid #000;
}

th {
  user-select: none;
  background-color: #333;
  color: #fff;
}

tr:nth-child(even) {
  background-color: #ddd;
}

tr:hover {
  background-color: #ccc;
}

.btn-table {
    background-color: #628A4C;
    border: 1px solid #628A4C;
    padding: 5px 10px; /* Ajuste do padding interno para reduzir o tamanho do botão */
    border-radius: 5px;
    width: auto; /* Deixa o botão com tamanho automático */
    margin: 0; /* Remove margem ao redor do botão */
    color: #fff;
    transition: background-color .5s;
}

td.botao2 {
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px; /* Menor espaçamento entre os botões */
  padding: 0; /* Remove o padding interno da célula para reduzir espaço */
  border: 1px solid #000;
}

.nav-buttons .btn{
    margin-left: -70%;
}
button {
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
.verde{
  
  background-color: #02c405; 
}
.vermelho{
  
  background-color: red; 
}

        .vermelho:hover {
            background-color: #c82333; 
        }
        .verde:hover {
            background-color: #008202; 
        }
.edit-form {
            display: none;
        }
      .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #fff;
    padding: 25px;
    border-radius: 10px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
}
#addProductForm {
    display: flex;
    flex-direction: column;
    gap: 15px;
}


#addProductForm label {
    font-size: 14px;
    color: #555;
    font-weight: 500;
    margin-bottom: 5px;
}

#addProductForm input[type="text"],
#addProductForm input[type="number"],
#addProductForm input[type="date"],
#addProductForm input[type="url"],
#addProductForm textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    resize: vertical;
}

#addProductForm textarea {
    min-height: 80px;
}


#addProductForm button[type="button"] {
    background-color: #008202;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
}

#addProductForm button[type="button"]:hover {
    background-color: #016603; 
}

.add-product-container {
    width: 100%;
    max-width: 1200px; 
    display: flex;
    justify-content: flex-end;
    margin: 10px 0;
}

.add-product-icon {
    width: 40px;
    cursor: pointer;
} 
.white{
    color: white;
}
</style>
<section id="sidebar">
	<a href="#" class="brand" style="color: #628A4C;"  gap= "10px;"><img src="img/logoverde.png" height="55px"> 
    <div style="width: 30px; display: inline-block;"></div>MAPSUS</a>
		<ul class="side-menu">
        <a href="indexhosp.php"><img src="./img/dash.png"  width="20px"> Dashboard</img></a>
			<li class="divider" data-text="Dados">Dados</li>
			<li><a href="medico.php"><i class='' ><img src="./img/med.png" width="20px" alt=""></i>MÉDICO </a></li>
			<li><a href="plantão.php" class="active" style="background-color: #628A4C;"><i class='' ><img src="./img/planb.png" width="20px" alt=""></i>PLANTÃO </a></li>
		</ul>
	</section>
  <section id="content">
<header>
        <nav class="navbar">
            <div class="logo">
                <span>Plantão</span>
            </div>
            <div class="nav-buttons">
              <a > <button class="btn" class="add-product-icon" 
              onclick="openForm()">adicionar</button></a>
            </div>
            
    </div>
    </header>
    <div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeForm()">×</span>
        <form id="addProductForm" onsubmit="event.preventDefault(); addDoctor();">
            <label for="Medico">Médico:</label>
            <select id="Medico" name="Medico" required onchange="updateEspecialidade()">
                <option value="" disabled selected>Selecione um médico</option>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?php echo $medico['id']; ?>" data-especialidade="<?php echo $medico['especialidade']; ?>">
                        <?php echo $medico['nome']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="Especialidade">Especialidade:</label>
            <input type="text" id="Especialidade" name="Especialidade" readonly>

            <label for="HorarioInicio">Horário de Início:</label>
            <input type="time" id="HorarioInicio" name="HorarioInicio" required>

            <label for="HorarioSaida">Horário de Saída:</label>
            <input type="time" id="HorarioSaida" name="HorarioSaida" required>

            <button type="button" class="salvar" onclick="addProductModal()">Salvar</button>

        </form>
    </div>
</div>

<main>
			<!--<h1 class="title">Dashboard</h1>-->
			<ul class="breadcrumbs">
				<li><a href="indexhosp.php" style="color: #000;">Home</a></li>
				<li class="divider">/</li>
				<li><a href="#" class="active">Plantão</a></li>
			</ul>
			

          <!--  <div class="info-data">
                <div class="card">
                    <div class="head">
                        <div id="graficoUsuarios"></div>       
                    </div>
                </div>
            </div> -->
		
		<!-- MAIN -->
        <div class="info-data">
                <div class="card">
                    <form method="GET" action="">
                        <label for="Nome">Médico:</label>
                        <select name="medico">
                        <option value="" <?php echo empty($_GET['medico']) ? 'selected' : ''; ?>>Selecione um médico</option>
                        <?php foreach ($medicos as $medico): ?>
                            <option value="<?php echo htmlspecialchars($medico['nome']); ?>"
                                <?php echo (isset($_GET['medico']) && $_GET['medico'] == $medico['nome']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($medico['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                        <label for="Especialidade">Especialidade:</label>
                        
                        <select name="Especialidade">
                        <option value="" <?php echo empty($_GET['Especialidade']) ? 'selected' : ''; ?>>Selecione uma especialidade</option>
                        <?php foreach ($especialidades as $especialidade): ?>
                            <option value="<?php echo htmlspecialchars($especialidade['Nome']); ?>"
                                <?php echo (isset($_GET['Especialidade']) && $_GET['Especialidade'] == $especialidade['Nome']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($especialidade['Nome']); ?>
                            </option>
                        <?php endforeach; ?>
                        </select>


                        
                        
                        <button type="submit" class="verde">Filtrar</button>
                        <button type="button" class="vermelho" onclick="window.location.href = 'plantão.php';">Limpar Filtros</button>
                    </form>
                </div>
            </div>

    <div class="user-list">
                <table>
                    <thead>
                        <tr>
                            <th><a class="white"  href="?sort=id&order=<?php echo ($sort_column == 'ID' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a class="white"  href="?sort=Medico&order=<?php echo ($sort_column == 'Medico' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Médico</a></th>
                            <th>Data</th>
                            <th><a class="white"  href="?sort=HoraEnt&order=<?php echo ($sort_column == 'HoraEnt' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Horário entrada</a></th>
                            <th><a class="white"  href="?sort=HoraSai&order=<?php echo ($sort_column == 'HoraSai' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Horário saída</a></th>
                            <th><a class="white"  href="?sort=Especialidade&order=<?php echo ($sort_column == 'Especialidade' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Especialidade</a></th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plantoes as $plantao) : ?>
                            <tr>
                                <td><?php echo $plantao['id']; ?></td>
                                <td><?php echo $plantao['medico']; ?></td>
                                <td><?php echo $plantao['Data']; ?></td>
                                <td><?php echo $plantao['horaEnt']; ?></td>
                                <td><?php echo $plantao['horaSai']; ?></td>
                                <td><?php echo $plantao['especialidade']; ?></td>
                                <td>
                                <button class="verde" onclick="toggleEditForm(<?php echo $plantao['id']; ?>)">Editar</button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($plantao['id']); ?>">
                                    <button type="submit" class="vermelho" name="delete" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</button>
                                </form>
                            </td>
                            </tr>
                            <tr id="edit-form-<?php echo $plantao['id']; ?>" class="edit-form">
    <td colspan="9">
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($plantao['id']); ?>">

            <!-- O campo Médico não será mais editável -->
            <input type="text" name="medico" value="<?php echo htmlspecialchars($plantao['medico']); ?>" readonly>

            <!-- O campo Data também será apenas leitura -->
            <input type="text" name="Data" value="<?php echo htmlspecialchars($plantao['Data']); ?>" readonly>

            <!-- Apenas os campos de hora podem ser editados -->
            <label for="horaEnt">Hora de Entrada:</label>
            <input type="time" name="horaEnt" value="<?php echo htmlspecialchars($plantao['horaEnt']); ?>" required>

            <label for="horaSai">Hora de Saída:</label>
            <input type="time" name="horaSai" value="<?php echo htmlspecialchars($plantao['horaSai']); ?>" required>

            <!-- O campo Especialidade será mantido como apenas leitura -->
            <input type="text" name="especialidade" value="<?php echo htmlspecialchars($plantao['especialidade']); ?>" readonly>

            <!-- Botões para salvar ou cancelar a edição -->
            <button type="submit" class="verde" name="update">Salvar</button>
            <button type="button" class="vermelho" onclick="toggleEditForm(<?php echo $plantao['id']; ?>)">Cancelar</button>
        </form>
    </td>
</tr>


                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </section>
    <script>
        function toggleEditForm(id) {
    const editForm = document.getElementById('edit-form-' + id);
    if (editForm.style.display === 'table-row') {
        editForm.style.display = 'none';
    } else {
        editForm.style.display = 'table-row';
    }
}


        function openForm() {
    document.getElementById('addProductModal').style.display = 'flex';
}

function closeForm() {
    document.getElementById('addProductModal').style.display = 'none';
}

function updateEspecialidade() {
    const medicoSelect = document.getElementById('Medico');
    const selectedOption = medicoSelect.options[medicoSelect.selectedIndex];
    const especialidade = selectedOption.getAttribute('data-especialidade');
    document.getElementById('Especialidade').value = especialidade || '';
}function addProductModal() {
    const medicoSelect = document.getElementById('Medico');
    const medicoId = medicoSelect.value; // ID do médico
    const medicoNome = medicoSelect.options[medicoSelect.selectedIndex].text; // Nome do médico
    const especialidade = document.getElementById('Especialidade').value;
    const horarioInicio = document.getElementById('HorarioInicio').value;
    const horarioSaida = document.getElementById('HorarioSaida').value;
    const data = new Date().toISOString().split('T')[0]; // Data no formato 'YYYY-MM-DD'

    // Validando campos antes de enviar
    if (!medicoId || !especialidade || !horarioInicio || !horarioSaida) {
        alert("Por favor, preencha todos os campos.");
        return;
    }

    // Enviando a solicitação para adicionar o plantão
    fetch('add2.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `medicoId=${encodeURIComponent(medicoId)}&medicoNome=${encodeURIComponent(medicoNome)}&especialidade=${encodeURIComponent(especialidade)}&horaInicio=${encodeURIComponent(horarioInicio)}&horaSaida=${encodeURIComponent(horarioSaida)}&data=${encodeURIComponent(data)}`
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Plantão adicionado com sucesso!');
            closeForm();
            location.reload(); // Atualiza a página para exibir os dados novos
        } else {
            alert('Erro: ' + (result.error || 'Erro ao adicionar plantão'));
        }
    })
    .catch(error => {
        console.error('Erro na solicitação:', error);
        alert('Erro ao processar a solicitação.');
    });
}

    </script>
</section>

</body>
</html>