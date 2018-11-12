var motoboyView = (function ($, genericIzitoastMenssage, genericErrorHandlerModulo) {

    var p;
    var $this;
    var $form = $("#form_motoboy");
    var table = $("#table_motoboy_veiculos");
    var $tableInstance;

    return p = {
        init : function () {
            $this = this;
            $this.initTableMotoboy();
        },
        initTableMotoboy : function(){
            $tableInstance = table.DataTable({
                processing: true,
                paging: true,
                lengthChange: true,
                serverSide: false,
                searching: true,
                ordering: false,
                info: true,
                responsive: true,
                autoWidth: false,
                scrollCollapse: true,
                language: language_datatable,
                ajax: "/api/v1/motoboy/"+idMotoboy+"/veiculos",
                sDom: '<"row" <"col col-md-6 text-left" i> <"col col-md-6 text-right" l>> <"row" <"col col-md-12" tr>> <"row" <"col col-md-12 text-right" p>>',
                "fnCreatedRow": function (nRow, aData, iDataIndex) {
                    $(nRow).attr('id', aData[0]);
                }
            });
        },
        extractMotoboyView : function () {
            return {
                id : $("#id_motoboy").val(),
                nome : $("#motoboy_nome").val(),
                login : $("#motoboy_login").val(),
                email : $("#motoboy_email").val(),
                senha : $("#motoboy_senha").val(),
                atualizar_senha: $("#motoboy_atualizar_senha").is(':checked') ? 1 : 0
            };
        },
        buttonLoading : function (btn) {
            btn.button('loading');
        },
        buttonReset : function (btn) {
            btn.button('reset');
        },
        saveMotoboySucesso : function (data) {
            genericErrorHandlerModulo.clearError($form);
            genericIzitoastMenssage.showConfirmMenssage(data.msg,function () {
                window.location = "/motoboy/form/busca";
            });
        },
        saveMotoboyErro : function (error) {
            genericErrorHandlerModulo.clearError($form);
            genericErrorHandlerModulo.setError(error, $form, '#motoboy_');
            genericIzitoastMenssage.showErrorMenssage(error);
        },
        saveMotoboyVeiculoSucesso : function (data) {
            $tableInstance.ajax.reload();
            genericIzitoastMenssage.showSuccessMenssage(data.msg);
        },
        saveMotoboyVeiculoErro : function (error) {
            genericIzitoastMenssage.showErrorMenssage("Veiculo inválido");
        }
    };
})(jQuery, genericIzitoastMenssage, genericErrorHandlerModulo);