<?php require_once "topo.php"; ?>

  <div class="slim-mainpanel">
    <div class="container">
      <div align="right" class="mg-b-10">
        <a href="categorias" class="btn btn-purple btn-sm">VOLTAR</a>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card card-info">
            <div class="card-body" align="justify">
              <label class="section-title">
                <i class="fa fa-check-square-o" aria-hidden="true"></i> CADASTRO DE CATEGORIA
              </label>

              <hr>

              <form id="formulario">
                <input type="hidden" name="cad_cat" id="cad_cat" value="ok">

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Nome</label>

                      <input type="text" class="form-control" name="nome" id="nome" maxlength="160" onkeydown="upperCaseF(this)" required>
                    </div>
                  </div>
                </div>

                <hr />

                <div class="row">
                  <div class="col-md-12">
                    <div align="center">
                      <button type="button" onclick="inserirRegistro()" class="btn btn-primary" name="cart" onclick="validarDocumento()">Salvar <i class="fa fa-arrow-right"></i></button>
                    </div>
                  </div>
                </div>
              </form>

              <div id="mensagem"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../lib/jquery/js/jquery.js"></script>
  <script src="https://rawgit.com/RobinHerbots/Inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
  <script>
  function inserirRegistro() {
    var nome = $("#nome").val();

    var cad_cat = $("#cad_cat").val();

    $.ajax({
      type: "POST",
      url: "classes/categoria_exe.php",
      data: {
        nome: nome,
        cad_cat: cad_cat
      },
      success: function (response) {
        window.location.href = './categorias';
      }
    });
  }

  function upperCaseF(a) {
    setTimeout(function () {
      a.value = a.value.toUpperCase();
    }, 1);
  }

  $("input[id*='cpfcnpj']").inputmask({
    mask: ['999.999.999-99', '99.999.999/9999-99'],
    keepStatic: true
  });
  </script>
  <script src="../js/slim.js"></script>
</body>
</html>
