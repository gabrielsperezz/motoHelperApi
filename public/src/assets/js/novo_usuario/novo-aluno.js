(function ($, novoLoginView, novoLoginModulo) {

    $(document).ready(function () {

        novoLoginView.init();

        $("#btn_criar_novo_login").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = $(this);
            var aluno = novoLoginView.extractAlunoView();

            novoLoginView.buttonLoading(btn);
            novoLoginModulo.insert(aluno, function (data) {

                novoLoginView.buttonReset(btn);
                novoLoginView.saveAlunoSucesso(data);

            }, function (error) {

                novoLoginView.buttonReset(btn);
                novoLoginView.saveAlunoErro(error);

            });

        });

    });

})(jQuery, novoLoginView, novoLoginModulo);