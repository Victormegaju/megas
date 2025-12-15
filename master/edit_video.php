<?php
require_once __DIR__ . '/../db/Conexao.php';

if (!isset($_GET['id'])) {
    die('Nenhum ID de vídeo fornecido');
}
$stmt = $connect->prepare('SELECT * FROM videos WHERE id = ?');
$stmt->execute([$_GET['id']]);
$video = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $connect->prepare('UPDATE videos SET title = ?, link = ? WHERE id = ?');
    $stmt->execute([$_POST['title'], $_POST['link'], $_GET['id']]);
    header('Location: /master/tutoriais?message=Video+Editado+com+sucesso');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Editar Vídeo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        Editar Vídeo
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="title">Título do Vídeo</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="<?= htmlspecialchars($video['title']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="link">Código do Vídeo</label>
                                <input type="text" name="link" id="link" class="form-control"
                                    value="<?= htmlspecialchars($video['link']) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>