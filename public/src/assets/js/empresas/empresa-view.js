var empresaView = (function ($, genericIzitoastMenssage, genericErrorHandlerModulo) {

    var p;
    var $this;
    var $form = $("#form_empresa");

    return p = {
        init : function () {
            $this = this;
        },
        extractEmpresaView : function () {
            return {
                id : $("#id_empresa").val(),
                email : $("#emp_email").val(),
                documento : $("#emp_documento").val(),
                razao_social : $("#emp_razao_social").val(),
                nome : $("#emp_nome").val(),
            };
        },
        buttonLoading : function (btn) {
            btn.button('loading');
        },
        buttonReset : function (btn) {
            btn.button('reset');
        },
        saveEmpresaSucesso : function (data) {
            genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                window.location = "/admin/empresa/form/busca";
            });
        },
        saveEmpresaErro : function (error) {
            genericErrorHandlerModulo.clearError($form);
            genericErrorHandlerModulo.setError(error, $form, '#emp_');
            genericIzitoastMenssage.showErrorMenssage(error);
        }
    };
})(jQuery, genericIzitoastMenssage, genericErrorHandlerModulo);