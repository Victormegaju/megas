<?php
ob_start();
session_start();

if ((!isset($_SESSION['cod_id']) == true)) {
  unset($_SESSION['cod_id']);
  header('location: ../');
}

$cod_id = $_SESSION['cod_id'];

$bytes = random_bytes(16);
$idempotency = bin2hex($bytes);

require_once __DIR__ . '/../db/Conexao.php';

//CELULAR ADMINISTRADOR
$celphoneAdm = $connect->query("SELECT contato FROM carteira WHERE tipo = 1");
$celphoneAdmRow = $celphoneAdm->fetch(PDO::FETCH_OBJ);

// DADOS GERAIS
$pegadadosgerais = $connect->query("SELECT * FROM carteira WHERE Id = '$cod_id'");
$dadosgerais = $pegadadosgerais->fetch(PDO::FETCH_OBJ);

$actualDate = date("Y-m-d");

$date = $dadosgerais->assinatura;
$dateParts = explode("/", $date);
$subscriptionDate = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];

if ($actualDate > $subscriptionDate && $dadosgerais->tipo > 1 && $dadosgerais->tipo != 2) {
  if (isset($_COOKIE['pdvx'])) {
    unset($_COOKIE['pdvx']);

    setcookie('pdvx', null, -1, '/');
  }

  session_destroy();

  session_write_close();

  header('location: ../index.php');

}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta name="description" content="Painel Administrativo.">

  <meta name="author" content="Paulo Carvalho">

  <title><?php echo $_nomesistema ?></title>

  <link rel="icon" href="<?php echo $url_base; ?>/master/icon/favicon.ico.php">

  <link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet">

  <link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">

  <link href="../lib/datatables/css/jquery.dataTables.css" rel="stylesheet">

  <link href="../lib/select2/css/select2.min.css" rel="stylesheet">

  <link href="../styles/planos.css" rel="stylesheet">

  <link href="../styles/msg-assinatura.css" rel="stylesheet">

  <link rel="stylesheet" href="../css/slim.css">
  <link rel="stylesheet" href="../styles/dark-mode.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


</head>

