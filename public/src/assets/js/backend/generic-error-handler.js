var genericErrorHandlerModulo = (function ($) {
    
    var p;
    
    return p = {
        throwModal:function(text){
            $('#msg-sistema-modal .modal-body').html("<p>" + text + "</p>");
            $('#msg-sistema-modal').modal('show');
        },
        throwErrorModal:function(error){
            switch (error.status){
                    case 0 /* offline */:
                        this.throwModal(msg_offline);
                    break;
                    
                    case 401 /* Unauthorized */:
                        $('#config-relogin-msg-modal').modal('show');
                    break;
                    
                    default:
                        var erros = this.mapErrorMsg(error);
                        if(erros.length > 0){
                            this.throwModal(erros);
                        }else {
                          this.throwModal(msg_500);
                        }
                    break;
            }
        },
        setError:function(error, form_id, prefix){
            var _prefix = prefix || '';
            var json  = error.responseJSON || {},
                        erros = json.errors || json.erros || json.error || json.erro || json || [];

            $.each(erros, function( index, value ){
                var input = ($.type(form_id) == "string") ? $(form_id + ' ' + _prefix + index) : form_id.find(_prefix + index);

                if(input.length){

                    var parent = input.parents('.form-group'),
                        label  = parent.children('.label-error');

                        parent.addClass('has-error');

                        if(label.length){
                            label.html('<i class="fa fa-times-circle-o"></i> ' + value );
                        }else{
                            parent.append('<label class="label-error control-label" for="' + index + '"><i class="fa fa-times-circle-o"></i> ' + value + '</label>');
                        }
                }
            });
                
        },
        clearError:function(form){
            
            form.find('.form-group.has-error').each(function(el){
                var div = $(this);
                div.removeClass('has-error');
                div.children('.label-error').remove();
            
            });
        },
        mapErrorMsg:function(error, templateD){
              
            var template = templateD || '<p>%value%</p>';
            var json  = error.responseJSON || {},
                        erros = json.errors || json.erros || json.error || json.erro || json || [];
                  
            erros = $.isArray(erros) ? erros : (($.type(erros) == 'string') ? [erros] : erros);
            var strErros = '';
             
            $.each(erros, function( index, value ){
                  
                if((value|| '').length > 0){
                    var m = '';
                    m = template.replace('%value%',value);
                    m = m.replace('%index%', index);
                    
                    strErros += m;
                }
            });
             
            return strErros;
        }
    };
    
    
})(jQuery);

