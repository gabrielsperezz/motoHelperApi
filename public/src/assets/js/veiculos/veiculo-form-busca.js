(function ($, veiculoModulo,genericIzitoastMenssage) {

    $(document).ready(function () {

        var veiculoTable = $("#table_veiculos").DataTable({
            processing : true,
            paging : true,
            lengthChange : true,
            searching : true,
            ordering : false,
            info : true,
            responsive : true,
            autoWidth : false,
            scrollCollapse : true,
            language : language_datatable
        });

        $(".editar").click(function (e) {
            var id = $(this).data('id-veiculo');
            window.location = "/veiculo/form/"+id;
        });

        $(".remover").click(function (e) {
            var id = $(this).data('id-veiculo');
            veiculoModulo.deleteveiculo(id,function (data) {
                genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                    window.location = "/veiculo/form/busca";
                });
            },function (error) {
                genericIzitoastMenssage.showErrorMenssage(error);
            })
        });



    });

})(jQuery, veiculoModulo,genericIzitoastMenssage);