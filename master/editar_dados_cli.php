<?php
require_once "topo.php";

$id_cliente = isset($_POST['vercli']) ? $_POST['vercli'] : '';

// Busca o nome do cliente
$cliente_info = $connect->query("SELECT nome FROM clientes WHERE Id='$id_cliente'")->fetch(PDO::FETCH_OBJ);

// Define o nome do cliente ou um valor padrão se não houver cliente com o ID fornecido
$nome_cliente = $cliente_info ? $cliente_info->nome : '';

// Busca os dados da conta do cliente apenas se o status for 1
$conta_info = $connect->query("SELECT * FROM financeiro1 WHERE idc='$id_cliente' AND status = 1")->fetch(PDO::FETCH_OBJ);

// Define os dados da conta do cliente ou valores padrão se não houverem dados
$dados_conta = new stdClass();
$dados_conta->cliente = $nome_cliente;
$dados_conta->valorfinal = $conta_info ? $conta_info->valorfinal : '';
$dados_conta->parcelas = $conta_info ? $conta_info->parcelas : 1;
$dados_conta->primeira_parcela = $conta_info ? $conta_info->primeiraparcela : '';


$buscaPrimeiraParcela = $connect->query("SELECT f2.datapagamento FROM financeiro2 f2 INNER JOIN financeiro1 f1 ON f2.chave = f1.chave WHERE f2.idc = '" . $id_cliente . "' ORDER BY f2.datapagamento ASC LIMIT 1");
$primeiraParcela = $buscaPrimeiraParcela->fetch(PDO::FETCH_OBJ);


if ($primeiraParcela && isset($primeiraParcela->datapagamento)) {
  $primeiraParcelaDateTime = DateTime::createFromFormat('d/m/Y', $primeiraParcela->datapagamento);
  if ($primeiraParcelaDateTime) {
    $primeiraParcelaFormatada = $primeiraParcelaDateTime->format('Y-m-d');
  }
}


?>

<div class="slim-mainpanel">
  <div class="container">
    <div align="right" class="mg-b-10">
      <a href="contas_receber" class="btn btn-purple btn-sm"> VOLTAR</a>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card card-info">
          <div class="card-body" align="justify">
            <label class="section-title">
              <i class="fa fa-check-square-o" aria-hidden="true"></i>
              CADASTRO CONTAS A RECEBER
            </label>
            <hr>
            <form action="cad_contas_simulador_edit" method="post">
            <input type="hidden" name="vercli" value="<?php echo $id_cliente; ?>">

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Selecione o cliente</label>
                    <select class="form-control select2-show-search" name="cliente" required>
                      <option value="">Pesquisar...</option>
                      <?php
                      $buscacli = $connect->query("SELECT * FROM clientes WHERE idm = '" . $cod_id . "' ORDER BY nome ASC");
                      while ($buscaclix = $buscacli->fetch(PDO::FETCH_OBJ)) {
                        $selected = $buscaclix->Id == $id_cliente ? 'selected' : '';
                        echo "<option value='" . $buscaclix->Id . "' $selected>" . $buscaclix->cpf . " - " . $buscaclix->nome . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
              <hr />
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Valor da mensalidade</label>
                    <input type="text" name="valor" class="dinheiro form-control"
                      value="<?php echo number_format($dados_conta->valorfinal, 2, ',', '.'); ?>" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Quantidade de mensalidades</label>
                    <input type="number" name="parcelas" class="form-control"
                      value="<?php echo $dados_conta->parcelas; ?>" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Data de vencimento primeiro pagamento</label>
                    <input type="date" name="datap" class="form-control"
                      value="<?php echo $primeiraParcelaFormatada; ?>" required>
                  </div>
                </div>
              </div>
              <hr />
              <div class="row">
                <div class="col-md-12">
                  <div align="center">
                  <button type="submit" class="btn btn-primary" name="cart">
                      Avançar <i class="fa fa-arrow-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../js/slim.js"></script>
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/jquery.cookie/js/jquery.cookie.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="../lib/select2/js/select2.full.min.js"></script>
<script src="../js/moeda.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>