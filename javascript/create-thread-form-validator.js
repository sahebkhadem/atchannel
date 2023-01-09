var form = document.getElementById("form");

var threadTitleInput = document.getElementById("thread-title-input");

var createThreadBtn = document.getElementById("create-thread-btn");

var threadTitleValid = false;

createThreadBtn.addEventListener("click", () => {
	if (threadTitleInput.value === null || threadTitleInput.value === "") {
		threadTitleValid = false;
		threadTitleInput.classList.add("is-invalid");
	} else {
		threadTitleValid = true;
		threadTitleInput.classList.remove("is-invalid");
		threadTitleInput.classList.add("is-valid");
	}

	if (threadTitleValid) form.submit();
});

threadTitleInput.addEventListener("keydown", () => {
	threadTitleInput.classList.remove("is-invalid");
	threadTitleInput.classList.remove("is-valid");
});

window.addEventListener("load", () => {
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
});
