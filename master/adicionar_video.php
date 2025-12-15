<?php
// Inclua o arquivo de conexão do banco de dados
require_once __DIR__ . '/../db/Conexao.php';

if (isset($_POST['link']) && isset($_POST['title'])) {
    $stmt = $connect->prepare("INSERT INTO videos (title, link) VALUES (?, ?)");
    $stmt->execute([$_POST['title'], $_POST['link']]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Vídeo adicionado com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falha ao adicionar o vídeo.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Título e/ou link do vídeo não fornecidos.']);
}
?>