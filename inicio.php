<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MapSus - Administração</title>
    <link rel="stylesheet" href="css/estiloini.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

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

.navbar-text {
    color: #495057;
    font-size: 1.7rem;
	font-weight: bold;
}

.navbar-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}

.navbar-menu li {
    margin-left: 20px;
}

.navbar-menu a {
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
    transition: color 0.3s;
}

.navbar-menu a:hover {
    color: #f0a500;
}
        </style>
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="img/logoverde.png" alt="MapSus Logo">
                <span>MAPSUS</span>
            </div>
            <div class="nav-buttons">
                <button class="btn">Baixe agora</button>
              <a href='cadastrar.php'> <button class="btn">Entrar</button></a>
            </div>
        </nav>
    </header>

    <section class="main-section">
        <div class="text-section">
            <h1>Bem-vindo à Administração do MapSus</h1>
            <p><a href="#">MapSus</a> é o novo app de localização com hospitais mais próximos de você, com a especialidade que você necessita.</p>
            <div class="cta-buttons">
               <a href= 'cadastrar.php'> <button class="cta-btn cadastre">Cadastre-se</button></a>
                <a href= 'cadastrar.php'><button class="cta-btn entrar">Entrar</button></a>
                
</a>

            </div>
        </div>
        <div class="image-section">
            <img src="img/med3.png" alt="imagem de inicio">
        </div>
    </section>


    <script src="js/1script.js"></script>
</body>
</html>
