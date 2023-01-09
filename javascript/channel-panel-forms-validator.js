var bannerForm = document.getElementById("banner-form");
var deleteChannelForm = document.getElementById("delete-channel-form");

var fileInput = document.getElementById("file-input");
var channelNameInput = document.getElementById("channel-name-input");

var changeBannerButton = document.getElementById("change-banner-btn");
var deleteChannelButton = document.getElementById("delete-channel-btn");

var fileSelected = false;
var fileValid = false;

var channelNameValid = false;

fileInput.addEventListener("change", () => {
	if (fileSelected) fileSelected = false;
	else fileSelected = true;

	fileInput.classList.remove("is-invalid");
	fileInput.classList.remove("is-valid");
});

changeBannerButton.addEventListener("click", () => {
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

	if (fileValid) bannerForm.submit();
});

channelNameInput.addEventListener("keydown", () => {
	channelNameInput.classList.remove("is-invalid");
	channelNameInput.classList.remove("is-valid");
});

deleteChannelButton.addEventListener("click", () => {
	if (channelNameInput.value !== null || channelNameInput.value !== "") deleteChannelForm.submit();
});

channelNameInput.addEventListener("keyup", () => {
	if (channelNameInput.value === null || channelNameInput.value === "") {
		deleteChannelButton.classList.add("disabled");
	} else {
		deleteChannelButton.classList.remove("disabled");
	}
});

window.addEventListener("load", () => {
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
});
