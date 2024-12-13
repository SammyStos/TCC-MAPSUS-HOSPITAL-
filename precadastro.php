<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
session_start();
require "conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Depuração para verificar os dados recebidos
    var_dump($_POST); 

    // Pegando todos os dados recebidos do formulário
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);  // Senha com hash
    $endereco = $_POST['endereco'];
    $cnpj = $_POST['cnpj'];
    $telefone = $_POST['telefone'];
    $cep = $_POST['cep'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $zona = $_POST['zona'];
    $hospital = $_POST['hospital'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
   

    // Atualizar todos os campos no banco de dados
    $sql = "UPDATE hospitais SET 
                Email = ?, 
                Senha = ?, 
                Endereco = ?, 
                Cnpj = ?, 
                Telefone = ?, 
                Cep = ?, 
                Cidade = ?, 
                Zona = ?, 
                Bairro = ?,
                Nome = ?, 
                Latitude = ?, 
                Longitude = ? 
            WHERE Usuario = ?";
    
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Erro ao preparar a query: " . $conn->error;
        exit;
    }

    // Passando todos os dados para a query
    $stmt->bind_param('sssssssssssss', $email, $senha, $endereco, $cnpj, $telefone, $cep, $cidade, $zona, $bairro, $hospital, $latitude, $longitude, $usuario);

    if ($stmt->execute() === false) {
        echo "Erro ao executar a query: " . $stmt->error;
    } else {
        echo "Cadastro atualizado com sucesso!";
    }

    $_SESSION['precadastro'] = false;
    header("Location: cadastrar.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-Cadastro</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body { display: flex; justify-content: center; align-items: center; background-color: #fff;  padding-top: 10px; font-family: Arial, sans-serif; height: 100vh; margin: 0; }
        .card { background-color: #555; color: #628A4C; padding: 20px; border-radius: 8px; width: 400px; text-align: center; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15); }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { color: #fff; }
        .form-group input { width: 95%; display: flex; justify-content: center; padding: 8px; border: 1px solid #628A4C; border-radius: 4px; }
        .btn-submit, .btn-next { background-color: #628A4C; color: #FFF; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-weight: bold; }
        .btn-submit:hover, .btn-next:hover { background-color: #556a3e; }
        .btn-section {
          background: none;
          border: none;
          color: #FFF;
          cursor: pointer;
         font-weight: bold;
          margin: 0 5px;
          padding: 5px;
          transition: color 0.3s, background-color 0.3s;
        }

        #map { height: 300px; margin-bottom: 15px; }
        .section { display: none; }
        .section.active { display: block; }

         /* Indicador de progresso */
        .progress-indicator button {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 50%;
            padding: 10px 15px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s, color 0.3s;
        }

        .progress-indicator button.active {
            background-color: #4caf50; /* Verde para botão ativo */
            color: #fff;
            border-color: #4caf50;
        }

    </style>
</head>
<body>

<div class="card">
    <h2>Complete seu Cadastro</h2>

        
    <div class="progress-indicator">
    <button id="indicator-1" class="active" onclick="showSection(0)">1</button>
    <button id="indicator-2" onclick="showSection(1)">2</button>
    <button id="indicator-3" onclick="showSection(2)">3</button>
</div>


    
    <form id="form-cadastro" method="POST">
        <div id="section-usuario" class="section active">
            <div class="form-group">
                <br>
                <label for="usuario">Nome de Usuário</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="button" class="btn-next" onclick="nextSection()">Próximo</button>
        </div>

        <div id="section-hospital" class="section">
            <div class="form-group">
                <br>
                <label for="hospital">Nome do Hospital</label>
                <input type="text" id="hospital" name="hospital" placeholder="Digite o nome do hospital" required>
            </div>
            <div class="form-group">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco" required>
            </div>
            <div class="form-group">
                <label for="bairro">Bairro</label>
                <input type="text" id="bairro" name="bairro" required>
            </div>
            <div class="form-group">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" required>
            </div>
            <div class="form-group">
                <label for="zona">Região</label>
                <input type="text" id="zona" name="zona" required>
            </div>
            <div class="form-group">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" required>
            </div>
            <div class="form-group">
                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" required>
            </div>
            <button type="button" class="btn-next" onclick="nextSection()">Próximo</button>
        </div>

        <div id="section-localizacao" class="section">
            <div class="form-group">
                <br>
                <input type="text" id="nome_hospital" name="nome_hospital" readonly>
            </div>
            <div class="form-group">
                <label for="map">Localização do Hospital</label>
                <div id="map"></div>
            </div>
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="text" id="latitude" name="latitude" readonly>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="text" id="longitude" name="longitude" readonly>
            </div>
            <button type="submit" class="btn-submit">Concluir Cadastro</button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Inicializa o mapa
    var map = L.map('map').setView([-23.550520, -46.633308], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([-23.550520, -46.633308]).addTo(map);
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });

    // Preenchimento automático do nome e atualização do mapa
    document.getElementById('hospital').addEventListener('input', function() {
        var hospitalName = this.value;
        document.getElementById('nome_hospital').value = hospitalName;

        if (hospitalName.length > 2) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${hospitalName}&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);

                        // Atualiza marcador e mapa
                        marker.setLatLng([lat, lon]);
                        map.setView([lat, lon], 15);
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lon;
                    }
                });
        }
    });

    const sections = document.querySelectorAll('.section');
    const indicators = document.querySelectorAll('.progress-indicator button');
    let currentSectionIndex = 0;

    function showSection(indexOrId) {
        // Determina se o argumento é índice ou ID
        let sectionIndex;
        if (typeof indexOrId === 'number') {
            sectionIndex = indexOrId;
        } else {
            sectionIndex = Array.from(sections).findIndex(section => section.id === indexOrId);
        }

        // Atualiza seções
        sections.forEach((section, idx) => {
            section.classList.toggle('active', idx === sectionIndex);
        });

        // Atualiza barra de progresso
        indicators.forEach((indicator, idx) => {
            indicator.classList.toggle('active', idx === sectionIndex);
        });

        // Atualiza índice atual
        currentSectionIndex = sectionIndex;

        // Corrige exibição do mapa na seção de localização
        if (sections[sectionIndex].id === 'section-localizacao') {
            setTimeout(() => {
                map.invalidateSize();
            }, 200);
        }
    }

    // Navegação entre seções
    function nextSection() {
        if (currentSectionIndex < sections.length - 1) {
            showSection(currentSectionIndex + 1);
        }
    }

    function previousSection() {
        if (currentSectionIndex > 0) {
            showSection(currentSectionIndex - 1);
        }
    }

    // Atualizar barra de progresso ao carregar a página
    document.addEventListener('DOMContentLoaded', () => {
        showSection(0);
    });
</script>


</body>
</html>
