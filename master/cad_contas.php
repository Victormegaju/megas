<?php require_once "topo.php"; ?>

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

            <form action="cad_contas_simulador" method="post">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Selecione o cliente</label>

                    <select class="form-control select2-show-search" name="cliente" required>
                      <option value="">Pesquisar...</option>

                      <?php $buscacli = $connect->query("SELECT * FROM clientes WHERE idm = '" . $cod_id . "' ORDER BY nome ASC");
                      while ($buscaclix = $buscacli->fetch(PDO::FETCH_OBJ)) { ?>

                        <option value="<?= $buscaclix->Id; ?>"><?php echo $buscaclix->cpf; ?> -
                          <?php echo $buscaclix->nome; ?>
                        </option>

                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>

              <hr />

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Valor da mensalidade</label>

                    <div class="styled-select">
                      <input type="text" name="valor" class="dinheiro form-control" required>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Quantidade de mensalidades</label>

                    <div class="styled-select">
                      <input type="number" name="parcelas" class="form-control" value="1" required>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Data de vencimento primeiro pagamento</label>

                    <div class="styled-select">
                      <input type="date" id="datap" name="datap" class="form-control" required>
                    </div>
                  </div>
                </div>
              </div>

              <hr />

              <div class="row">
                <div class="col-md-12">
                  <div align="center">
                    <button type="submit" class="btn btn-primary" name="cart" id="btn-avancar">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/select2/js/select2.min.js"></script>
<script src="../lib/datatables/js/jquery.dataTables.js"></script>
<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>


<script src="../js/slim.js"></script>
<script src="../lib/jquery/js/jquery.js"></script>

<script src="../lib/bootstrap/js/bootstrap.js"></script>

<script src="../lib/jquery.cookie/js/jquery.cookie.js"></script>

<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>

<script src="../lib/select2/js/select2.full.min.js"></script>

<script src="../js/moeda.js"></script>

<script>$('.dinheiro').mask('#.##0,00', { reverse: true });</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- <script>
  document.getElementById('datap').addEventListener('change', function() {
    var selectedDate = new Date(this.value + 'T00:00');
    var dayOfWeek = selectedDate.getDay();

    // Se o dia da semana for sábado (6) ou domingo (0), exiba um alerta e limpe o campo de entrada
    if (dayOfWeek == 6 || dayOfWeek == 0) {
      toastr.warning('Por favor, selecione um dia útil. Cobranças não podem cair em dias de sábados ou domingos.');
      this.value = '';
    }
  });
</script> -->
<script>
  
  $(function () {
    'use strict';

    $('.select2').select2({
      minimumResultsForSearch: Infinity
    });

    $('.select2-show-search').select2({
      minimumResultsForSearch: ''
    });

    $('#select2').select2({
      dropdownCssClass: 'hover-success',
      minimumResultsForSearch: Infinity
    });

    $('#select3').select2({
      dropdownCssClass: 'hover-danger',
      minimumResultsForSearch: Infinity
    });

    $('#select4').select2({
      containerCssClass: 'select2-outline-success',
      dropdownCssClass: 'bd-success hover-success',
      minimumResultsForSearch: Infinity
    });

    $('#select5').select2({
      containerCssClass: 'select2-outline-info',
      dropdownCssClass: 'bd-info hover-info',
      minimumResultsForSearch: Infinity
    });

    $('#select6').select2({
      containerCssClass: 'select2-full-color select2-primary',
      minimumResultsForSearch: Infinity
    });

    $('#select7').select2({
      containerCssClass: 'select2-full-color select2-danger',
      dropdownCssClass: 'hover-danger',
      minimumResultsForSearch: Infinity
    });

    $('#select8').select2({
      dropdownCssClass: 'select2-drop-color select2-drop-primary',
      minimumResultsForSearch: Infinity
    });

    $('#select9').select2({
      dropdownCssClass: 'select2-drop-color select2-drop-indigo',
      minimumResultsForSearch: Infinity
    });

    $('#select10').select2({
      containerCssClass: 'select2-full-color select2-primary',
      dropdownCssClass: 'select2-drop-color select2-drop-primary',
      minimumResultsForSearch: Infinity
    });

    $('#select11').select2({
      containerCssClass: 'select2-full-color select2-indigo',
      dropdownCssClass: 'select2-drop-color select2-drop-indigo',
      minimumResultsForSearch: Infinity
    });
  });

  $(document).ready(function () {
    $('select[name="cliente"]').change(function () {
      var cliente_id = $(this).val();

      $.ajax({
        type: 'POST',
        url: '/master/editar_dados_cli_processa.php',
        data: {
          cliente_id: cliente_id
        },
        success: function (response) {
          if (response == 'nao_permitir') {
            Swal.fire({
              icon: 'warning',
              title: 'Atenção',
              text: 'Existe uma cobrança pendente para este cliente.'
            });
            $('#btn-avancar').prop('disabled', true);
          } else if (response == 'permitir') {
            $('#btn-avancar').prop('disabled', false);
          }
        }
      });
    });
  });
</script>
</body>

</html>