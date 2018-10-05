(function ($, veiculoAutorizacaoModulo) {

    $(document).ready(function () {

        $(".autorizar").click(function (e) {
            var idVeiculo = $(this).data('id-veiculo');
            window.location = "/admin/veiculo/"+idVeiculo+"/verificao";
        });

        $("#autorizar").click(function (e) {
            var idVeiculo = $(this).data('id-veiculo');
            veiculoAutorizacaoModulo.autorizar(idVeiculo, function (data) {
                genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                    window.location = "/admin/veiculo/verificao";
                });
            },function (error) {
                genericIzitoastMenssage.showErrorMenssage(error);
            });
        });


    });

})(jQuery, veiculoAutorizacaoModulo);