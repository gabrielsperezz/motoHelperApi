(function ($, empresaModulo,genericIzitoastMenssage) {

    $(document).ready(function () {

        var empresaTable = $("#table_empresas").DataTable({
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
            var id = $(this).data('id-empresa');
            window.location = "/admin/empresa/form/"+id;
        });

        $(".remover").click(function (e) {
            var id = $(this).data('id-empresa');
            empresaModulo.deleteempresa(id,function (data) {
                genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                    window.location = "/admin/empresa/form/busca";
                });
            },function (error) {
                genericIzitoastMenssage.showErrorMenssage(error);
            })
        });



    });

})(jQuery, empresaModulo,genericIzitoastMenssage);