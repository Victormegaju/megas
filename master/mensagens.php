<?php
require_once "topo.php";
?>
<div class="slim-mainpanel">
  <div class="container">

    <?php if (isset($_GET["sucesso"])) { ?>
      <div class="alert alert-solid alert-success" role="alert">
        <strong>Sucesso!!!</strong>
      </div>
      <meta http-equiv="refresh" content="1;URL=./mensagens" />
    <?php } ?>

    <div class="section-wrapper">
      <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Mensagens de Notificação
      </label>
      <hr>
      <div class="row">
        <!-- <div class="col-md-12">
        <div align="center">
            <button type="submit" class="btn btn-primary" name="cart">Atualizar <i class="fa fa-arrow-right"></i></button>
            <button type="button" class="btn btn-secondary" onclick="gerarRecibo()">Gerar Recibo</button>
        </div>
    </div> -->
      </div>
      <div class="table-wrapper">
        <table id="datatable1" class="table display responsive nowrap" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Tipo</th>
              <th>Envio Automático</th>
              <th>Mensagem</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>

            <?php
            $clientes = $connect->query("SELECT * FROM mensagens WHERE idu = '" . $cod_id . "' ORDER BY tipo ASC");
            while ($dadosclientes = $clientes->fetch(PDO::FETCH_OBJ)) {

              if ($dadosclientes->tipo == '1') {
                $tipomsg = "5 dias antes";
              }
              if ($dadosclientes->tipo == '2') {
                $tipomsg = "3 dias antes";
              }
              if ($dadosclientes->tipo == '3') {
                $tipomsg = "No dia";
              }
              if ($dadosclientes->tipo == '4') {
                $tipomsg = "Vencida";
              }
              if ($dadosclientes->tipo == '5') {
                $tipomsg = "Comprovante de Pagamento";
              }
              if ($dadosclientes->tipo == '6') {
                $tipomsg = "Cobrança Manual";
              }
              if ($dadosclientes->tipo == '7') {
                $tipomsg = "Cobrança 7 dias";
              }


              ?>
              <tr>
                <td><?php print $dadosclientes->id; ?></td>
                <td><?php print $tipomsg; ?></td>

                <?php if ($dadosclientes->status == '1') { ?>
                  <td><button class="btn btn-success btn-sm">Ativo</button></td>
                <?php } else { ?>
                  <td><button class="btn btn-danger btn-sm">Desativado</button></td>
                <?php } ?>

                <td><?php print substr($dadosclientes->msg, 0, 50); ?>...</td>

                <?php if ($dadosclientes->status == '1') { ?>
                  <td align="center">
                    <form action="classes/mensagens_exe.php" method="post">
                      <input type="hidden" name="edicli1" value="<?php print $dadosclientes->id; ?>" />
                      <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top"
                        title="Desativar Mensagem"><i class="icon fa fa-times"></i></button>
                    </form>
                  </td>
                <?php } elseif ($dadosclientes->status == '0') { ?>
                  <td align="center">
                    <form action="classes/mensagens_exe.php" method="post">
                      <input type="hidden" name="edicli2" value="<?php print $dadosclientes->id; ?>" />
                      <button type="submit" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top"
                        title="Ativar Mensagem"><i class="icon fa fa-thumbs-up"></i></button>
                    </form>
                  </td>
                <?php } ?>
                <td align="center">
                  <form action="edit_mensagens" method="post">
                    <input type="hidden" name="edicli" value="<?php print $dadosclientes->id; ?>" />
                    <button type="submit" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top"
                      title="Editar Mensagem"><i class="icon fa fa-pencil-square"></i></button>
                  </form>
                </td>
              </tr>
            <?php } ?>
            <div align="center">
              <form action="create_menssage" method="post">
                <button type="submit" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top"
                  title="Adicionar nova mensagem"><i class="icon fa fa-plus"></i> Adicionar nova mensagem</button>
              </form>
            </div>

          </tbody>
        </table>
      </div>
    </div>

  </div><!-- container -->
</div><!-- slim-mainpanel -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css"
  href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/select2/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


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


  function gerarRecibo() {
    // Preencha os detalhes do recibo
    var empresa = "#EMPRESA#";
    var cnpj = "#CNPJ#";
    var endereco = "#ENDERECO#";
    var contato = "#CONTATO#";
    var nome = "#NOME#";
    var vencimento = "#VENCIMENTO#";
    var dataPagamento = "#DATAPAGAMENTO#";
    var valor = "#VALOR#";

    // Chame a função PHP gerarRecibo usando AJAX
    $.ajax({
      url: '/recibo/recibo.php',
      type: 'post',
      data: {
        empresa: empresa,
        cnpj: cnpj,
        endereco: endereco,
        contato: contato,
        nome: nome,
        vencimento: vencimento,
        dataPagamento: dataPagamento,
        valor: valor
      },
      success: function (response) {
        // Abra o recibo em PDF em uma nova janela
        window.open('/recibo/recibo.pdf');
      }
    });
  }
</script>

</body>

</html>