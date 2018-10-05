var veiculoAutorizacaoModulo = (function ($) {

    var p;

    return p = {

        autorizar : function (idVeiculo, s, e) {
            $.ajax({
                url : '/api/v1/veiculo/' + idVeiculo + '/autorizar',
                dataType : 'json',
                type : 'patch',
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        }
    };


})(jQuery);
