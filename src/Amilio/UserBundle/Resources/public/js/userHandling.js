function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2)
		return parts.pop().split(";").shift();
}

function isUser(userId) {
	return userId == currentUserId;
}

function isLoggedIn() {
	return currentUserId > 0;
}

var currentUserId = getCookie("userId");

var currentUserName = getCookie("userName");

$(document).ready(function() {
	$("[class^=user-]").hide();
	$("[class^=not-user.].show();
	$(".user-" + currentUserId).show();
	if (isLoggedIn()) {
		$(".user").show();
		$(".username").html(currentUserName);
		$(".not-user-" + currentUserId).hide();
	} else {
		$(".anonymous").show();
	}
});
