var passwordInput = document.getElementById("password-input");
var confirmPasswordInput = document.getElementById("confirm-password-input");
var togglePasswordBtn = document.getElementById("toggle-password-btn");

var state = false;

function togglePassword() {
	if (state) {
		state = false;
		passwordInput.setAttribute("type", "password");
		togglePasswordBtn.setAttribute("title", "Show password");
		togglePasswordBtn.innerHTML = '<i class="bi bi-eye"></i>';

		if (confirmPasswordInput !== null) confirmPasswordInput.setAttribute("type", "password");
	} else {
		state = true;
		passwordInput.setAttribute("type", "text");
		togglePasswordBtn.setAttribute("title", "Hide password");
		togglePasswordBtn.innerHTML = '<i class="bi bi-eye-slash"></i>';

		if (confirmPasswordInput !== null) confirmPasswordInput.setAttribute("type", "text");
	}
}
