<?php 

if(isset($_POST['Nome']) && 
   isset($_POST['uname']) &&  
   isset($_POST['pass'])){

    include "../db_conn.php";

    $Nome = $_POST['Nome'];
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];

    $data = "Nome=".$Nome."&uname=".$uname;
    
    if (empty($Nome)) {
    	$em = "Nome não preenchido";
    	header("Location: ../index.php?error=$em&$data");
	    exit;
    }else if(empty($uname)){
    	$em = "Nome de usuário não preenchido";
    	header("Location: ../index.php?error=$em&$data");
	    exit;
    }else if(empty($pass)){
    	$em = "Senha não preenchida";
    	header("Location: ../index.php?error=$em&$data");
	    exit;
    }else {
      // hashing the password
      $pass = password_hash($pass, PASSWORD_DEFAULT);

      if (isset($_FILES['pp']['name']) AND !empty($_FILES['pp']['name'])) {
         
         
         $img_name = $_FILES['pp']['name'];
         $tmp_name = $_FILES['pp']['tmp_name'];
         $error = $_FILES['pp']['error'];
         
         if($error === 0){
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_to_lc = strtolower($img_ex);

            $allowed_exs = array('jpg', 'jpeg', 'png');
            if(in_array($img_ex_to_lc, $allowed_exs)){
               $new_img_name = uniqid($uname, true).'.'.$img_ex_to_lc;
               $img_upload_path = '../upload/'.$new_img_name;
               move_uploaded_file($tmp_name, $img_upload_path);

               // Insert into Database
               $sql = "INSERT INTO hospitais(Nome, Usuario, Senha, pp) 
                 VALUES(?,?,?,?)";
               $stmt = $conn->prepare($sql);
               $stmt->execute([$fname, $uname, $pass, $new_img_name]);

               header("Location: ../index.php?success=Conta criada com sucesso");
                exit;
            }else {
               $em = "Arquivo em formato inválido";
               header("Location: ../index.php?error=$em&$data");
               exit;
            }
         }else {
            $em = "Ocorreu um erro desconhecido";
            header("Location: ../index.php?error=$em&$data");
            exit;
         }

        
      }else {
       	$sql = "INSERT INTO hospitais(Nome, Usuario, Senha, pp) 
       	        VALUES(?,?,?)";
       	$stmt = $conn->prepare($sql);
       	$stmt->execute([$Nome, $uname, $pass]);

       	header("Location: ../index.php?success=Conta criada com sucesso");
   	    exit;
      }
    }


}else {
	header("Location: ../index.php?error=error");
	exit;
}
