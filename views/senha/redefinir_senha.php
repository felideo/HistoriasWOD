<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Redefinição de Senha</h3>
            </div>
            <div class="panel-body">
                <form id="redefinir_senha" method="post" action="<?php echo URL; ?>senha/update/<?php echo $this->token; ?>">
                    <fieldset>
                        <div class="form-group">
                            <input id="senha_01" class="form-control" placeholder="Senhar" name="senha" type="password" autofocus>
                        </div>
                        <div class="form-group">
                            <input id="senha_02" class="form-control" placeholder="Digita a Senha Novamente" type="password" value="">
                        </div>
                        <button id="enviar" class="btn btn-lg btn-success btn-block" type="button" >Confirmar</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#enviar').on('click', function(){
        if($('#senha_01').val() != $('#senha_02').val()){
            swal("Erro!", "As senhas não conferem", "error");
        } else if($('#senha_01').val() == "" ||  $('#senha_02').val() == ""){
            swal("Erro!", "Digite duas senhas identicas", "error");
        } else {
            $('#redefinir_senha').submit();
        }
    });
});
</script>