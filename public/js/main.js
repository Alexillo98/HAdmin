$(document).ready(function(){
    document.getElementById('registration_form_especialidad').disabled = false;
    $('#registration_form_user_type').on('change',function(){
        if($(this).val() == 'ROLE_PACIENTE'){
            $('#especialidad').hide();
            $('#historia-c').show();
        } else {
            $('#especialidad').show();
            $('#historia-c').hide();
        }
    })

    $('#registration_form_especialidad').val($('#select-especialidad').val())
    $('#registration_form_especialidad').prop('readonly', true);
    $('#select-especialidad').on('change', function() {
        $('#registration_form_especialidad').val($(this).val());
    });

    $.ajax({
        type:"GET",
        url:"/files/especialidades.json",
        success:function (data){
            const select = $("#select-especialidad");
            data.forEach(especialidad =>{
                const option = document.createElement("option");
                option.value = especialidad.name.toString();
                option.textContent = especialidad.name.toString();

                select.append(option)

                console.log(especialidad.name);
            })
        }
    })
})