var veiculoView = (function ($, genericIzitoastMenssage, genericErrorHandlerModulo) {

    var p;
    var $this;
    var $form = $("#form_veiculo");

    return p = {
        init : function () {
            $this = this;
        },
        extractVeiculoView : function () {
            return {
                id : $("#id_veiculo").val(),
                descricao : $("#vei_descricao").val(),
                placa : $("#vei_placa").val(),
                cor : $("#vei_cor").val(),
                modelo : $("#vei_modelo").val(),
                fabricante : $("#vei_fabricante").val()
            };
        },
        buttonLoading : function (btn) {
            btn.button('loading');
        },
        buttonReset : function (btn) {
            btn.button('reset');
        },
        saveVeiculoSucesso : function (data) {
            genericErrorHandlerModulo.clearError($form);
            genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                window.location = "/veiculo/form/busca";
            });
        },
        saveVeiculoErro : function (error) {
            genericErrorHandlerModulo.clearError($form);
            genericErrorHandlerModulo.setError(error, $form, '#vei_');
            genericIzitoastMenssage.showErrorMenssage(error);
        }
    };
})(jQuery, genericIzitoastMenssage, genericErrorHandlerModulo);