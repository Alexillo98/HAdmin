$(document).ready(function (){
    function initCalendar(eventos){
        let eventosPrueba = [];
        Array.from(eventos).forEach((evento)=>{
            eventosPrueba.push({
                startDate: new Date(evento.fecha),
                endDate: new Date(evento.fecha),
                summary: evento.descripcion
            })
        })

        $("#calendar").simpleCalendar({
        });
        let calendar = $("#calendar").data("plugin_simpleCalendar");

        let event = {
            startDate: eventos.startDate,
            endDate: eventos.endDate,
            summary: eventos.summary
        }
        calendar.addEvent(event);

    }

    loadEvents();
    function loadEvents(){
        $.ajax({
            url: "/getCitas",
            method: "GET",
            dataType : "json",
            success: function (response){
                initCalendar(response);

            },
            error: function (err){
                console.error("Error cargando /getEventos", err);
            }
        })
    }

    $(document).on("click", ".day", function() {
        let selectedDate = $(this).data("date");
        let formattedDate = dayjs(selectedDate).format('YYYY-MM-DD');
        let nuevaFecha = new Date(formattedDate);
        $("#inputFecha").val(formattedDate);
        $("#modalAddEvent").show();

        let descripcion = $("#inputDescripcion").val();
        let newEvent = {
            startDate: nuevaFecha,
            endDate: nuevaFecha,
            summary: descripcion
        }

        calendar.addEvent(newEvent);
    });

    $("#btnCloseModal").on("click", function() {
        $("#modalAddEvent").hide();
    });

    $(window).on("click", function(e) {
        if (e.target.id === "modalAddEvent") {
            $("#modalAddEvent").hide();
        }
    });
})
