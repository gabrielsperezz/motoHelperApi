var novoLoginView = (function ($, genericIzitoastMenssage, genericErrorHandlerModulo) {

    var p;
    var $this;
    var $form = $("#form_novo_login");

    return p = {
        init: function () {
            $this = this;
        },
        extractAlunoView: function () {
            return {
                usuario: $("#new_user_usuario").val(),
                email: $("#new_user_email").val(),
                password: $("#new_user_password").val(),
                descricao: $("#new_user_descricao").val(),
                ra: $("#new_user_ra").val()
            };
        },
        buttonLoading: function (btn) {
            btn.button('loading');
        },
        buttonReset: function (btn) {
            btn.button('reset');
        },
        saveAlunoSucesso: function () {
            window.location = "/home";
        },
        saveAlunoErro: function (error) {
            genericErrorHandlerModulo.setError(error, $form, '#new_user_');
            genericIzitoastMenssage.showErrorMenssage(error);

        }
    };
})(jQuery, genericIzitoastMenssage, genericErrorHandlerModulo);