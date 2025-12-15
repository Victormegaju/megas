<?php
require_once __DIR__ . '/../db/Conexao.php';

if (!isset($_GET['id'])) {
    die('Nenhum ID de vídeo fornecido');
}

$stmt = $connect->prepare('DELETE FROM videos WHERE id = ?');
$stmt->execute([$_GET['id']]);

header('Location: /master/tutoriais?message=Video+deletado+com+sucesso');
exit;
?>