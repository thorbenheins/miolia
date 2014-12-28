var oldUrl;
var newUrl;

function showModal(url, width, keepUrl, useIFrame, height) {
	if (width) {
		$("#modalDialog").width(width);
	}
	
	if( height ) {
		$("#modalDialogContent").height(height);
	}

	if (keepUrl) {
		newUrl = window.location.href;
	}else{
		newUrl = url;
	}
	
	if(useIFrame) {
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
		$("#modalDialogContent").html('<iframe frameborder="0" src="' + url + '" width="100%" height="100%" />');
		window.history.pushState('', '', newUrl);
	}else{
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

function closeModal()
{
	$.modal.close();
}