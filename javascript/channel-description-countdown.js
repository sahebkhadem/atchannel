var channelDescriptionInput = document.getElementById("channel-description-input");
var countdownSpan = document.getElementById("characters-countdown");

channelDescriptionInput.addEventListener("keyup", () => {
	countdownSpan.innerHTML = 1024 - channelDescriptionInput.value.length;
});
