function submitRegistrationForm() {
    var name = jQuery("#frm-register-participant input[name='name']");
    var surname = jQuery("#frm-register-participant input[name='surname']");
    var patronymic = jQuery("#frm-register-participant input[name='patronymic']");
    var email = jQuery("#frm-register-participant input[name='email']");
    var inn = jQuery("#frm-register-participant input[name='inn']");
    var company_name = jQuery("#frm-register-participant input[name='company_name']");
    var number = jQuery("#frm-register-participant input[name='number']");
    var webinar_title = jQuery("#frm-register-participant input[name='webinar_title']");
    var webinar_date = jQuery("#frm-register-participant input[name='webinar_date']");
    var webinar_link = jQuery("#frm-register-participant input[name='webinar_link']");
    var btnSubmit_verification_code = jQuery("#frm-register-participant .btnSubmit-verification-code");
    var verification_code = jQuery("#frm-register-participant .form-input-verification-code");
    var formcomplete = true;
    number.css("border", "");
    name.css("border", "");
    email.css("border", "	");
	verification_code.css("border", "	");
    if (number.val() == "") {
        number.css("border", "2px red solid");
        formcomplete = false;
        number.find("~.error-description").html("Номер не может быть пустым")
        number.find("~.error-description").show(100);
    } else if (!number.inputmask("isComplete")) {
        number.css("border", "2px red solid");
        formcomplete = false;
        number.find("~.error-description").show(100);
    }
    if (name.val() == "") {
        name.css("border", "2px red solid");
        name.find("~.error-description").show(100);
        formcomplete = false;
    }
    if (surname.val() == "") {
        surname.css("border", "2px red solid");
        surname.find("~.error-description").show(100);
        formcomplete = false;
    }
    if (!email.inputmask("isComplete")) {
        email.find("~.error-description").show(100);
        email.css("border", "2px red solid");
        formcomplete = false;
    }
    if ((!inn.inputmask("isComplete")) && (inn.next().hasClass("error-description"))) {
        inn.find("~.error-description").show(100);
        inn.css("border", "2px red solid");
        formcomplete = false;
    }
    if ((company_name.val() == "") && (company_name.next().hasClass("error-description"))) {
        company_name.find("~.error-description").show(100);
        company_name.css("border", "2px red solid");
        formcomplete = false;
    }
    if (btnSubmit_verification_code.css("background-color") !== "rgb(0, 128, 0)") {
        verification_code.css("border", "2px red solid");
        verification_code.find("~.error-description").show(100);
        // number.css("border", "2px red solid");
        formcomplete = false;
    } else if (jQuery(".form-row-verification-code").css('display') !== 'flex'){
        number.css("border", "2px red solid");
        number.find("~.error-description").show(100);
        number.find("~.error-description").html("Подтвердите свой номер телефона");
    }

    if (formcomplete == true) {
        debugger;
        var input = {
            "mobile_number": number.val(),
            "name": name.val(),
            "surname": surname.val(),
            "patronymic": patronymic.val(),
            "email": email.val(),
            "inn": inn.val(),
            "company_name": company_name.val(),
            "webinar_title": webinar_title.val(),
            "webinar_date": webinar_date.val(),
            "webinar_link": webinar_link.val(),
            "action": "submitRegistrationForm"
        };
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: input,
            success: function (response) {
                var form = jQuery("#frm-register-participant");
                var height = form.outerHeight();
                form.css('min-height',height);
                form.css('display','flex');
                form.css('flex-direction','column');
                form.css('align-items','center');
                form.children('div').eq(1).hide();
                form.children('div').eq(2).show();
            }
        });
    }
}

function sendOTP() {
    var number = jQuery("#frm-register-participant input[name='number']");
    //TODO: check number
    if (number.inputmask("isComplete")) {
        number.css("border", "");
        var input = {
            "mobile_number": number.val(),
            "action": "send_otp"
        };
        //TODO: Loading animation
        jQuery(".btnSubmit-phone").prop('value', 'Отправка...');
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: input,
            success: function (response) {
                var $data = jQuery.parseJSON(response);
                if ($data.success == true) {
                    if ($data.otp > 0) {
                        alert('На телефон отправлен код ' + $data.otp);
                    }
                    var btn = jQuery(".btnSubmit-phone");
                    var phone = jQuery("#frm-register-participant-number");
                    phone.prop("disabled", true);
                    btn.prop("disabled", true);
                    btn.css("background-color", "green");
                    btn.prop('value', 'Отправлено');
                    var waitinterval = 60;
                    var count = waitinterval;     // Set timer to repeat sending

                    var timer = null;
                    (function countDown() {
                        // Display counter on button and start counting down
                        if (count !== 0) {
                            timer = setTimeout(countDown, 1000);
                            count--; // decrease the timer
                            if (waitinterval - count >= 4) {
                                btn.css("background-color", "gray");
                                btn.prop('value', 'Повторить(' + count + ')');
                            }
                        } else {
                            // Enable the button
                            btn.prop("disabled", false);
                            btn.css("background-color", "#1a1a1a");
                            btn.prop('value', 'Отправить код');
                            phone.prop("disabled", false);

                        }
                    }());
                    jQuery(".form-row-verification-code").css('display', 'flex');
                }
                else if ($data.success == false) {
                    var btn = jQuery(".btnSubmit-phone");
                    btn.css("background-color", "red");
                    btn.prop('value', 'Ошибка');
                    setTimeout(() => {  btn.css("background-color", "");  btn.prop('value', 'Отправить'); }, 2000);



                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        });
    } else {
        number.css("border", "2px red solid");
        number.find("~.error-description").show(100);
    }
}

function verifyOTP() {
    var otp = jQuery("#frm-register-verification-code").val();
    var btnSubmitNumber = jQuery(".btnSubmit-phone");
    var number = jQuery("#frm-register-participant input[name='number']");
    var btn = jQuery(".btnSubmit-verification-code");
    var input = {
        "otp": otp,
        "action": "verify_otp"
    };
    if (otp.length == 6 && otp !== null) {
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            dataType: "json",
            data: input,
             success: function (response) {
                if (response.success == true) {
                    btn.prop("disabled", true);
                    btn.css("background-color", "green");
                    btn.prop('value', 'Подтверждено');
				 	number.prop("disabled", true);
                } else {
                	btnSubmitNumber.show();
                    number.parent().css('width','60%');
                    btnSubmitNumber.parent().css('width','40%');
                    number.prop('disabled', false);
                    btn.css("background-color", "#ef1716");
                    btn.prop('value', 'Код неверен');
                    btn.prop("disabled", false);
                    // timer(function( btn.prop('value', '');btn.css("background-color", "");),1000);
				}
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError);
            }
        });
    } else {
        alert("Код неверен");
    }
}