<body class="<?php echo $darkMode ? 'dark-mode' : ''; ?>">
  <div id="loader"
    style="position: fixed; width: 100%; height: 100%; background: white; display: flex; align-items: center; justify-content: center; z-index: 9999;">
    <div class="col-3" style="display: flex; align-items: center; justify-content: center;">
      <div class="snippet" data-title="loader">
        <div class="stage">
          <div class="loader"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="slim-header with-sidebar">
    <div class="container-fluid">
      <div class="slim-header-left">
        <a class="slim-logo" href="/.">
          <!-- <img src="img/logo.png" alt="Logo Financeiro"> -->
          <img src="<?php echo $url_base; ?>/master/img/logo.png" alt="Logo Financeiro">
        </a>

        <a href="javascript: void(0);" id="slimSidebarMenu">
          <img src="<?php echo $url_base; ?>/master/icon/registradora.png" style="width: 24px; height: 24px;">
        </a>
      </div>

      <?php
      $hoje = date('Y-m-d');
      $date = $dadosgerais->assinatura;
      $dateParts = explode("/", $date);
      $dataExpiracao = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
      $paginaAtual = $_SERVER['REQUEST_URI'];

      if ($dadosgerais->tipo > 1) { ?>
        <div id="msg-assinatura" class="slim-header-center">
          <div class="time">Seu plano expira em: <span class="expiration-date"><?php echo date('d/m/Y', strtotime($dataExpiracao)); ?></span></div>
          <a href="/master/planos" target="_parent" class="button">Renovar assinatura</a>
        </div>

        <div id="expiry-modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false"
          style="display: none;">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header" style="background-color: #f5f5f5; color: #333; border-bottom: 1px solid #dee2e6;">
                <h5 class="modal-title">Plano Expirado</h5>
              </div>
              <div class="modal-body" style="background-color: #fff; color: #333;">
                <p>Seu plano expirou. Por favor, renove para continuar usando nossos serviços.</p>
              </div>
              <div class="modal-footer" style="background-color: #f5f5f5; border-top: 1px solid #dee2e6;">
                <a href="/master/planos" target="_parent" class="button"
                  style="background-color: #007bff; color: #fff; border-radius: 5px; padding: 10px 20px;">Renovar
                  assinatura</a>
              </div>
            </div>
          </div>
        </div>

        <?php

// var_dump($hoje);
// var_dump($dataExpiracao);
        if ($hoje >= $dataExpiracao) {

          
          if ($paginaAtual != '/master/planos' && strpos($paginaAtual, '/sair') === false) {
            header("Location: /master/planos");
            exit;
          } else if ($paginaAtual == '/master/planos') {
            ?>
            <script>
              $(document).ready(function () {
                $("#expiry-modal").modal("show");
              });
            </script>
            <?php
          }
        }
        ?>
      <?php } ?>
      <div class="slim-header-right">
        <!-- Adicionado interruptor para o modo noturno -->
        <?php
        // Supondo que você tenha uma variável $isDarkMode que é verdadeira se o modo escuro estiver ativado
        $icon = $isDarkMode ? 'luaa.png' : 'sol.png';
        ?>

        <div class="dark-mode-switch">
          <span id="darkModeSwitch" onclick="toggleDarkMode()">
            <img id="darkModeIcon" src="<?php echo $url_base; ?>/master/icon/<?php echo $icon; ?>"
              style="width: 24px; height: 24px;">
            <span id="darkModeText"></span>
          </span>
        </div>


        <div class="dropdown dropdown-c">
          <a href="perfil" class="logged-user" data-toggle="dropdown">


            <?php
            date_default_timezone_set('America/Sao_Paulo');
            $hora = date('H');

            $diaMes = date('d');
            $diaSemana = date('w');
            $mes = date('n') - 1;
            $ano = date('Y');

            $nomesDiasDaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];

            $nomeDosMeses = ['Janeiro', 'Fevereiro', 'março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

            $dataFormatada = $nomesDiasDaSemana[$diaSemana] . ', ' . $diaMes . ' de ' . $nomeDosMeses[$mes] . ' de ' . $ano;

            if ($hora < 12 && $hora >= 6)
              $saudacao = "Bom Dia";

            if ($hora >= 12 && $hora < 18)
              $saudacao = "Boa Tarde";

            if ($hora >= 18 && $hora <= 23)
              $saudacao = "Boa Noite";
            if ($hora < 6 && $hora >= 0)
              $saudacao = "Boa madrugada";
            ?>

            <div>
              <?php echo $saudacao . " " . $dadosgerais->nome; ?>
            </div>

          </a>
        </div>
      </div>
    </div>
  </div>

  <script>

  

    function toggleDarkMode() {
      var body = document.body;
      var icon = document.getElementById('darkModeIcon');
      var text = document.getElementById('darkModeText');

      // Adiciona ou remove a classe 'dark-mode' do body
      body.classList.toggle('dark-mode');

      // Muda o ícone e o texto de acordo com o modo
      if (body.classList.contains('dark-mode')) {
        icon.src = '<?php echo $url_base; ?>/master/icon/luaa.png';
        text.textContent = '';
        localStorage.setItem('mode', 'dark'); // Salva o modo escuro no localStorage
      } else {
        icon.src = '<?php echo $url_base; ?>/master/icon/sol.png';
        text.textContent = '';
        localStorage.setItem('mode', 'light'); // Salva o modo claro no localStorage
      }
    }

    window.onload = function () {
      var body = document.body;
      var icon = document.getElementById('darkModeIcon');
      var mode = localStorage.getItem('mode'); // Obtém o modo do localStorage

      // Define o modo inicial e o ícone com base no valor salvo no localStorage
      if (mode === 'dark') {
        body.classList.add('dark-mode');
        icon.src = '<?php echo $url_base; ?>/master/icon/luaa.png';
      } else {
        body.classList.remove('dark-mode');
        icon.src = '<?php echo $url_base; ?>/master/icon/sol.png';
      }

      // Oculta a tela de carregamento após 3 segundos
      setTimeout(function () {
        document.getElementById('loader').style.display = 'none';
      }, 1000);
    }

  </script>
</body>

</html>