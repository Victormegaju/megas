<?php
date_default_timezone_set('America/Sao_Paulo');

require_once "../db/Conexao.php";

$idCob = $_GET["idCob"];
$idInst = $_GET["idInst"];
$idFin = $_GET["idFin"];

$buscafin2  = $connect->query("SELECT * FROM financeiro2 WHERE Id = '" . $idFin . "'");
$buscafinan = $buscafin2->fetch(PDO::FETCH_OBJ);

$idcli = $buscafinan->idc;
$idmas = $buscafinan->idm;

$buscacli  = $connect->query("SELECT nome FROM clientes WHERE Id='" . $idcli . "'");
$buscacli = $buscacli->fetch(PDO::FETCH_OBJ);

$partesNome = explode(" ", $buscacli->nome);
$primeiroNome = $partesNome[0];
$ultimoNome = end($partesNome);

$buscaconfig = $connect->query("SELECT * FROM carteira WHERE Id='" . $idmas . "'");
$buscaconfig = $buscaconfig->fetch(PDO::FETCH_OBJ);

$tokenmp = $buscaconfig->tokenmp;

$buscapgmtos = $connect->query("SELECT * FROM mercadopago WHERE id = '" . $idCob . "'");
$buscapgmtocs = $buscapgmtos->rowCount();

if($buscapgmtocs == "1") {
	$buscapgmtos = $buscapgmtos->fetch(PDO::FETCH_OBJ);

	$idtrans = $buscapgmtos->idp;

	$statuspgmot = $buscapgmtos->status;

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/'.$idtrans,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Bearer '.$tokenmp.'',
	  ),
	));

	$response = curl_exec($curl);

	curl_close($curl);

	$response = json_decode($response, true);

	$statuspgmto = $response["status"];

	if($statuspgmto === "approved") {
		$upd = $connect->query("UPDATE mercadopago SET status = '" . $statuspgmto . "' WHERE id = '" . $idCob . "'");

		$upd = $connect->query("UPDATE financeiro2 SET status = '2', pagoem = '".date("d/m/Y")."' WHERE Id = '".$idFin."'");
	}
}

$buscapgmtos2	= $connect->query("SELECT * FROM mercadopago WHERE id = '".$idCob."'");

$buscapgmtos2n = $buscapgmtos2->fetch(PDO::FETCH_OBJ);

$statuspgmot = $buscapgmtos2n->status;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
<meta name="description" content="Cobranças automáticas para whatsapp.">
<meta name="keywords" content="financeiro, cobranças, whatsapp">
<meta property="og:url" content="<?php print $_urlmaster;?>">
<meta property="og:title" content="<?php print $_nomesistema; ?>">
<meta property="og:description" content="Cobranças automáticas para whatsapp.">
<meta property="og:type" content="website">
<meta property="og:image" content="<?php print $_urlmaster;?>/img/favicon.png">
<meta property="og:image:width" content="520">
<meta property="og:image:type" content="image/png">
<meta property="og:site_name" content="<?php print $_nomesistema; ?>">
<meta property="og:locale" content="pt-BR">
<title>Whatsapp Cobranças: Integre sua empresa | <?php print $_nomesistema; ?> </title>
<link rel="icon" href="<?php print $_urlmaster;?>/img/favicon.png" sizes="32x32" type="image/png">
<link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">
<link rel="stylesheet" href="../css/slim.css">
</head>

<body>
	<div class="slim-mainpanel">
		<div class="container">
			<div class="slim-pageheader">
				<ol class="breadcrumb slim-breadcrumb">Pagamento da Mensalidade</ol>

				<h6 class="slim-pagetitle"><?php print $primeiroNome ; ?> <?php print $ultimoNome ; ?></h6>
			</div>

			<?php if($statuspgmot == "approved") { ?>
				<div class="alert alert-success" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>

					Pagamento aprovado.
				</div>
			<?php } else { ?>
				<meta http-equiv="refresh" content="5;URL=./?idCob=<?php print $idCob; ?>&idInst=<?php print $idInst; ?>&idFin=<?php print $idFin; ?>" />
			<?php
				$emprestimos1	= $connect->query("SELECT * FROM mercadopago WHERE id = '".$idCob."'");

				while ($dadosemprestimos1 = $emprestimos1->fetch(PDO::FETCH_OBJ)) {
			?>

			<div class="mg-t-10">
				<div class="card card-body">
					<div class="row">
						<div class="col-md-6 mg-b-5">
							<h5 class="tx-gray-800 mg-b-25">QRcode PIX</h5>

							<center>
								<img src="data:image/png;base64,<?php echo $dadosemprestimos1->qrcode; ?>" width="220">

								<br/>

								Pagamento mensalidade com vencimento em

								<br/>

								<?php echo $buscafinan->datapagamento; ?>

								<br/>

								<h2 class="tx-success">R$: <?php print number_format($dadosemprestimos1->valor, 2, ',', '.');?></h2>
							</center>
						</div>

						<div class="col-md-6 bd-l">
							<h5 class="tx-gray-800 mg-b-25">Chave Copia e Cola</h5>

							<div class="alert alert-success" role="alert">
								<strong>Chave gerada com sucesso</strong>
							</div>

							<p id="vps" style="display:none"><?php print $dadosemprestimos1->linhad;?></p>

							<a href="#" class="btn btn-dark bt-sm btn-block" onclick="copyToClipboard('#vps')" title="Copiar Chave">Clique aqui para copiar a chave</a>

							<br/>

							<h5 class="tx-gray-800 mg-b-25">Importante</h5>

							<h6>A confirmação do pagamento é feita de forma automática em até 5 minutos após a realização do pagamento.</h6>

							<h6>Você terá até 24hrs para realizar o pagamento utilizando esses dados. Após este período o sistema gera um novo QRcode.</h6>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>

	<script src="../../lib/jquery/js/jquery.js"></script>

	<script src="../../js/slim.js"></script>

	<script>
	function copyToClipboard(element) {
		var $temp = $("<input>");

		$("body").append($temp);

		$temp.val($(element).text()).select();

		document.execCommand("copy");

		$temp.remove();
	}
	</script>
</body>
</html>
