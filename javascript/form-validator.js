var form = document.getElementById("form");

var usernameInput = document.getElementById("username-input");
var passwordInput = document.getElementById("password-input");
var confirmPasswordInput = document.getElementById("confirm-password-input");
var fileInput = document.getElementById("file-input");

var registerButton = document.getElementById("register-btn");
var loginButton = document.getElementById("login-btn");
var loadingButton = document.getElementById("loading-btn");

var usernameValid = false;
var passwordValid = false;
var confirmPasswordValid = false;

var fileSelected = false;
var fileValid = false;

function validateForm(isLogin) {
	if (!isLogin) {
		if (
			confirmPasswordInput.value === null ||
			confirmPasswordInput.value === " " ||
			!(confirmPasswordInput.value.length >= 8) ||
			confirmPasswordInput.value !== passwordInput.value
		) {
			confirmPasswordValid = false;
			confirmPasswordInput.classList.add("is-invalid");
		} else {
			confirmPasswordValid = true;
			confirmPasswordInput.classList.remove("is-invalid");
			confirmPasswordInput.classList.add("is-valid");
		}

		if (fileSelected) {
			if (fileInput.files[0].name !== "item" && typeof fileInput.files[0].name !== undefined) {
				var fileSize = (fileInput.files[0].size / 1024 / 1024).toFixed(4);
				if (fileSize > 2) {
					fileValid = false;
					fileInput.classList.add("is-invalid");
				} else {
					fileValid = true;
					fileInput.classList.remove("is-invalid");
				}

				var fileExt = fileInput.files[0].name.split(".").pop().toLowerCase();
				if (fileExt !== "jpg" && fileExt !== "jpeg" && fileExt !== "png") {
					fileValid = false;
					fileInput.classList.add("is-invalid");
				} else {
					fileValid = true;
					fileInput.classList.remove("is-invalid");
				}
			} else {
				fileValid = true;
				fileInput.classList.remove("is-invalid");
				fileInput.classList.add("is-valid");
			}
		} else fileValid = true;
	} else {
		fileValid = true;
		confirmPasswordValid = true;
	}

	if (usernameInput.value === null || usernameInput.value === " " || !/^[0-9a-zA-Z_-]+$/.test(usernameInput.value)) {
		usernameValid = false;
		usernameInput.classList.add("is-invalid");
	} else {
		usernameValid = true;
		usernameInput.classList.remove("is-invalid");
		usernameInput.classList.add("is-valid");
	}

	if (passwordInput.value === null || passwordInput.value === " " || !(passwordInput.value.length >= 8)) {
		passwordValid = false;
		passwordInput.classList.add("is-invalid");
	} else {
		passwordValid = true;
		passwordInput.classList.remove("is-invalid");
		passwordInput.classList.add("is-valid");
	}

	if (usernameValid && passwordValid && confirmPasswordValid && fileValid) {
		if (loadingButton.classList.contains("d-none")) {
			loadingButton.classList.remove("d-none");
			registerButton.classList.add("d-none");
			loginButton.classList.add("d-none");
		} else {
			loadingButton.classList.add("d-none");
			registerButton.classList.remove("d-none");
			loginButton.classList.remove("d-none");
		}

		form.submit();
	} else return;
}

usernameInput.addEventListener("keydown", () => {
	usernameInput.classList.remove("is-invalid");
	usernameInput.classList.remove("is-valid");
});

passwordInput.addEventListener("keydown", () => {
	passwordInput.classList.remove("is-invalid");
	passwordInput.classList.remove("is-valid");
});

if (confirmPasswordInput !== null) {
	confirmPasswordInput.addEventListener("keydown", () => {
		confirmPasswordInput.classList.remove("is-invalid");
		confirmPasswordInput.classList.remove("is-valid");
	});
}

if (fileInput !== null) {
	fileInput.addEventListener("change", () => {
		if (fileSelected) fileSelected = false;
		else fileSelected = true;
	});
}

window.addEventListener("load", () => {
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
});
