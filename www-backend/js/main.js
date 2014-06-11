$(document).ready(function()
{
    ///////////////////////////////////////////////////////////////////////

    $('#profiler span.profiler-sql-show').on(
        'click',
        function(e)
        {
            e.preventDefault()

            if( $('#profiler-sql').is( ':visible' ) )
            {
                $('#profiler-sql').hide();
            }
            else
            {
                $('#profiler-sql').show();
            }
        }
    );

    ///////////////////////////////////////////////////////////////////////

    $('#admin_login').validate({
        rules: {
            email: {
                required: true,
                minlength: 3,
                maxlength: 128,
                email: true
            },
            passwd: {
                required: true,
                minlength: 3,
                maxlength: 128
            }
        },
        messages: {
            email: {
                required: "Пожалуйста, введите email",
                minlength: "Email должен содержать как минимум 3 символа",
                maxlength: "Длина email больше максимальной",
                email: "Пожалуйста, введите валидный email"
            },
            passwd: {
                required: "Пожалуйста, введите пароль",
                minlength: "Пароль должен содержать как минимум 3 символа",
                maxlength: "Длина пароля больше максимальной"
            }
        }
    });

    ///////////////////////////////////////////////////////////////////////

})