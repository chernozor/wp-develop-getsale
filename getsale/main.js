/* 02/08/16 19:00*/
jQuery(document).ready(function () {
    jQuery('.form-table th').css('width', '70px');
    jQuery('th').css('padding-bottom', '12px').css('padding-top', '12px');
    jQuery('.form-table td').css('padding', '5px 10px');
    jQuery('input[name=submit_btn]').css('margin-top', '8px').css('margin-bottom', '8px').css('-webkit-appearance', 'button');
    jQuery('#getsale_project_id').parent().parent().hide();
    jQuery('#getsale_reg_error').parent().parent().hide();
    jQuery('input[name=submit_btn]').attr('value', 'Авторизация');

    jQuery('#getsale_api_key, #getsale_email').each(function () {
        if (jQuery(this).val() == '') {
            jQuery('input[name=submit_btn]').attr('disabled', 'disabled');
        }
    });

    jQuery('#getsale_api_key, #getsale_email').keyup(function () {
        var empty = false;
        jQuery('#getsale_api_key, #getsale_email').each(function () {
            if (jQuery(this).val() == '') {
                empty = true;
            }
        });
        if (!empty) {
            jQuery('[name=submit_btn]').removeAttr('disabled');
        } else {
            jQuery('[name=submit_btn]').attr('disabled', 'disabled');
        }
    });
    var app_key_selector = '#getsale_api_key';
    var getsale_images_ok = "<?php echo plugins_url('ok.png', __FILE__);?>";
    var email_selector = '#getsale_email';
    var text_after = "<br><br>Введите Email и Ключ API из личного кабинета GetSale.<br>" +
        "Если вы еще не регистрировались в сервисе GetSale это можно сделать по ссылке <a href='https://getsale.io'>GetSale</a>";
    var support_text = "<p>Служба поддержки: <a href='mailto:support@getsale.io'>support@getsale.io</a></p>" +
        "<p>WordPress GetSale v1.0.0</p>";
    var success_text = "<div class='updated'><p>Поздравляем! Ваш сайт успешно привязан к аккаунту <a href='https://getsale.io'>GetSale</a></p></div>" +
        "Теперь вы можете создать виджеты в личном кабинете на <a href='https://getsale.io'>GetSale</a>.";
    if ((!jQuery('#getsale_reg_error').val()) && (jQuery('#getsale_project_id').val())) {
        window.getsale_succes_reg = true;
    } else {
        window.getsale_reg_error = jQuery('#getsale_reg_error').val();
        window.getsale_succes_reg = false;
    }
    if ((jQuery(app_key_selector).val() !== '') && (jQuery(email_selector).val() !== '')) {
        if (window.getsale_succes_reg == true) {
            jQuery(app_key_selector).after('<img title="Введен правильный Ключ API!" class="gtsl_ok" src="' + getsale_images_ok +'">');
            jQuery(email_selector).after('<img title="Введен правильный Email!" class="gtsl_ok" src="' + getsale_images_ok + '">');
            jQuery(app_key_selector).attr('disabled', 'disabled');
            jQuery(email_selector).attr('disabled', 'disabled');
            jQuery('[name=submit_btn]').before('<br>' + success_text + support_text);
            jQuery('[name=submit_btn]').hide();
        } else if (window.getsale_succes_reg == false) {
            jQuery('input[name=submit_btn]').after(text_after + support_text);
            if (window.getsale_reg_error == 403) {
                var error_text = '<div class="error"><p>Ошибка! Неверно введен Email или Ключ API.</p></div>';
            } else if (window.getsale_reg_error == 500) {
                var error_text = '<div class="error"><p>Ошибка! Данный сайт уже используется на <a href="https://getsale.io">GetSale</a></p></div>';

            } else if (window.getsale_reg_error == 404) {
                var error_text = '<div class="error"><p>Ошибка! Данный Email не зарегистрирован на сайте <a href="https://getsale.io">GetSale</a></p></div>';
            }
            var gtsl_btn_html = '<div style="width:100%;margin-top: 5px;">' +
                '<div style="padding-top: 7px;">' + error_text +
                '</span>' +
                '</div>' +
                '</div>';
            jQuery('input[name=submit_btn]').after(gtsl_btn_html);
            jQuery('input[name=submit_btn]').css('float', 'left');
        }
        else {
            jQuery('input[name=submit_btn]').after(text_after + support_text);
        }
    } else {
        jQuery('input[name=submit_btn]').after(text_after + support_text);
    }
    jQuery(app_key_selector).parent().css('margin-left', '70px');
    jQuery(email_selector).parent().css('margin-left', '70px');
});
var text_after2 = "<p><b>GetSale</b> &mdash; профессиональный инструмент для создания popup-окон.</p>" +
    "<p>GetSale поможет вашему сайту нарастить контактную базу лояльных клиентов, информировать посетителей о предстоящих акциях, распродажах, раздавать промокоды, скидки и многое другое, что напрямую повлияет на конверсии покупателей и рост продаж.</p>";
jQuery('.readmore').parent().hide();
jQuery('.info-labels').after(text_after2);
