$(document).ready(function () {
    $('#mobile').keyup(function () {
        var mobile = $('#mobile').val();
        $('#to_mobile_number').text("سوف نرسل رسالة نصية تختوي على زمز التوثيق الى "+mobile);
    });

    $('#post_new_user').click(function () {
        if ($('#username').val() == "") {
            $("#edit_info").click();
            return false;
        } 
        if ($('#mobile').val() == "") {
            $("#edit_info").click();
            return false;
        }
        if ($('#gender').val() == "") {
            $("#edit_info").click();
            return false;
        }
        
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '/user/sign-up',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                username:$('#username').val(),
                mobile:$('#mobile').val(),
                gender:$('#gender').val()
            }
        }).done(function(msg) {
            if (msg.status == false) {
                var errors = msg.errors.original.message;
                console.log(errors);
                $("#edit_info").click();
                $.each(errors,function(key,val){
                    if(val.fieldname == "mobile") {
                        $('#mobile-error').text(val.message);
                    }
                    console.log(key+" "+val.message);
                });
            }
        });
    });
});
