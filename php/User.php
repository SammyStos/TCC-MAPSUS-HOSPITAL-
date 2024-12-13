<?php 

function getUserById($id, $db){
    $sql = "SELECT * FROM hospitais WHERE id = ?";
	$stmt = $db->prepare($sql);
	$stmt->execute([$id]);
    
    if($stmt->rowCount() == 1){
        $hospitais = $stmt->fetch();
        return $hospitais;
    }else {
        return 0;
    }
}

 ?>