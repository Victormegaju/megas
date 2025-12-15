<?php
require_once "topo.php";

// ID CLIENTE
if (isset($_GET["vercli"])) {
	$cliente = $_GET['vercli'];
}
if (isset($_POST["vercli"])) {
	$cliente = $_POST['vercli'];
}

$buscafin = $connect->query("SELECT * FROM financeiro1 WHERE Id='$cliente' AND idm ='" . $cod_id . "'");
$buscafinx = $buscafin->fetch(PDO::FETCH_OBJ);

$buscacli = $connect->query("SELECT * FROM clientes WHERE Id='" . $buscafinx->idc . "' AND idm ='" . $cod_id . "'");
$buscaclix = $buscacli->fetch(PDO::FETCH_OBJ);



// $chave = ($buscafinx->chave);

//  var_dump($chave);

?>
<div class="slim-mainpanel">
	<div class="container">
		<div align="right" class="mg-b-10"><a href="./" class="btn btn-purple btn-sm"> VOLTAR</a></div>

		<?php if (isset($_GET["sucesso"])) { ?>
			<div class="alert alert-solid alert-success" role="alert">
				<strong>Sucesso!!!</strong>
			</div>
			<meta http-equiv="refresh" content="1;URL=./ver_financeiro" />
		<?php } ?>

		<div class="row">
			<div class="col-md-12">
				<div class="card card-info">
					<div class="card-body" align="justify">
						<label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> SITUAÇÃO
							FINANCEIRA POR CLIENTE</label>
						<hr>
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="form-group">
									<h4><span style="color:#FF9966"></span> <?= $buscaclix->nome; ?></h4>
									<?php if ($buscafinx->status == 1) { ?>
										<button class="btn btn-danger btn-sm">MENSALIDADES PENDENTE </button>
									<?php } ?>
									<?php if ($buscafinx->status == 2) { ?>
										<button class="btn btn-success btn-sm">MENSALIDADES QUITADAS EM
											<?php print $buscafinx->pagoem; ?></button>
									<?php } ?>
								</div>
							</div>
						</div>
						<hr style="margin-top:-5px;">


						<div class="row">

							<div class="col-md-4">
								<div class="form-group">
									<label>Valor Final</label>
									<input type="text" class="dinheiro form-control"
										value="R$: <?php print number_format($buscafinx->vparcela * $buscafinx->parcelas, 2, ',', '.'); ?>"
										disabled>
								</div>
							</div>


							<div class="col-md-4">
								<div class="form-group">
									<label>Forma de Pagamento</label>
									<?php if ($buscafinx->formapagamento == 1) { ?>
										<input type="text" name="valor" class="form-control" value="Diário" disabled>
									<?php } ?>
									<?php if ($buscafinx->formapagamento == 7) { ?>
										<input type="text" name="valor" class="form-control" value="Semanal" disabled>
									<?php } ?>
									<?php if ($buscafinx->formapagamento == 15) { ?>
										<input type="text" name="valor" class="form-control" value="Quinzenal" disabled>
									<?php } ?>
									<?php if ($buscafinx->formapagamento == 30) { ?>
										<input type="text" name="valor" class="form-control" value="Mensal" disabled>
									<?php } ?>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label>Parcelas</label>
									<div class="styled-select">
										<input type="number" name="parcelas" class="form-control"
											value="<?php print $buscafinx->parcelas; ?>" disabled>
									</div>
								</div>
							</div>


						</div>

						<hr />

						<div class="row">
							<div class="col-md-12">
								<div class="table-wrapper">

									<table id="datatable1" class="table display responsive nowrap" width="100%">
										<thead>
											<tr>
												<th>#</th>
												<th>Data de Vencimento</th>
												<th>Data de Pagamento</th>
												<th>Valor</th>
												<th></th>
												<th></th>
												<th></th>

											</tr>
										</thead>
										<tbody>


											<?php
											session_start();

											function obterTaxaJurosDiaria($connect, $cod_id)
											{
												try {
													$buscaJuros = $connect->query("SELECT juros_diarios FROM carteira WHERE Id = '" . $cod_id . "'");
													$resultado = $buscaJuros->fetch(PDO::FETCH_OBJ);
													$taxaJurosDiaria = $resultado->juros_diarios;
													return $taxaJurosDiaria;
												} catch (PDOException $e) {
													return null;
												}
											}

											$taxaJurosDiaria = floatval(obterTaxaJurosDiaria($connect, $cod_id));
											// $buscafin2 = $connect->query("SELECT f2.*, f1.valorfinal as valor_original FROM financeiro2 f2 INNER JOIN financeiro1 f1 ON f2.chave = f1.chave WHERE f2.chave='" . $buscafinx->chave . "' AND f2.idm = '" . $cod_id . "' ORDER BY f2.Id ASC");
											
											$buscafin2 = $connect->query("SELECT f2.*, f1.valorfinal as valor_original, f1.vparcela FROM financeiro2 f2 INNER JOIN financeiro1 f1 ON f2.chave = f1.chave WHERE f2.chave='" . $buscafinx->chave . "' AND f2.idm = '" . $cod_id . "' ORDER BY f2.Id ASC");
											$jurosCalculados = false;

											while ($buscafinx2 = $buscafin2->fetch(PDO::FETCH_OBJ)) {
												$data1 = date("d/m/Y");
												$data2 = $buscafinx2->datapagamento;

												$data1 = implode('-', array_reverse(explode('/', $data1)));
												$data2 = implode('-', array_reverse(explode('/', $data2)));

												$d1 = strtotime($data1);
												$d2 = strtotime($data2);

												$prazo = ($d2 - $d1) / 86400;

												$prazox = explode(".", $prazo);
												$diasprazox = str_replace("-", "", $prazo);

												

												if ($prazo < 0 && ($buscafinx2->juros_calculados == 0 || $buscafinx2->taxa_juros_diaria != $taxaJurosDiaria || $buscafinx2->dias_vencidos != abs($prazo))) {
													$diasAtraso = abs($prazo);
													$juro = bcmul($taxaJurosDiaria, $diasAtraso, 2);
													// $valorParcela = bcadd($buscafinx2->valor_original, $juro, 2);
													$valorParcela = bcadd($buscafinx2->vparcela, $juro, 2);

													$updateParcela = $connect->prepare("UPDATE financeiro2 SET parcela = :parcela, juros_calculados = 1, taxa_juros_diaria = :taxaJurosDiaria, dias_vencidos = :dias_vencidos WHERE Id = :id");
													$updateParcela->execute(
														array(
															':parcela' => $valorParcela,
															':id' => $buscafinx2->Id,
															':taxaJurosDiaria' => $taxaJurosDiaria,
															':dias_vencidos' => abs($prazo)
														)
													);
													$jurosCalculados = true;
												
												} else if ($prazo >= 0 && ($buscafinx2->dias_vencidos != 0 || $buscafinx2->parcela != $buscafinx2->valor_original)) {
													$updateParcela = $connect->prepare("UPDATE financeiro2 SET dias_vencidos = 0, parcela = :valorParcela WHERE Id = :id");
													$updateParcela->execute(
														array(
															':id' => $buscafinx2->Id,
															':valorParcela' => $valorParcela
														)
													);
												}

												if ($jurosCalculados) {
													echo "
														<script>
														$(document).ready(function() {
															getParcelaAtualizada('{$buscafinx2->Id}');
														});
														</script>
													";
												}

												?>

												<tr>

													<td><?php print $buscafinx2->Id; ?></td>
													<td><?php print $buscafinx2->datapagamento; ?><br />
														<?php if ($prazox[0] < 0 and $buscafinx2->pagoem === "n") { ?>
															<span class="square-8 bg-danger mg-r-5 rounded-circle"></span>
															Vencida a <?php print $diasprazox; ?> dia(s)
														<?php } ?>

														<?php if ($buscafinx2->pagoem != "n") { ?>
															<span class="square-8 bg-success mg-r-5 rounded-circle"></span>
															Mensalidade Paga
														<?php } else { ?>
															<?php if ($prazox[0] >= 0) { ?>
																<span class="square-8 bg-success mg-r-5 rounded-circle"></span> Em
																Dias
															<?php } ?>
														<?php } ?>


													</td>
													<?php if ($buscafinx2->pagoem == "n") { ?>
														<td>--</td>
													<?php } else { ?>
														<td><?php print $buscafinx2->pagoem; ?></td>
													<?php } ?>
													<td id="parcela-<?php echo $buscafinx2->Id; ?>">R$:
														<?php print number_format($buscafinx2->parcela, 2, ',', '.'); ?>
													</td>
													</td>


													<?php if ($buscafinx2->status == 2) { ?>
														<td><button class="btn btn-success btn-sm" data-toggle="tooltip"
																data-placement="top" title="Esta mensalidade já está paga"><i
																	class="far fa-thumbs-up" aria-hidden="true"></i></button>
														</td>
													<?php } else { ?>
														<td>
															<form action="classes/baixa_exe.php" method="get">
																<input type="hidden" name="idfin2"
																	value="<?php print $buscafinx2->Id; ?>" />
																<input type="hidden" name="codcli"
																	value="<?php print $cliente; ?>" />
																<input type="hidden" name="emdias" value="ok" />
																<button type="submit" class="btn btn-sm btn-warning"
																	data-toggle="tooltip" data-placement="top"
																	title="Dar baixa manual na mensalidade"><i
																		class="fas fa-check-square"
																		aria-hidden="true"></i></button>
															</form>
														</td>
													<?php } ?>

													<?php if ($buscafinx2->status == 1 && $prazo <= 0) { ?>
														<td>
															<form onsubmit="gerarPagamentos_omie(event)">
																<input type="hidden" name="dcob"
																	value="<?php print $cliente; ?>" />
																<input type="hidden" name="cob"
																	value="<?php print $buscafinx2->Id; ?>" />
																<input type="hidden" name="codclix"
																	value="<?php print $buscaclix->Id; ?>" />
																<input type="hidden" name="tipom" value="6" />
																<button type="submit" class="btn btn-sm btn-info"
																	data-toggle="tooltip" data-placement="top"
																	title="Enviar cobrança da mensalidade no WhatsAPP"><i
																		class="fab fa-whatsapp" aria-hidden="true"></i></button>
															</form>
														</td>
													<?php } ?>


													<?php if ($buscafinx2->status == 1 && $prazo >= 1) { ?>
														<td>
															<form onsubmit="gerarPagamentos_omie(event)">
																<input type="hidden" name="dcob"
																	value="<?php print $cliente; ?>" />
																<input type="hidden" name="cob"
																	value="<?php print $buscafinx2->Id; ?>" />
																<input type="hidden" name="codclix"
																	value="<?php print $buscaclix->Id; ?>" />
																<input type="hidden" name="tipom" value="4" />
																<button type="submit" class="btn btn-sm btn-info"
																	data-toggle="tooltip" data-placement="top"
																	title="Enviar cobrança da mensalidade no WhatsAPP"><i
																		class="fab fa-whatsapp" aria-hidden="true"></i></button>
															</form>
														</td>
													<?php } ?>

													<?php if ($buscafinx2->status == 2) { ?>
														<td>
															<form onsubmit="enviarcomprovate(event)">
																<input type="hidden" name="dcob"
																	value="<?php print $cliente; ?>" />
																<input type="hidden" name="cob"
																	value="<?php print $buscafinx2->Id; ?>" />
																<input type="hidden" name="codclix"
																	value="<?php print $buscaclix->Id; ?>" />
																<input type="hidden" name="tipom" value="5" />
																<button type="submit" class="btn btn-sm btn-dark"
																	data-toggle="tooltip" data-placement="top"
																	title="Enviar comprovante de pagamento da mensalidade"><i
																		class="bi bi-box-arrow-up-right"
																		aria-hidden="true"></i></button>
															</form>
														</td>
													<?php } ?>


													<td >
														<form action="classes/clientes_exe.php" method="post">
															<input type="hidden" name="delparcela"
																value="<?php print $buscafinx2->chave; ?>" />
															<input type="hidden" name="idparcela"
																value="<?php print $buscafinx2->Id; ?>" />
															<button type="submit" class="btn btn-dark btn-sm"
																onclick='return pergunta();'><i
																	class="icon fa fa-times"></i></button>
														</form>
													</td>





												</tr>

											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>


						<hr />
						<?php
						$buscafink = $connect->query("SELECT * FROM financeiro1 WHERE chave='" . $buscafinx->chave . "' AND idm ='" . $cod_id . "'");
						$buscafinkx = $buscafink->fetch(PDO::FETCH_OBJ);

						$buscafinr = $connect->query("SELECT * FROM financeiro2 WHERE chave='" . $buscafinx->chave . "' AND status='2' AND idm ='" . $cod_id . "'");
						$buscafinx = $buscafinr->rowCount();
						?>
						<?php if ($buscafinx == $buscafinkx->parcelas) { ?>
							<div class="row">
								<div class="col-md-12">
									<div align="center">
										<form action="classes/baixa_exe.php" method="get">
											<input type="hidden" name="idbaixa" value="<?php print $cliente; ?>" />
											<input type="hidden" name="codcli" value="<?php print $cliente; ?>" />
											<button type="submit" class="btn btn-success">Baixar Cobrança</button>
										</form>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css"
	href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/select2/js/select2.min.js"></script>
<script src="../lib/datatables/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
<script src="../js/slim.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" type="text/css" href="https://agilizaweb.com/atoast.css" />

<script src="https://agilizaweb.com/atoast.js"></script>


<script>

	function gerarPagamentos_omie(event) {
		console.log('gerarPagamentos_omie');
		event.preventDefault();
		let atoast = aToast('Gerando cobrança', {
			position: 'center',
			type: 'promise'
		});
		let data = {}
		var form = event.target;
		form.querySelectorAll('input').forEach(function (input) {
			data[input.name] = input.value;
		});

		$.ajax({
			url: '../cobm.php',
			type: 'POST',
			data: data,
			success: function (response) {
				console.log('sucesso:', response);
				aToastUpdate(atoast, 'success', 'Cobrança enviada com sucesso');
			},
			error: function (error) {
				aToastUpdate(atoast, 'error', 'Erro ao enviar cobrança');
				console.error(error);
			}
		});
	}
	function enviarcomprovate(event) {
		console.log('gerarPagamentos_omie');
		event.preventDefault();
		let atoast = aToast('Gerando comprovante', {
			position: 'center',
			type: 'promise'
		});
		let data = {}
		var form = event.target;
		form.querySelectorAll('input').forEach(function (input) {
			data[input.name] = input.value;
		});

		$.ajax({
			url: '../agrad.php',
			type: 'POST',
			data: data,
			success: function (response) {
				console.log('sucesso:', response);
				aToastUpdate(atoast, 'success', 'Cobrança enviada com sucesso');
			},
			error: function (error) {
				aToastUpdate(atoast, 'error', 'Erro ao enviar cobrança');
				console.error(error);
			}
		});
	}

	function getParcelaAtualizada(id) {
		$.ajax({
			url: '/master/atualizar_parcelas.php?id=' + id,
			type: 'GET',
			dataType: 'json',
			success: function (parcela) {
				// Imprime o valor recebido no console
				//console.log(parcela);

				// Atualiza a interface do usuário com os dados da parcela atualizada
				$('#parcela-' + id).text(parcela.parcela);
			},
			error: function (error) {
				//console.error(error);
			}
		});
	}

	setInterval(function () {
		var ids = [/* lista de IDs de parcelas */];
		for (var i = 0; i < ids.length; i++) {
			getParcelaAtualizada(ids[i]);
		}
	}, 3000);

</script>
<script>
	$(function () {
		'use strict';

		$('#datatable1').DataTable({
			responsive: true,
			language: {
				searchPlaceholder: 'Buscar...',
				sSearch: '',
				lengthMenu: '_MENU_ ítens',
			}
		});

		$('#datatable2').DataTable({
			bLengthChange: false,
			searching: false,
			responsive: true
		});

		// Select2
		$('.dataTables_length select').select2({ minimumResultsForSearch: Infinity });

	});

	function pergunta() {
    // retorna true se confirmado, ou false se cancelado
    return confirm('Tem certeza que deseja excluir esta cobrança?');
  }
</script>


<script src="../js/slim.js"></script>

<script>
	$(function () {
		'use strict';

		// Initialize tooltip
		$('[data-toggle="tooltip"]').tooltip();

		// colored tooltip
		$('[data-toggle="tooltip-primary"]').tooltip({
			template: '<div class="tooltip tooltip-primary" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-success"]').tooltip({
			template: '<div class="tooltip tooltip-success" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-warning"]').tooltip({
			template: '<div class="tooltip tooltip-warning" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-danger"]').tooltip({
			template: '<div class="tooltip tooltip-danger" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-info"]').tooltip({
			template: '<div class="tooltip tooltip-info" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-indigo"]').tooltip({
			template: '<div class="tooltip tooltip-indigo" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-purple"]').tooltip({
			template: '<div class="tooltip tooltip-purple" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-teal"]').tooltip({
			template: '<div class="tooltip tooltip-teal" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-orange"]').tooltip({
			template: '<div class="tooltip tooltip-orange" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$('[data-toggle="tooltip-pink"]').tooltip({
			template: '<div class="tooltip tooltip-pink" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
		});
	});
</script>
</body>

</html>