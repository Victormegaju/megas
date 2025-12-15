<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "topo.php";

?>

<div class="slim-mainpanel">
    <div class="container">
        <div align="right" class="mg-b-10">
            <a href="mensagens" class="btn btn-purple btn-sm">VOLTAR</a>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-body" align="justify">
                        <label class="section-title">
                            <i class="fa fa-check-square-o" aria-hidden="true"></i>
                            CRIAR NOVA MENSAGEM
                        </label>

                        <hr>

                        <form action="classes/mensagens_exe.php" method="post">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Tipo de Mensagem:</label>
                                        <select name="tipo" class="form-control">
                                            <option value="1">Cobrança 5 dias</option>
                                            <option value="2">Cobrança 3 dias</option>
                                            <option value="3">Cobrança no dia</option>
                                            <option value="4">Cobrança mensalidade vencida</option>
                                            <option value="5">Agradecimento</option>
                                            <option value="6">Cobrança Manual</option>
                                            <option value="7">Cobrança 7 dias</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Hora de execução:</label>
                                        <input id="hora" type="time" name="hora" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Mensagem:</label>
                                        <textarea name="msg" cols="30" rows="7" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Opcionais:</label>
                                        <br />

                                        <a href="#" class="optional" data-value="#EMPRESA#">#EMPRESA# - Nome da Empresa</a>

                                        <br />

                                        <a href="#" class="optional" data-value="#CNPJ#">#CNPJ# - CNPJ da Empresa</a>

                                        <br />

                                        <a href="#" class="optional" data-value="#ENDERECO#">#ENDERECO# - Endereço</a>

                                        <br />

                                        <a href="#" class="optional" data-value="#CONTATO#">#CONTATO# - Telefone</a>

                                        <br />

                                        <a href="#" class="optional" data-value="#NOME#">#NOME# - Nome do Cliente</a>

                                        <br />

                                        <a href="#" class="optional" data-value="#VENCIMENTO#">#VENCIMENTO# - Data de vencimento da parcela</a>

                                        <br />

                                        <a href="#" class="optional" data-value="#DATAPAGAMENTO#">#DATAPAGAMENTO# - Data de pagamento da parcela</a>

                                        <br />

                                        <a href="#" class="optional" data-value="#VALOR#">#VALOR# - Valor da Parcela</a>

                                        <br />

                                        <a href="#" class="optional" data-value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>#LINK#">#LINK# - Link de Pagamento final, adicione seu domínio antes.</a>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <div align="center">
                                    <input type="hidden" name="cart" value="1">
                                        <button type="submit" class="btn btn-primary" name="cart">Criar <i class="fa fa-arrow-right"></i></button>
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

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/jquery.cookie/js/jquery.cookie.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="../lib/select2/js/select2.full.min.js"></script>
<script>
    function upperCaseF(a) {
        setTimeout(function () {
            a.value = a.value.toUpperCase();
        }, 1);
    }

    $(function() {
        'use strict';

        $("#hora").mask("99:99");
        
    
    $('.optional').on('click', function (event) {
            event.preventDefault();

            // Adiciona o valor opcional ao final do campo de texto da mensagem
            var textarea = $('textarea[name="msg"]');
            textarea.val(textarea.val() + ' ' + $(this).data('value'));
        });
    });
</script>
<script src="../js/slim.js"></script>
</body>
</html>
<?php
//ob_end_flush();
?>