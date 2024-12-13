<?php

session_start(); 
include "db_conn.php";

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id']; 

    // Consultar todas as especialidades
    $sql_especialidades = "SELECT id, Nome FROM especialidades";
    $stmt_especialidades = $conn->prepare($sql_especialidades);
    $stmt_especialidades->execute();
    $especialidades = $stmt_especialidades->fetchAll(PDO::FETCH_ASSOC);

    // Contar o número de usuários
    $sql_count_medic = "SELECT COUNT(id) AS total_medicos FROM medicos WHERE id_hospital = ?";
    $stmt_count = $conn->prepare($sql_count_medic);
    $stmt_count->execute([$id]);
    $total_medic = $stmt_count->fetchColumn();

    // Definir a coluna a ser ordenada e a ordem
    $allowed_columns = ['Nome', 'Especialidade', 'Crm', 'ID'];
    $sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_columns) ? $_GET['sort'] : 'Nome';
    
    $allowed_orders = ['ASC', 'DESC'];
    $sort_order = isset($_GET['order']) && in_array($_GET['order'], $allowed_orders) ? $_GET['order'] : 'ASC';

    // Recuperar todos os médicos do hospital logado
    $sql_all_medic = "SELECT id, Nome, Especialidade, Crm FROM medicos WHERE id_hospital = ?";
    $params = [$id];

    if (!empty($_GET['Nome'])) {
        $sql_all_medic .= " AND Nome LIKE ?";
        $params[] = '%' . $_GET['Nome'] . '%';
    }

    if (!empty($_GET['Especialidade'])) {
        $sql_all_medic .= " AND Especialidade LIKE ?";
        $params[] = '%' . $_GET['Especialidade'] . '%';
    }

    if (!empty($_GET['Crm'])) {
        $sql_all_medic .= " AND Crm LIKE ?";
        $params[] = '%' . $_GET['Crm'] . '%';
    }

    // Adicionar ordenação segura
    $sql_all_medic .= " ORDER BY $sort_column $sort_order";

    // Prepara e executa a consulta
    $stmt_all_medic = $conn->prepare($sql_all_medic);
    $stmt_all_medic->execute($params);
    $medicos = $stmt_all_medic->fetchAll(PDO::FETCH_ASSOC);

    // Verificar se o formulário de exclusão foi enviado
    if (isset($_POST['delete'])) {
        $userId = $_POST['id'];
        $sql_delete_medic = "DELETE FROM medicos WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete_medic);
        try {
            $stmt_delete->execute([$userId]);
            echo "<script>alert('Médico excluído com sucesso!');</script>";
            header("Refresh:0");
            exit();
        } catch (PDOException $e) {
            echo "Erro ao excluir o medico: " . $e->getMessage();
        }
    }

    // Lógica de sessão para usuário logado
    if (isset($_SESSION['fname']) && isset($_SESSION['pp'])) {
        $fname = htmlspecialchars($_SESSION['fname']); 
        $pp = htmlspecialchars($_SESSION['pp']); 
        $pp_path = 'upload/' . $pp;
        if (!file_exists($pp_path)) {
            $pp_path = 'upload/default-pp.png'; 
        }
    } else {
        $sql = "SELECT Nome, pp FROM hospitais WHERE id = ?";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                $fname = htmlspecialchars($user['Nome']);
                $pp = htmlspecialchars($user['pp']);
                $_SESSION['Nome'] = $fname;
                $_SESSION['pp'] = $pp;
                $pp_path = 'upload/' . $pp;
                if (!file_exists($pp_path)) {
                    $pp_path = 'upload/default-pp.png';
                }
            } else {
                header("Location: ../indexdash.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Erro ao buscar dados do usuário: " . $e->getMessage();
            exit();
        }
    }
}



  // Verificar se o formulário de exclusão foi enviado
  if (isset($_POST['delete'])) {
      $userId = $_POST['id'];

      // Preparar e executar a consulta para excluir o usuário
      $sql_delete_medic = "DELETE FROM medicos WHERE id = ?";
      $stmt_delete = $conn->prepare($sql_delete_medic);
      
      try {
          $stmt_delete->execute([$userId]);
          echo "<script>alert('Médico excluído com sucesso!');</script>";
          header("Refresh:0"); 
          exit();
      } catch (PDOException $e) {
          echo "Erro ao excluir o medico: " . $e->getMessage();
      }
  }

  
