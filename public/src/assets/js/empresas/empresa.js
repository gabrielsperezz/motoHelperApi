(function ($, empresaView, empresaModulo) {

    $(document).ready(function () {

        empresaView.init();

        $("#salvar").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = $(this);
            var Empresa = empresaView.extractEmpresaView();

            empresaView.buttonLoading(btn);
            empresaModulo.save(Empresa, function (data) {

                empresaView.buttonReset(btn);
                empresaView.saveEmpresaSucesso(data);

            }, function (error) {

                empresaView.buttonReset(btn);
                empresaView.saveEmpresaErro(error);

            });

        });

        $("#cancelar").click(function (e) {
            e.preventDefault();
            e.stopPropagation()
            window.location = "/admin/empresa/form/busca";
        });


    });

})(jQuery, empresaView, empresaModulo);