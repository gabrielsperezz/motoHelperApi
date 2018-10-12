(function ($, genericIzitoastMenssage) {

    $("#login").focus();

    $('#login-msg-modal').on('shown.bs.modal', function () {
        $('#bt-modal-ok').focus();
    });

    $('#login-msg-modal').on('hidden.bs.modal', function () {
        $("#login").focus();
    });

    $('#btn_login').click(function (event) {

        var $login = $('#username'),
            $password = $('#password');
        $login.siblings('label').remove();
        $password.siblings('label').remove();

        $.ajax({
            url: '/login',
            dataType: 'json',
            type: 'post',
            data: {
                username: $login.val(),
                password: $password.val()
            },
            error: function (error) {

                switch (error.status) {
                    case 400 /* Bad Request */
                    :

                        var json = error.responseJSON,
                            erros = json.erros;

                        $.each(erros, function (index, value) {
                            var input = $('#' + index);

                            if (input.length) {

                                var parent = input.parent(),
                                    label = input.siblings('label');

                                parent.addClass('has-error');

                                if (label.length) {
                                    label.html('<i class="fa fa-times-circle-o"></i> ' + value);
                                } else {
                                    parent.append('<label class="control-label" for="' + index + '"><i class="fa fa-times-circle-o"></i> ' + value + '</label>');
                                }
                            }
                        });

                        break;
                    case 401 /* Unauthorized */:
                        genericIzitoastMenssage.showErrorMenssage(error);
                        break;
                }
            },
            success: function (data) {
                window.location = "/home";
            }
        });

        event.preventDefault();

    });

})(jQuery,genericIzitoastMenssage);


