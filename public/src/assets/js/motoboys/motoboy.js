(function ($, motoboyView, motoboyModulo) {

    $(document).ready(function () {

        motoboyView.init();

        $("#salvar").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = $(this);
            var Motoboy = motoboyView.extractMotoboyView();

            motoboyView.buttonLoading(btn);
            motoboyModulo.save(Motoboy, function (data) {

                motoboyView.buttonReset(btn);
                motoboyView.saveMotoboySucesso(data);

            }, function (error) {

                motoboyView.buttonReset(btn);
                motoboyView.saveMotoboyErro(error);

            });

        });

        $("#salvar_veiculo").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = $(this);
            var idMotoboy = $("#id_motoboy").val();
            var idVeiculo = $("#motoboy_veiculo").val();

            motoboyView.buttonLoading(btn);
            motoboyModulo.insertMotoboyVeiculo( idVeiculo,idMotoboy,function (data) {

                motoboyView.buttonReset(btn);
                motoboyView.saveMotoboySucesso(data);

            }, function (error) {

                motoboyView.buttonReset(btn);
                motoboyView.saveMotoboyErro(error);

            });

        });

        $("#cancelar").click(function (e) {
            e.preventDefault();
            e.stopPropagation()
            window.location = "/motoboy/form/busca";
        });


    });

})(jQuery, motoboyView, motoboyModulo);