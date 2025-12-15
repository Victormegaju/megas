<?php
if (isset($_GET["mes"])) {
  $mmm = $_GET["mes"];
  $aaa = date("Y");
  $ddd = date("d");
  $datam = $mmm . "/" . $aaa;
  $datam2 = $ddd . "/" . $mmm . "/" . $aaa;
  $datam3 = $mmm . "/" . $aaa;
} else {
  $datam = date("m/Y");
  $mmm = date("m");
  $aaa = date("Y");
  $datam2 = date("d/m/Y");
  $datam3 = date("Y-m");
}

// SOMA VALORES A RECEBER
$valoresareceber = $connect->query("SELECT SUM(parcela) AS totalparcela FROM financeiro2 WHERE status='1' AND datapagamento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresareceberx = $valoresareceber->fetch(PDO::FETCH_OBJ);

// SOMA VALORES RECEBIDOS
$valoresrecebidos = $connect->query("SELECT SUM(parcela) AS totalpago FROM financeiro2 WHERE status='2' AND pagoem LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresrecebidoss = $valoresrecebidos->fetch(PDO::FETCH_OBJ);

// SOMA VALORES RECEBIDOS
$valoresrecebidosh = $connect->query("SELECT SUM(parcela) AS totalrh FROM financeiro2 WHERE status='2' AND pagoem = '" . $datam2 . "' AND idm ='" . $cod_id . "'");
$valoresrecebidossh = $valoresrecebidosh->fetch(PDO::FETCH_OBJ);

// EMPRÉSTIMOS ATIVOS
$empativos = $connect->query("SELECT * FROM financeiro1 WHERE status='1' AND idm ='" . $cod_id . "'");
$empativosx = $empativos->rowCount();

// PARCELAS ABERTAS
$parcelasab = $connect->query("SELECT * FROM financeiro2 WHERE status='1' AND datapagamento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$parcelasabx = $parcelasab->rowCount();

// PARCELAS PAGAS
$parcelasap = $connect->query("SELECT * FROM financeiro2 WHERE status='2' AND pagoem LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$parcelasapx = $parcelasap->rowCount();

// CLIENTES
$cadcli = $connect->query("SELECT * FROM clientes WHERE idm ='" . $cod_id . "'");
$cadclix = $cadcli->rowCount();

// SOMA VALORES CONTAS A PAGAR
$valoresapagar = $connect->query("SELECT SUM(valor) AS totalapagar FROM financeiro3 WHERE status='1' AND datavencimento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresapagarx = $valoresapagar->fetch(PDO::FETCH_OBJ);

// SOMA VALORES CONTAS PAGAS
$valorespagos = $connect->query("SELECT SUM(valor) AS totalpago FROM financeiro3 WHERE status='2' AND datapagamento LIKE '%" . $datam3 . "%' AND idm ='" . $cod_id . "'");
$valorespagosx = $valorespagos->fetch(PDO::FETCH_OBJ);

$meses = array(
  '01' => 'Janeiro',
  '02' => 'Fevereiro',
  '03' => 'Março',
  '04' => 'Abril',
  '05' => 'Maio',
  '06' => 'Junho',
  '07' => 'Julho',
  '08' => 'Agosto',
  '09' => 'Setembro',
  '10' => 'Outubro',
  '11' => 'Novembro',
  '12' => 'Dezembro'
);
$mes = $meses[$mmm];
$ano = date('Y');



$url_base = "https://$_SERVER[HTTP_HOST]";



?>
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<div class="slim-mainpanel">
  <div class="container">

    <div class="report-summary-header" style="margin-top:-10px;">
      <div>
        <h4 class="tx-inverse mg-b-3">PAINEL FINANCEIRO</h4>
        <p class="mg-b-0"><i class="icon ion-calendar mg-r-3"></i> Dados Referente a
          <?php print $mes; ?> de
          <?php print $ano; ?>
        </p>
      </div>
      <div>

        <?php

        $v_conexao = $connect->query("SELECT count(id) FROM conexoes WHERE id_usuario = '" . $cod_id . "' AND conn = '1'")->fetchColumn();

        if ($v_conexao === "0") {

          ?>
          <a href="whatsapp" class="btn btn-secondary mg-r-5"><i class="fa fa-whatsapp tx-22 mg-r-10"
              aria-hidden="true"></i> API Desconectada</a>
        <?php } else {

          $idins = $dadosgerais->tokenapi;

          $curl = curl_init();

          curl_setopt_array(
            $curl,
            array(
              CURLOPT_URL => $urlapi . '/instance/connectionState/AbC123' . $idins,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'apikey: ' . $idins . ''
              ),
            )
          );

          $response = curl_exec($curl);

          curl_close($curl);

          $res = json_decode($response, true);

          $conexaoo = "false";
          $conexaoo = $res['instance']['state'];

          if ($conexaoo == 'open') {



            ?>
            <a href="whatsapp" class="btn btn-success mg-r-5 rounded"><i class="fa fa-whatsapp tx-22 mg-r-10"
                aria-hidden="true"></i> API Conectada</a>
          <?php } else {

            $editarcad = $connect->query("UPDATE conexoes SET qrcode='', conn = '0', apikey = '0' WHERE id_usuario = '" . $cod_id . "'");

            ?>
            <a href="whatsapp" class="btn btn-danger mg-r-5"><i class="fa fa-whatsapp tx-22 mg-r-10" aria-hidden="true"></i>
              API Desconectada</a>
          <?php } ?>
        <?php } ?>

        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle rounded" type="button" id="dropdownMenuButton"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="icon ion-ios-calendar-outline tx-24"></i> Alterar Mês
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="./">Mês Atual</a>
            <a class="dropdown-item" href="&mes=01">Janeiro</a>
            <a class="dropdown-item" href="&mes=02">Fevereiro</a>
            <a class="dropdown-item" href="&mes=03">Março</a>
            <a class="dropdown-item" href="&mes=04">Abril</a>
            <a class="dropdown-item" href="&mes=05">Maio</a>
            <a class="dropdown-item" href="&mes=06">Junho</a>
            <a class="dropdown-item" href="&mes=07">Julho</a>
            <a class="dropdown-item" href="&mes=08">Agosto</a>
            <a class="dropdown-item" href="&mes=09">Setembro</a>
            <a class="dropdown-item" href="&mes=10">Outubro</a>
            <a class="dropdown-item" href="&mes=11">Novembro</a>
            <a class="dropdown-item" href="&mes=12">Dezembro</a>
          </div><!-- dropdown-menu -->
        </div><!-- dropdown -->


      </div>
    </div><!-- d-flex -->
  <hr />
    
    <div class="row row-xs">

      <div class="col-sm-6 col-lg-4">
        <div class="card card-status" style="border-radius: 20px;">
          <div class="media">
            <img src="<?php echo $url_base; ?>/master/icon/vendas.png" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h1>R$: <?php echo number_format($valoresrecebidoss->totalpago, 2, ',', '.'); ?></h1>
              <p>Recebidos no mês</p>
            </div><!-- media-body -->
          </div><!-- media -->
        </div><!-- card -->
      </div><!-- col-3 -->
      <div class="col-sm-6 col-lg-4 mg-t-10 mg-lg-t-0">
        <div class="card card-status" style="border-radius: 20px;">
          <div class="media">
            <img src="<?php echo $url_base; ?>/master/icon/caixas.png" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h1>R$: <?php echo number_format($valoresrecebidossh->totalrh, 2, ',', '.'); ?></h1>
              <p>Recebidos Hoje</p>
            </div><!-- media-body -->
          </div><!-- media -->
        </div><!-- card -->
      </div><!-- col-3 -->
      <div class="col-sm-6 col-lg-4 mg-t-10 mg-sm-t-0">
        <div class="card card-status" style="border-radius: 20px;">
          <div class="media">
            <img src="<?php echo $url_base; ?>/master/icon/dinheiro.png" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h1>R$: <?php echo number_format($valoresareceberx->totalparcela, 2, ',', '.'); ?></h1>
              <p>A receber no mês</p>
            </div><!-- media-body -->
          </div><!-- media -->
        </div><!-- card -->
      </div><!-- col-3 -->


    </div><!-- row -->

    <hr />

    <div class="row row-xs">
      <div class="col-sm-6 col-lg-3 mg-t-10 mg-sm-t-0">
        <div class="card card-status" style="border-radius: 20px;">
          <div class="media">
            <img src="<?php echo $url_base; ?>/master/icon/usurioss.png" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h1><?php echo $cadclix; ?></h1>
              <p>CLIENTES CADASTRADOS</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mg-t-10 mg-sm-t-0">
        <div class="card card-status" style="border-radius: 20px;">
          <div class="media">
            <img src="<?php echo $url_base; ?>/master/icon/transacao.png" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h1><?php echo $empativosx; ?></h1>
              <p>COBRANÇAS ATIVAS</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mg-t-10 mg-sm-t-0">
        <div class="card card-status" style="border-radius: 20px;">
          <div class="media">
            <img src="<?php echo $url_base; ?>/master/icon/calendario.png" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h1><?php echo $parcelasabx; ?></h1>
              <p>MENSALIDADES EM ABERTO</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mg-t-10 mg-sm-t-0">
        <div class="card card-status" style="border-radius: 20px;">
          <div class="media">
            <img src="<?php echo $url_base; ?>/master/icon/pagarr.png" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h1><?php echo $parcelasapx; ?></h1>
              <p>MENSALIDADES PAGAS</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr />



    <div class="financial-summary">
      <h5>Resumo financeiro de contas a pagar</h5>
      <div class="summary-item">
        <img src="<?php echo $url_base; ?>/master/icon/pagarr.png" alt="Ícone de pagamento" class="summary-icon">
        <div class="summary-detail">
          <h1>R$: <?php echo number_format($valorespagosx->totalpago, 2, ',', '.'); ?> | R$:
            <?php echo number_format($valoresapagarx->totalapagar, 2, ',', '.'); ?>
          </h1>
          <span>Valores pagos | Valores a pagar</span>
        </div>
      </div>
    </div>


    <hr />

    <div class="row row-xs">

      <div class="col-lg-4">

        <a href="clientes" class="btn btn-info btn-block mg-b-10"><i class="fa fa-users tx-22 mg-r-10"
            aria-hidden="true"></i> Cadastrar Clientes</a>

      </div>

      <div class="col-lg-4">

        <a href="contas_pagar" class="btn btn-danger btn-block mg-b-10"><i class="fa fa-random tx-22 mg-r-10"
            aria-hidden="true"></i> Contas a Pagar</a>

      </div>

      <div class="col-lg-4">

        <a href="contas_receber" class="btn btn-success btn-block mg-b-10"><i class="fa fa-money tx-22 mg-r-10"
            aria-hidden="true"></i> Contas a Receber</a>

      </div>

    </div><!-- row -->

    <style>
      .chart-container {
        max-width: 1200px;
        margin: auto;
      }
    </style>

    <div class="row row-xs chart-container">
      <div class="col-lg-12">
        <canvas id="myChart"></canvas>
      </div>
    </div>



  </div><!-- container -->
</div><!-- slim-mainpanel -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/select2/js/select2.min.js"></script>
<script src="../lib/datatables/js/jquery.dataTables.js"></script>
<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>


<script src="../js/slim.js"></script>


<script>
  var valoresRecebidosMes = <?php echo json_encode($valoresrecebidoss->totalpago); ?>;
  var valoresRecebidosHoje = <?php echo json_encode($valoresrecebidossh->totalrh); ?>;
  var valoresAReceberMes = <?php echo json_encode($valoresareceberx->totalparcela); ?>;
  var clientesCadastrados = <?php echo json_encode($cadclix); ?>;
  var cobrancasAtivas = <?php echo json_encode($empativosx); ?>;
  var mensalidadesAberto = <?php echo json_encode($parcelasabx); ?>;
  var mensalidadesPagas = <?php echo json_encode($parcelasapx); ?>;

  var ctx = document.getElementById('myChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Recebidos no mês', 'Recebidos hoje', 'A receber no mês', 'Clientes cadastrados', 'Cobranças ativas', 'Mensalidades em aberto', 'Mensalidades pagas'],
      datasets: [{
        label: 'Dados',
        data: [valoresRecebidosMes, valoresRecebidosHoje, valoresAReceberMes, clientesCadastrados, cobrancasAtivas, mensalidadesAberto, mensalidadesPagas],
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)',
          'rgba(255, 99, 132, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(255, 99, 132, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true

        }
      }
    }
  });
</script>






</body>

</html>