document.addEventListener('DOMContentLoaded', function() {
  const btnForm = document.querySelector('#btn-success');
  const formSuccess = document.querySelector('.form__success');
  const formError = document.querySelector('.form_error');

  if (btnForm && formSuccess) {
      btnForm.addEventListener('click', function() {
          setTimeout(function() {
              if (formSuccess) formSuccess.style.display = 'block';
          }, 1000);
          
          setTimeout(function() {
              if (formSuccess) formSuccess.style.display = 'none';
          }, 2500);
      });
  }
});

/* 
<span class="form__success" style="display:none;">Форма отправлена успешно</span>
<span class="form__error" style="display:none;">Ошибка при отправке формы</span>
*/


// Получает значение куки
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[ 1 ]) : undefined;
}

var cookieName = 'cookieNotice';
var cookieNotice = document.querySelector('.cookie-notice');

if (typeof getCookie(cookieName) == 'undefined') {
    document.querySelector('.cookie-notice__confirm').addEventListener('click', function (e) {
        e.preventDefault();
        document.cookie = cookieName + '=true; max-age=15552000; path=/'; // установка куки на 6 месяцев
        cookieNotice.style.display = 'none';
    });
}