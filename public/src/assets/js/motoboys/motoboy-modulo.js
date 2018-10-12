var motoboyModulo = (function ($) {

    var p;

    return p = {
        save : function (motoboy, s, e) {
            if (motoboy.id > 0) {
                this.update(motoboy, s, e);
            } else {
                this.insert(motoboy, s, e);
            }
        },
        insert : function (motoboy, s, e) {
            $.ajax({
                url : '/api/v1/motoboy',
                dataType : 'json',
                type : 'post',
                data : motoboy,
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        insertMotoboyVeiculo : function (idVeiculo, idMotoboy, s, e) {
            $.ajax({
                url : '/api/v1/veiculo/'+ idVeiculo + '/motoboy/'+idMotoboy,
                dataType : 'json',
                type : 'post',
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        deleteVeiculoMotoboy : function (idVeiculoMotoboy, s, e) {
            $.ajax({
                url : '/api/v1/veiculo/motoboy/'+ idVeiculoMotoboy,
                dataType : 'json',
                type : 'delete',
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        update : function (motoboy, s, e) {
            $.ajax({
                url : '/api/v1/motoboy/' + motoboy.id,
                dataType : 'json',
                type : 'put',
                data : motoboy,
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        deletemotoboy : function (id, s, e) {
            $.ajax({
                url : '/api/v1/motoboy/' + id,
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
