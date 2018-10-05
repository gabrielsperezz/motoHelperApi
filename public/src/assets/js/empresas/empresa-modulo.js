var empresaModulo = (function ($) {

    var p;

    return p = {
        save : function (empresa, s, e) {
            if (empresa.id > 0) {
                this.update(empresa, s, e);
            } else {
                this.insert(empresa, s, e);
            }
        },
        insert : function (empresa, s, e) {
            $.ajax({
                url : '/api/v1/empresa',
                dataType : 'json',
                type : 'post',
                data : empresa,
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        update : function (empresa, s, e) {
            $.ajax({
                url : '/api/v1/empresa/' + empresa.id,
                dataType : 'json',
                type : 'put',
                data : empresa,
                error : function (error) {
                    e(error);
                },
                success : function (data) {
                    s(data);
                }
            });
        },
        deleteempresa : function (id, s, e) {
            $.ajax({
                url : '/api/v1/empresa/' + id,
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
