<?php
// Inclua o arquivo de conexão do banco de dados
require_once __DIR__ . '/../db/Conexao.php';

$cod_id = $_SESSION['cod_id'];
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
<style>
    .toast-success {
        background-color: blue !important;
    }
</style>
    <div class="container mt-5">
        <?php if ($dadosgerais->tipo == 1): ?>
            <div class="card video-card">
                <div class="card-header">
                    Adicionar Vídeo
                </div>
                <div class="card-body">
                    <form id="videoForm" method="POST">
                        <div class="form-group">
                            <label for="title">Título do Vídeo</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Insira o título do vídeo">
                        </div>
                        <div class="form-group">
                            <label for="link">Código do Vídeo</label>
                            <input type="text" name="link" id="link" class="form-control" placeholder="Insira o código do vídeo aqui. Por exemplo, para o vídeo 'https://youtu.be/i6gEcyFmJDc?si=sYI499sHHK5tFKCZ', o código seria 'i6gEcyFmJDc?si=sYI499sHHK5tFKCZ'">
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar Vídeo</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>


        
        <?php if ($dadosgerais->tipo == 2 || $dadosgerais->tipo == 1): ?>
            <?php $stmt = $connect->query('SELECT * FROM videos'); ?>
            <div class="row mt-5">
                <?php while ($row = $stmt->fetch()): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card video-card">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['title'] ?></h5>
                                <iframe width="100%" height="200" src="https://www.youtube.com/embed/<?= $row['link'] ?>" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                                <?php if ($dadosgerais->tipo == 1): ?>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="edit_video.php?id=<?= $row['id'] ?>" class="btn btn-primary mt-2">Editar Vídeo</a>
                                        <a href="#" class="btn btn-danger mt-2 ml-2" onclick="confirmDelete('<?= $row['id'] ?>')">Excluir Vídeo</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
   
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
    <script src="../lib/popper.js/js/popper.js"></script>
    <script src="../lib/bootstrap/js/bootstrap.js"></script>
    <script src="../lib/select2/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
    <script src="../js/slim.js"></script>
    
    
    <script>
        
        $(document).ready(function(){
            $(document).ready(function(){
        // Verifique se há uma mensagem na URL
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');

        if (message) {
            toastr.success(message);
        }

            $('#videoForm').on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: '/master/adicionar_video.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response){
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            });
        });
    });

    function confirmDelete(id) {
        swal({
            title: "Tem certeza?",
            text: "Uma vez excluído, você não poderá recuperar este vídeo!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                window.location.href = 'delete_video.php?id=' + id;
            }
        });
    }
    </script>
</body>

</html>