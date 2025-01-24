$(document).ready(function () {
    // Función para calcular la posición final del formulario
    function positionForm() {
        var positionFinal = $(window).width() - ($('#register_form').outerWidth() + 30);
        $('#register_form').css('left', positionFinal + 'px');
    }

    // Animación inicial
    $('#register_form').animate({
        left: $(window).width() - ($('#register_form').outerWidth() + 30) + 'px'
    }, 1000);

    // Recalcula y ajusta la posición en tiempo real al redimensionar la ventana
    $(window).on('resize', function () {
        positionForm();
    });
});
