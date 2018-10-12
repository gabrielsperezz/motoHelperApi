var veiculoModulo = (function ($) {

    var p;

    return p = {
        save : function (veiculo, s, e) {
            if (veiculo.id > 0) {
                this.update(veiculo, s, e);
            } else {
                this.insert(veiculo, s, e);
            }
        },
        insert : function (veiculo, s, e) {
            $.ajax({
                url : '/api/v1/veiculo',
                dataType : 'json',
                type : 'post',
                data : veiculo,
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        update : function (veiculo, s, e) {
            $.ajax({
                url : '/api/v1/veiculo/' + veiculo.id,
                dataType : 'json',
                type : 'put',
                data : veiculo,
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        deleteveiculo : function (id, s, e) {
            $.ajax({
                url : '/api/v1/veiculo/' + id,
                dataType : 'json',
                type : 'delete',
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
