var oldUrl;
var newUrl;

function showModal(url, width) {
	newUrl = url;

	if (width) {
		$("#modalDialog").width(width);
	}
	$.get(url, function(data) {
		$.modal.close();
		oldUrl = window.location.href;
		$("#modalDialog").modal({
			opacity : 80,
			overlayCss : {
				backgroundColor : "#000"
			},
			onClose : function() {
				window.history.pushState('', '', oldUrl);
				$.modal.close();
			}
		});
		$("#modalDialogContent").html(data);
		processUserFields();
		window.history.pushState('', '', newUrl);
	});
}

function showProductModal(id, name, channelId) {
	var url = product_element_show_path;
	url = url.replace("elementid", id);
	url = url.replace("canonicalname", name);

	showModal(url + "#" + channelId, "800px");
}

var elementToRemove;

function confirmElementRemove(id) {
	elementToRemove = id;
	confirm(function() {
		url = product_element_remove_path.replace("elementid", elementToRemove);
		$.post(url, function(data) {
			location.reload();
		});
	});
}