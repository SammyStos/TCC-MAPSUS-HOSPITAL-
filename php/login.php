<?php 
session_start();

if(isset($_POST['uname']) && isset($_POST['pass'])) {

    include "../db_conn.php";

    $uname = $_POST['uname'];
    $pass = $_POST['pass'];

    $data = "uname=".$uname;

    if(empty($uname)){
        $em = "User name is required";
        header("Location: ../login.php?error=$em&$data");
        exit;
    } else if(empty($pass)){
        $em = "Password is required";
        header("Location: ../login.php?error=$em&$data");
        exit;
    } else {

        $sql = "SELECT * FROM hospitais WHERE Usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if($stmt->rowCount() == 1){
            $hospitais = $stmt->fetch();

            $username = $hospitais['Usuario'];
            $Senha = $hospitais['Senha'];
            $Nome = $hospitais['Nome'];
            $id = $hospitais['id'];
            $pp = $hospitais['pp'];

            if($username === $uname) {
                if(password_verify($pass, $Senha)) {
                    $_SESSION['id'] = $id;
                    $_SESSION['Nome'] = $Nome;
                    $_SESSION['pp'] = $pp;

                    header("Location: ../indexdash.php");
                    exit;
                } else {
                    $em = "Nome de usuário ou senha incorretos";
                    header("Location: ../login.php?error=$em&$data");
                    exit;
                }

            } else {
                $em = "Nome de usuário ou senha incorretos";
                header("Location: ../login.php?error=$em&$data");
                exit;
            }

        } else {
            $em = "Nome de usuário ou senha incorretos";
            header("Location: ../login.php?error=$em&$data");
            exit;
        }
    }
} else {
    header("Location: ../login.php?error=error");
    exit;
}
?>