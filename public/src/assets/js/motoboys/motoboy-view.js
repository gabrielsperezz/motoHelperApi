var motoboyView = (function ($, genericIzitoastMenssage, genericErrorHandlerModulo) {

    var p;
    var $this;
    var $form = $("#form_motoboy");

    return p = {
        init : function () {
            $this = this;
        },
        extractMotoboyView : function () {
            return {
                id : $("#id_motoboy").val(),
                nome : $("#motoboy_nome").val(),
                login : $("#motoboy_login").val(),
                email : $("#motoboy_email").val(),
                senha : $("#motoboy_senha").val(),
                atualizar_senha: $("#motoboy_atualizar_senha").is(':checked') ? 1 : 0
            };
        },
        buttonLoading : function (btn) {
            btn.button('loading');
        },
        buttonReset : function (btn) {
            btn.button('reset');
        },
        saveMotoboySucesso : function (data) {
            genericErrorHandlerModulo.clearError($form);
            genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                window.location = "/motoboy/form/busca";
            });
        },
        saveMotoboyErro : function (error) {
            genericErrorHandlerModulo.clearError($form);
            genericErrorHandlerModulo.setError(error, $form, '#motoboy_');
            genericIzitoastMenssage.showErrorMenssage(error);
        },
        saveMotoboySucesso : function (data) {
            genericIzitoastMenssage.showSuccessMenssage(data.msg);
        },
        saveMotoboyVeiculoErro : function (error) {
            genericIzitoastMenssage.showErrorMenssage("Veiculo inválido");
        }
    };
})(jQuery, genericIzitoastMenssage, genericErrorHandlerModulo);