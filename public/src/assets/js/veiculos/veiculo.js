(function ($, veiculoView, veiculoModulo) {

    $(document).ready(function () {

        veiculoView.init();

        $("#salvar").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = $(this);
            var Veiculo = veiculoView.extractVeiculoView();

            veiculoView.buttonLoading(btn);
            veiculoModulo.save(Veiculo, function (data) {

                veiculoView.buttonReset(btn);
                veiculoView.saveVeiculoSucesso(data);

            }, function (error) {

                veiculoView.buttonReset(btn);
                veiculoView.saveVeiculoErro(error);

            });

        });

        $("#cancelar").click(function (e) {
            e.preventDefault();
            e.stopPropagation()
            window.location = "/veiculo/form/busca";
        });


    });

})(jQuery, veiculoView, veiculoModulo);