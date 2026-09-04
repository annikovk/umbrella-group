document.addEventListener("DOMContentLoaded", function() {
	document.addEventListener("click", function(event) {
		if (event.target.tagName.toLowerCase() === "input") {
			const form = event.target.closest("form");
			if (!form) return;
	
			const recaptchaInput = form.querySelector("[name=_wpcf7_recaptcha_response]");
			if (!recaptchaInput) return;

			grecaptcha.ready(function() {
				grecaptcha.execute('6LexE8cpAAAAAB84YlbaewFpOGf3Nz31kIGgsDZ4', {action: 'submit'}).then(function(token) {
					recaptchaInput.value = token;
				});
			});
		}
	});
});
