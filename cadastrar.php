
    <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedir acesso</title>
    <link rel="stylesheet" href="css/estiloini.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
    integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>
<style>
    .botao {
  border-radius: 10px;
  width: 420px;
  height: 45px;
  cursor: pointer;
  border: 0;
  background-color: #628A4C;
  box-shadow: #628A4C 0 0 8px;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  font-size: 15px;
  transition: all 0.5s ease;
  color: white;

}

.botao:hover {
  letter-spacing: 3px;
  background-color: #628A4C;
  color: hsl(0, 0%, 100%);
  box-shadow: #628A4C 0px 7px 29px 0px;
}

.botao:active {
  letter-spacing: 3px;
  background-color: #628A4C;
  color: hsl(0, 0%, 100%);
  box-shadow: #628A4C 0px 0px 0px 0px;
  transform: translateY(10px);
  transition: 100ms;
}
#borda{
	
	border-radius: 15px;
}

/* Estilos gerais */
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


    </style>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="img/logoverde.png" alt="MapSus Logo">
                <span>MAPSUS</span>
            </div>
    </header>
    <div class="container">
        <div class="content first-content">
            <div class="first-column">
                <h2 class="title title-primary">Bem vindo de volta!</h2>
                <p class="description description-primary">Para manter-se conectado</p>
                <p class="description description-primary">entre com seus dados</p>
                <button id="signin" class="btn btn-primary">Entrar</button>
            </div>    
            <div class="second-column">
                <h2 class="title title-second">Solicitar acesso</h2>
                    
                <form class="form"  action="processar_cadastro.php"  method="post" id="borda">
                    <label class="label-input" for="">
                        <i class="far fa-user icon-modify"></i>
                        <input placeholder="Nome" type="text" id="nome" name="nome" value="<?php echo (isset($_GET['uname']))?$_GET['uname']:"" ?>" required>
                    </label>
                    
                    <label class="label-input" for="">
                        <i class="far fa-envelope icon-modify"></i>
                        <input placeholder="Email" type="email" id="email" name="email" required>
                    </label>
                    
                    
                    
                    
                    <button class="btn btn-second">Enviar</button>        
                </form>
            </div><!-- second column -->
        </div><!-- first content -->
        <div class="content second-content">
            <div class="first-column">
                <h2 class="title title-primary">Não tem cadastro?</h2>
                <p class="description description-primary">Solicite seu acesso</p>
                <button id="signup" class="btn btn-primary">Solicitar</button>
            </div>
            <div class="second-column">
                <h2 class="title title-second">Entrar</h2>
                
                <form class="form" id="login-form" action="login.php" method="POST">
                
                    <label class="label-input" for="nome">
                        <i class="far fa-envelope icon-modify"></i>
                        <input type="text" id="usuario" name="usuario" placeholder="Nome de Usuário" required>
                    </label>
                
                    <label class="label-input" for="senha">
                        <i class="fas fa-lock icon-modify"></i>
                        <input type="password" id="senha" name="senha" placeholder="Senha" required>
                    </label>
                
                    <a class="password" href="#">Esqueceu sua senha?</a>
                    <button  type="submit" class="btn btn-second">Entrar</button>
                </form>
            </div><!-- second column -->
        </div><!-- second-content -->
    </div>


    <script src="js/app.js"></script>
</body>
</html>
