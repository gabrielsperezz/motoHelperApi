var novoLoginModulo = (function ($) {

    var p;

    return p = {
        insert : function (aluno, s, e) {
            $.ajax({
                url : '/aluno',
                dataType : 'json',
                type : 'post',
                data : aluno,
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
