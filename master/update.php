<?php
require_once __DIR__ . '/../db/Conexao.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="/master/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="/master/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/master/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="/master/plugins/fontawesome-pro/css/all.min.css">
    <link rel="stylesheet" href="/master/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="/master/dist/css/adminlte.min.css">
</head>

<body>

    <div class="slim-mainpanel">
        <div class="container">
            <div class="page-content">
                <section class="content">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="timeline">
                                    <?php
                                    $changelog = json_decode(file_get_contents('changelog.json'), true);
                                    foreach ($changelog as $version => $info) { ?>
                                        <div class="time-label">
                                            <span class="bg-info">
                                                <?php echo $version ?>
                                            </span>
                                        </div>
                                        <?php if (count($info['feat']) > 0) { ?>
                                            <div>
                                                <i class="far fa-sparkles bg-success"></i>
                                                <div class="timeline-item">
                                                    <h3 class="timeline-header">Novidades</h3>
                                                    <span class="time"><i class="fas fa-clock"></i>
                                                        <?php echo $info['date'] ?>
                                                    </span>
                                                    <div class="timeline-body">
                                                        <ul>
                                                            <?php foreach ($info['feat'] as $novidade) { ?>
                                                                <li>
                                                                    <?php echo $novidade ?>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        if (count($info['fix']) > 0) { ?>
                                            <div>
                                                <i class="far fa-bug bg-warning"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fas fa-clock"></i>
                                                        <?php echo $info['date'] ?>
                                                    </span>
                                                    <h3 class="timeline-header">Correção de Bugs</h3>
                                                    <div class="timeline-body">
                                                        <ul>
                                                            <?php foreach ($info['fix'] as $fix) { ?>
                                                                <li>
                                                                    <?php echo $fix ?>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        if (count($info['style']) > 0) { ?>
                                            <div>
                                                <i class="fal fa-paint-brush-alt bg-purple"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fas fa-clock"></i>
                                                        <?php echo $info['date'] ?>
                                                    </span>
                                                    <h3 class="timeline-header">Estilizaão</h3>
                                                    <div class="timeline-body">
                                                        <ul>
                                                            <?php foreach ($info['style'] as $style) { ?>
                                                                <li>
                                                                    <?php echo $style ?>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        if (count($info['refactor']) > 0) { ?>
                                            <div>
                                                <i class="fal fa-tools bg-secondary"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fas fa-clock"></i>
                                                        <?php echo $info['date'] ?>
                                                    </span>
                                                    <h3 class="timeline-header">Melhorias</h3>
                                                    <div class="timeline-body">
                                                        <ul>
                                                            <?php foreach ($info['refactor'] as $refactor) { ?>
                                                                <li>
                                                                    <?php echo $refactor ?>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        if (count($info['chore']) > 0) { ?>
                                            <div>
                                                <i class="fal fa-info bg-cyan"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fas fa-clock"></i>
                                                        <?php echo $info['date'] ?>
                                                    </span>
                                                    <h3 class="timeline-header">Outras Informações</h3>
                                                    <div class="timeline-body">
                                                        <ul>
                                                            <?php foreach ($info['chore'] as $chore) { ?>
                                                                <li>
                                                                    <?php echo $chore ?>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
        <script src="../lib/popper.js/js/popper.js"></script>
        <script src="../lib/bootstrap/js/bootstrap.js"></script>
        <script src="../lib/select2/js/select2.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
        <script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="../js/slim.js"></script>
        <script src="/master/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/master/assets/libs/metismenujs/metismenujs.min.js"></script>
        <script src="/master/assets/libs/simplebar/simplebar.min.js"></script>
        <script src="/master/assets/libs/feather-icons/feather.min.js"></script>
        <script src="/master/plugins/jquery/jquery.min.js"></script>
        <script src="/master/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/master/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="/master/dist/js/adminlte.min.js?"></script>

        
</body>

</html>