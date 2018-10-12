(function ($, motoboyModulo,genericIzitoastMenssage) {

    $(document).ready(function () {

        var motoboyTable = $("#table_motoboys").DataTable({
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
            var id = $(this).data('id-motoboy');
            window.location = "/motoboy/form/"+id;
        });

        $(".remover").click(function (e) {
            var id = $(this).data('id-motoboy');
            motoboyModulo.deletemotoboy(id,function (data) {
                genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                    window.location = "/motoboy/form/busca";
                });
            },function (error) {
                genericIzitoastMenssage.showErrorMenssage(error);
            })
        });



    });

})(jQuery, motoboyModulo,genericIzitoastMenssage);