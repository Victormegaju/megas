
<?php
    require_once __DIR__ . '/../db/Conexao.php';

    $id = $_GET['id'];

    $query = $connect->prepare("SELECT * FROM financeiro2 WHERE Id = :id");
    $query->execute(array(':id' => $id));

    $parcela = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode($parcela);
?>
