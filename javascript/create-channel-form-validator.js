var form = document.getElementById("form");

var channelNameInput = document.getElementById("channel-name-input");
var channelDescriptionInput = document.getElementById("channel-description-input");
var channelBannerInput = document.getElementById("channel-banner-input");

var createChannelBtn = document.getElementById("create-channel-btn");

var channelNameValid = false;
var channelDescriptionValid = false;
var channelBannerValid = false;

var channelBannerSelected = false;

createChannelBtn.addEventListener("click", () => {
	if (
		channelNameInput.value === null ||
		channelNameInput.value === "" ||
		!/^[0-9a-zA-Z_-]+$/.test(channelNameInput.value)
	) {
		channelNameValid = false;
		channelNameInput.classList.add("is-invalid");
	} else {
		channelNameValid = true;
		channelNameInput.classList.remove("is-invalid");
		channelNameInput.classList.add("is-valid");
	}

	if (channelDescriptionInput.value === null || channelDescriptionInput.value === "") {
		channelDescriptionValid = false;
		channelDescriptionInput.classList.add("is-invalid");
	} else {
		channelDescriptionValid = true;
		channelDescriptionInput.classList.remove("is-invalid");
		channelDescriptionInput.classList.add("is-valid");
	}

	if (channelBannerSelected) {
		if (channelBannerInput.files[0].name !== "item" && typeof channelBannerInput.files[0].name !== undefined) {
			var fileSize = (channelBannerInput.files[0].size / 1024 / 1024).toFixed(4);
			if (fileSize > 2) {
				channelBannerValid = false;
				channelBannerInput.classList.add("is-invalid");
			} else {
				channelBannerValid = true;
				channelBannerInput.classList.remove("is-invalid");
			}

			var fileExt = channelBannerInput.files[0].name.split(".").pop().toLowerCase();
			if (fileExt !== "jpg" && fileExt !== "jpeg" && fileExt !== "png") {
				channelBannerValid = false;
				channelBannerInput.classList.add("is-invalid");
			} else {
				channelBannerValid = true;
				channelBannerInput.classList.remove("is-invalid");
			}
		} else {
			channelBannerValid = true;
			channelBannerInput.classList.remove("is-invalid");
			channelBannerInput.classList.add("is-valid");
		}
	} else channelBannerValid = true;

	if (channelNameValid && channelDescriptionValid && channelBannerValid) form.submit();
});

channelNameInput.addEventListener("keydown", () => {
	channelNameInput.classList.remove("is-invalid");
	channelNameInput.classList.remove("is-valid");
});

channelBannerInput.addEventListener("change", () => {
	if (channelBannerSelected) channelBannerSelected = false;
	else channelBannerSelected = true;

	channelBannerInput.classList.remove("is-invalid");
	channelBannerInput.classList.remove("is-valid");
});

window.addEventListener("load", () => {
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
});