// Processar edição
if (isset($_POST['edit'])) {
  $medico_id = $_POST['id'];
  $sql_edit = "SELECT * FROM medicos WHERE id = ?";
  $stmt_edit = $conn->prepare($sql_edit);
  $stmt_edit->execute([$medico_id]);
  $medico_data = $stmt_edit->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['update'])) {
  $medico_id = $_POST['id'];
  $medico_n = $_POST['Nome'];
  $especialidade = $_POST['Especialidade'];
  $crm = $_POST['Crm'];


  $sql_update = "UPDATE medicos SET Nome = ?, Especialidade = ?, Crm = ? WHERE id = ?";
  $stmt_update = $conn->prepare($sql_update);
  $stmt_update->execute([$medico_n, $especialidade, $crm, $medico_id]);
  header("Location: medico.php");
  exit();
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
			<li><a href="medico.php" class="active" style="background-color: #628A4C;"><i class='' ><img src="./img/medb.png" width="20px" alt=""></i>MÉDICO </a></li>
			<li><a href="plantão.php"><i class='' ><img src="./img/plan.png" width="20px" alt=""></i>PLANTÃO </a></li>
		</ul>
	</section>
  <section id="content">
<header>
        <nav class="navbar">
            <div class="logo">
                <span>Médicos</span>
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
        <form id="addProductForm" onsubmit="event.preventDefault(); addProduct();">
            <label for="Nome">Nome:</label>
            <input type="text" id="Nome" name="Nome" required>
            
            <label for="Especialidade">Especialidade:</label>
            <select id="Especialidade" name="Especialidade" required>
                <option value="" disabled selected>Selecione uma especialidade</option>
                <?php foreach ($especialidades as $especialidade): ?>
                    <option value="<?php echo htmlspecialchars($especialidade['Nome']); ?>">
                        <?php echo htmlspecialchars($especialidade['Nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="Crm">CRM:</label>
            <input type="text" id="Crm" name="Crm">
            
            <button type="button" class="salvar" onclick="addProduct()">Salvar</button>
        </form>
    </div>
</div>
<main>
			<!--<h1 class="title">Dashboard</h1>-->
			<ul class="breadcrumbs">
				<li><a href="indexhosp.php" style="color: #000;">Home</a></li>
				<li class="divider">/</li>
				<li><a href="#" class="active">médicos</a></li>
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
                    <form method="GET" action="">
                        <label for="Nome">Nome:</label>
                        <input type="text" name="Nome" id="Nome" value="<?php echo isset($_GET['Nome']) ? htmlspecialchars($_GET['Nome']) : ''; ?>">

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


                        <label for="Crm">CRM:</label>
                        <input type="text" name="Crm" id="Crm" value="<?php echo isset($_GET['Crm']) ? htmlspecialchars($_GET['Crm']) : ''; ?>">

                        
                        
                        <button type="submit" class="verde">Filtrar</button>
                        <button type="button" class="vermelho" onclick="window.location.href = 'medico.php';">Limpar Filtros</button>
                    </form>
                </div>
            </div>

          
		
		<!-- MAIN -->

    <div class="user-list">
        
                
                <table>
                    <thead>
                        <tr>
                            <th><a class="white"  href="?sort=ID&order=<?php echo ($sort_column == 'ID' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">ID</a></th>
                            <th><a class="white"  href="?sort=Nome&order=<?php echo ($sort_column == 'Nome' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Nome</a></th>
                            <th><a class="white"  href="?sort=Especialidade&order=<?php echo ($sort_column == 'Especialidade' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Especialidade</a></th>
                            <th><a class="white"  href="?sort=Crm&order=<?php echo ($sort_column == 'Crm' && $sort_order == 'ASC') ? 'DESC' : 'ASC'; ?>">Crm</a></th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicos as $medico) : ?>
                            <tr>
                                <td><?php echo $medico['id']; ?></td>
                                <td><?php echo $medico['Nome']; ?></td>
                                <td><?php echo $medico['Especialidade']; ?></td>
                                <td><?php echo $medico['Crm']; ?></td>
                                <td>
                                <button class="verde" onclick="toggleEditForm(<?php echo $medico['id']; ?>)">Editar</button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($medico['id']); ?>">
                                    <button type="submit" class="vermelho" name="delete" onclick="return confirm('Tem certeza que deseja excluir este médico?');">Excluir</button>
                                </form>
                            </td>
                            </tr>
                            <tr id="edit-form-<?php echo $medico['id']; ?>" class="edit-form">
                                <td colspan="9">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($medico['id']); ?>">
                                        <input type="text" name="Nome" value="<?php echo htmlspecialchars($medico['Nome']); ?>" >
                                        <label for="Especialidade">Especialidade:</label>
                                    <select name="Especialidade" required>
                                        <option value="" disabled>Selecione uma especialidade</option>
                                        <?php foreach ($especialidades as $especialidade): ?>
                                            <option value="<?php echo htmlspecialchars($especialidade['Nome']); ?>"
                                                <?php echo ($medico['Especialidade'] == $especialidade['Nome']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($especialidade['Nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                        <input type="text" name="Crm" value="<?php echo htmlspecialchars($medico['Crm']); ?>" >
        
                                        <button type="submit" class="verde" name="update">Salvar</button>
                                        <button type="button" class="vermelho" onclick="toggleEditForm(<?php echo $medico['id']; ?>)">Cancelar</button>
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
            const form = document.getElementById('edit-form-' + id);
            form.style.display = form.style.display === 'table-row' ? 'none' : 'table-row';
        }

        function openForm() {
    document.getElementById('addProductModal').style.display = 'flex';
}


function closeForm() {
    document.getElementById('addProductModal').style.display = 'none';
}

function addProduct() {
    // Obter os dados do formulário
    const nome = document.getElementById('Nome').value;
    const especialidade = document.getElementById('Especialidade').value;
    const crm = document.getElementById('Crm').value;

    // Enviar os dados via fetch
    fetch('add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `Nome=${encodeURIComponent(nome)}&Especialidade=${encodeURIComponent(especialidade)}&Crm=${encodeURIComponent(crm)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Médico adicionado com sucesso!');
            closeForm(); // Fecha o modal
            // Recarrega a página
            location.reload();
        } else {
            alert('Erro: ' + (data.error || 'Erro ao adicionar médico'));
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