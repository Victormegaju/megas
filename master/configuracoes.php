<?php
require_once "topo.php";



$editarcat = $connect->query("SELECT * FROM carteira WHERE Id='$cod_id'");
$dadoscat = $editarcat->fetch(PDO::FETCH_OBJ);





$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$url = $scheme . "://" . $host;

?>

<div class="slim-mainpanel">
  <div class="container">
    <div align="right" class="mg-b-10"><a href="./" class="btn btn-purple btn-sm"> VOLTAR</a></div>

    <?php if (isset($_GET["sucesso"])) { ?>
      <div class="alert alert-solid alert-success" role="alert">
        <strong>Sucesso!!!</strong>
      </div>

      <meta http-equiv="refresh" content="1;URL=./configuracoes" />
    <?php } ?>

    <div class="row">
      <div class="col-md-12">
        <div class="card card-info">
          <div class="card-body" align="justify">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> DADOS DA
              EMPRESA</label>
            <hr>

            <form action="classes/config_exe.php" method="post" enctype="multipart/form-data">
              <input type="hidden" name="edit_emp" value="<?php print $edicli; ?>">

              <div class="row">
                <?php if ($cod_id == 1) { ?>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>LOGO DA EMPRESA</label>
                      <input type="file" class="form-control" name="logoEmpresa" accept="image/png"
                        style="height: 42px; padding: 6px;">
                    </div>
                  </div>
                <?php } ?>

                <div class="col">
                  <div class="form-group">
                    <label>NOME COMERCIAL</label>
                    <input type="text" class="form-control" name="nomecom" value="<?php print $dadoscat->nomecom; ?>"
                      required>
                  </div>
                </div>

                <div class="col">
                  <div class="form-group">
                    <label>CNPJ</label>
                    <div class="styled-select">
                      <input type="text" class="form-control" name="cnpj" value="<?php print $dadoscat->cnpj; ?>"
                        required>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>ENDEREÇO</label>
                    <input type="text" class="form-control" name="enderecom"
                      value="<?php print $dadoscat->enderecom; ?>" required>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>CONTATO</label>
                    <input type="text" class="form-control" name="contato" value="<?php print $dadoscat->contato; ?>"
                      required>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <label>Modelo de Cobrança</label>
                    <select class="form-control" name="tipopgmto" required>
                      <option value="1" <?php if($dadoscat->pagamentos == "1") echo 'selected="selected"'; ?>>Mercado Pago</option>
                      <option value="2" <?php if($dadoscat->pagamentos == "2") echo 'selected="selected"'; ?>>Sem Gateway</option>
                       <!-- <option value="3" <!?php if($dadoscat->pagamentos == "3") echo 'selected="selected"'; ?>>PagSeguro</option>  -->
                    </select>
                  </div>
                </div>
                <div class="col-md-2 <?php echo $dadoscat->tipo != 1 ? 'hidden' : ''; ?>">
                  <div class="form-group">
                    <label for="background-input">Background Login</label>
                    <input type="text" id="background-input" class="form-control" name="background" value="<?php echo htmlspecialchars($dadoscat->background); ?>">
                  </div>
                </div>

                <style>
                .hidden {
                  display: none;
                }
                </style>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="juros-diarios-input">Juros Diários</label>
                    <input type="text" id="juros-diarios-input" class="form-control" name="juros_diarios" value="<?php echo htmlspecialchars($dadoscat->juros_diarios); ?>">
                  </div>
                </div>
                </div>
                <hr />

                <div class="row">
                  <div class="col-md-12">
                    <div align="center"> <button type="submit" class="btn btn-primary" name="cart">Salvar <i
                        class="fa fa-arrow-right"></i></button></div>
                  </div>
                </div>
                </form>

          </div>
        </div>
      </div>
    </div>


    <?php if ($dadoscat->tipo == 1) { ?>

      <br />

      
    <?php } ?>
    <script src="../lib/jquery/js/jquery.js"></script>
    <script src="../lib/jquery.cookie/js/jquery.cookie.js"></script>
    <script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
    <script src="../lib/select2/js/select2.full.min.js"></script>
    <script src="../js/moeda.js"></script>
    <script>
      $('.dinheiro').mask('#.##0,00', { reverse: true });
    </script>
    <script>
      function upperCaseF(a) {
        setTimeout(function () {
          a.value = a.value.toUpperCase();
        }, 1);
      }
    </script>
    <script src="../js/slim.js"></script>
    </body>

    </html>
    <?php
    ob_end_flush();
    ?>