$(document).ready(function () {
    // Variable "global" dentro de este scope para poder reusar el plugin
    let calendar;
    let eventos;
    function initCalendar(eventos) {
        // Mapeamos el array de eventos del back a un array de objetos que el plugin pueda interpretar
        let events = eventos.map(evento => {
            return {
                // Creamos objetos Date a partir del string devuelto por el back
                startDate: evento.startDate,
                endDate: evento.endDate,
                summary: evento.summary,

                dbId: evento.id,
                name: evento.name
            };
        });

        // Inicializamos el calendario
        $("#calendar").simpleCalendar({
        });

        // Recuperamos la instancia para luego poder añadir/quitar eventos
        calendar = $("#calendar").data("plugin_simpleCalendar");

        // Añadimos cada evento al calendario
        events.forEach(ev => {
            calendar.addEvent(ev);
        });
    }

    // Función para cargar eventos desde el backend
    function loadEvents() {
        $.ajax({
            url: "/getCitas",
            method: "GET",
            dataType: "json",
            success: function (response) {
                // 'response' debe ser un array de eventos
                eventos = response;
                initCalendar(response);
            },
            error: function (err) {
                console.error("Error cargando /getCitas", err);
            }
        });
    }
    // Llamamos a la carga inicial de eventos
    loadEvents();

    // Al hacer clic en un día del calendario, abrimos el modal
    $(document).on("click", ".day", function () {
        let selectedDate = $(this).data("date"); // devuelto por el plugin simpleCalendar
        let formattedDate = dayjs(selectedDate).format('YYYY-MM-DD');

        // rellenamos un input con la fecha seleccionada (si tienes un formulario en el modal)
        $("#inputFecha").val(formattedDate);

        // Mostramos el modal (lógica muy simple; ajusta a tus necesidades)
        $("#modalAddEvent").show();

        addDeleteButon(eventos);
    });

    // Cuando cierras el modal, lo ocultas
    $("#btnCloseModal").on("click", function() {
        $("#modalAddEvent").hide();
    });

    // Ejemplo de click en "Guardar cita" dentro del modal
    $("#btnGuardarEvento").on("click", function() {
        // Obtenemos la fecha y la descripción
        let fecha = $("#inputFecha").val();
        let nombre = $("#inputNombre").val();
        let descripcion = $("#inputDescripcion").val();

        // Aquí podrías añadir la nueva cita directamente al calendario:
        let nuevaFecha = new Date(fecha);
        nuevaFecha.setHours($('#hourSelect').val());
        nuevaFecha.setMinutes($('#minuteSelect').val());
        let newEvent = {
            startDate: nuevaFecha,
            endDate: nuevaFecha,
            summary: descripcion
        };
        calendar.addEvent(newEvent);

        // Pero, si realmente quieres GUARDAR en la BD, llama a tu endpoint:
        $.ajax({
            url: "/addCita",
            method: "POST",
            data: {
                fecha: nuevaFecha.toISOString(),
                nombre: nombre,
                descripcion: descripcion
            },
            dataType: "json",
            success: function(response) {
                location.reload();
                // refrescar el calendario completo o solo añadir el nuevo event
                //loadEvents(); // si quieres recargar todo
            },
            error: function(err) {
                console.error("Error al guardar la cita", err);
            }
        });

        // Cerramos el modal
        $("#modalAddEvent").hide();
    });

    // Para cerrar el modal al hacer clic fuera
    $(window).on("click", function(e) {
        if (e.target.id === "modalAddEvent") {
            $("#modalAddEvent").hide();
        }
    });

    function addDeleteButon(events){
        let $eventDivs = $(".event");
        let limit = events.length;
        let contador = 0;

        $eventDivs.each(function (index){
            if (contador >= limit){
                return;
            }
            let evData = events[index];

            $(this).attr("data-db-id",$(this).find(".event-dbid").text());

            $(this).append(
                "<button class='btnDeleteEvent' style='float: right;\n" +
                "  position: absolute;\n" +
                "  z-index: 1000;\n" +
                "  top: 56px;\n" +
                "  width: 30px;\n" +
                "  height: 30px;\n" +
                "  right: 0px;'>" +
                "X</button>"
            )
            contador++;
        })
    }

    $("body").on("click",".btnDeleteEvent",function (){
        let id = $(this).parent().attr("data-db-id");

        let parentEvent = $(this).closest(".event");
        console.log(parentEvent);
        const currentWidth = parentEvent.width();

        parentEvent.css("width", currentWidth);
        $.ajax({
            type: "POST",
            url: "/deleteCita",
            data: {
                id: id
            },
            success: function (){
                parentEvent.animate({
                    width: 0
                },500, function (){
                    parentEvent.remove();
                    location.reload();
                })
            }
        })
    })

    // Referenciamos los select del DOM
    const hourSelect = document.getElementById("hourSelect");
    const minuteSelect = document.getElementById("minuteSelect");

    // Generar opciones de horas (0 a 23)
    for (let h = 0; h < 24; h++) {
        const hourString = h.toString().padStart(2, "0"); // Para mostrar "00", "01", "02", etc.
        const option = document.createElement("option");
        option.value = hourString;
        option.text  = hourString;
        hourSelect.appendChild(option);
    }

    // Generar opciones de minutos (0 a 59)
    for (let m = 0; m < 60; m++) {
        const minuteString = m.toString().padStart(2, "0"); // Para mostrar "00", "01", "02", etc.
        const option = document.createElement("option");
        option.value = minuteString;
        option.text  = minuteString;
        minuteSelect.appendChild(option);
    }
});