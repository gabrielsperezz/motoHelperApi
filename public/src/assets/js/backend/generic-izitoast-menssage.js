var genericIzitoastMenssage = (function ($) {

    var p;

    var mapErrorMsg = function (error, templateD) {

        var template = templateD || ' %value%. <br>';
        var json = error.responseJSON || {},
                erros = json.errors || json.erros || json.error || json.erro || json || [];

        erros = $.isArray(erros) ? erros : (($.type(erros) == 'string') ? [erros] : erros);
        var strErros = '';

        $.each(erros, function (index, value) {

            if ((value || '').length > 0) {
                var m = '';
                m = template.replace('%value%', value);
                m = m.replace('%index%', index);

                strErros += m;
            }
        });
        return strErros;
    };

    return p = {
        showErrorMenssage : function (menssage) {

            menssage = mapErrorMsg(menssage);

            iziToast.destroy();
            iziToast.error({
                messageColor : 'rgba(0,0,0,.7)',
                messageSize : '16',
                message : menssage,
                position : "topCenter",
                timeout : 10000,
            });

        },
        showSuccessMenssage : function (menssage) {
            iziToast.destroy();
            iziToast.success({
                title : sucesso,
                message : menssage,
                position : "topCenter",
                timeout : 3500
            });

        },
        showConfirmMenssage : function (menssage, cb) {

            iziToast.destroy();
            iziToast.success({
                title : sucesso,
                message : menssage,
                timeout : 3500,
                position : 'topCenter',  
                buttons : [
                    ['<button>OK</button>', function (instance, toast) {
                            instance.hide({
                                transitionOut : 'fadeOutUp',
                                onClose : function (instance, toast, closedBy) {
                                    if (cb instanceof Function) {
                                        cb();
                                    }
                                }
                            }, toast, 'close');
                        }]
                ],
                onclose : function () {
                    if (cb instanceof Function) {
                        cb();
                    }
                }
            });
            $(document).on('iziToast-close', function () {
                if (cb instanceof Function) {
                    cb();
                }
            });
        }
    };


})(jQuery);