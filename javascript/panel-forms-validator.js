var pfpForm = document.getElementById("pfp-form");
var passwordForm = document.getElementById("password-form");
var usernameForm = document.getElementById("username-form");
var deleteUserForm = document.getElementById("delete-user-form");

var fileInput = document.getElementById("file-input");
var passwordInput = document.getElementById("password-input");
var newPasswordInput = document.getElementById("confirm-password-input");
var usernameInput = document.getElementById("username-input");
var deleteUserUsernameInput = document.getElementById("delete-user-username-input");

var changePfpButton = document.getElementById("change-pfp-btn");
var changePasswordButton = document.getElementById("change-password-btn");
var changeUsernameButton = document.getElementById("change-username-btn");
var deleteUserButton = document.getElementById("delete-user-btn");

var fileSelected = false;
var fileValid = false;

var passwordValid = false;
var newPasswordValid = false;
var usernameValid = false;

fileInput.addEventListener("change", () => {
	if (fileSelected) fileSelected = false;
	else fileSelected = true;

	fileInput.classList.remove("is-invalid");
	fileInput.classList.remove("is-valid");
});

changePfpButton.addEventListener("click", () => {
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
	} else {
		fileValid = false;
		fileInput.classList.add("is-invalid");
	}

	if (fileValid) pfpForm.submit();
});

changePasswordButton.addEventListener("click", () => {
	if (passwordInput.value === null || passwordInput.value === "") {
		passwordValid = false;
		passwordInput.classList.add("is-invalid");
	} else {
		passwordValid = true;
		passwordInput.classList.remove("is-invalid");
		passwordInput.classList.add("is-valid");
	}

	if (
		newPasswordInput.value === null ||
		newPasswordInput.value === " " ||
		!(newPasswordInput.value.length >= 8) ||
		newPasswordInput.value === passwordInput.value
	) {
		newPasswordValid = false;
		newPasswordInput.classList.add("is-invalid");
	} else {
		newPasswordValid = true;
		newPasswordInput.classList.remove("is-invalid");
		newPasswordInput.classList.add("is-valid");
	}

	if (passwordValid && newPasswordValid) passwordForm.submit();
});

passwordInput.addEventListener("keydown", () => {
	passwordInput.classList.remove("is-invalid");
	passwordInput.classList.remove("is-valid");

	newPasswordInput.classList.remove("is-invalid");
	newPasswordInput.classList.remove("is-valid");
});

newPasswordInput.addEventListener("keydown", () => {
	passwordInput.classList.remove("is-invalid");
	passwordInput.classList.remove("is-valid");

	newPasswordInput.classList.remove("is-invalid");
	newPasswordInput.classList.remove("is-valid");
});

changeUsernameButton.addEventListener("click", () => {
	if (usernameInput.value === null || usernameInput.value === "" || !/^[0-9a-zA-Z_-]+$/.test(usernameInput.value)) {
		usernameValid = false;
		usernameInput.classList.add("is-invalid");
	} else {
		usernameValid = true;
		usernameInput.classList.remove("is-invalid");
		usernameInput.classList.add("is-valid");
	}

	if (usernameValid) usernameForm.submit();
});

usernameInput.addEventListener("keydown", () => {
	usernameInput.classList.remove("is-invalid");
	usernameInput.classList.remove("is-valid");
});

deleteUserButton.addEventListener("click", () => {
	if (deleteUserUsernameInput.value !== null || usernameInput.value !== "") deleteUserForm.submit();
});

deleteUserUsernameInput.addEventListener("keyup", () => {
	if (deleteUserUsernameInput.value === null || deleteUserUsernameInput.value === "") {
		deleteUserButton.classList.add("disabled");
	} else {
		deleteUserButton.classList.remove("disabled");
	}
});

window.addEventListener("load", () => {
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
});
