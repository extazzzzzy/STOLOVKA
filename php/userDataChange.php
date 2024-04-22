<?php
    session_start();

    $name = $_POST['edit_f_name'];
    $address = $_POST['edit_add'];
    $id = $_SESSION['id'];

    $connectMySQL = new mysqli('localhost', 'root', 'root', 'stolovka');

    $sql_query = "UPDATE users SET first_name = ?, address = ? WHERE id=?";
    $query = $connectMySQL -> prepare($sql_query);
    $query -> bind_param("ssi", $name, $address, $id);
    $query->execute();
    
    header("Location: ../Pages/profile.php");
    die();
?>