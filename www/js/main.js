jQuery(document).ready( function(){
    $('.carousel').carousel({
        interval: 5000,
        spead: 5000
    });
    $('#registrationFormModal').modal;
    $('#enterFrom').modal;
    $('#registrationForm').validate({
        rules: {
            password: "required",
            password_again: {
                equalTo: "#password"
            }
        }
    });
});