
function merger2updateEditButton(button) {
	if (button) {
		button = $(button);
		var image = button.getElement('img');
		var select = button.getParent('tr').getElement('select');
		var moduleID = select.value;

		if (/^\d+$/.exec(moduleID)) {
			image.src = image.src.replace('edit_.gif', 'edit.gif');
			button.moduleID = moduleID;
			button.setStyle('cursor', '');
		}
		else {
			image.src = image.src.replace('edit.gif', 'edit_.gif');
			button.moduleID = null;
			button.setStyle('cursor', 'default');
		}
	}
}

function merger2buttonClick() {
	if (this.moduleID) {
		var rt = /[&\?](rt=[\d\w]+)/.exec(document.location.search);
		location.href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' + this.moduleID + (rt ? '&' + rt[1] : '');
	}
}

$(window).addEvent('domready', function() {
	MultiColumnWizard.addOperationUpdateCallback('copy', function(el, row) {
		var button = $(row).getElement('a.edit_module');
		merger2updateEditButton(button);
		button.addEvent('click', merger2buttonClick);
		$(row).getElement('select').addEvent('change', function() {
			merger2updateEditButton($(this).getParent('tr').getElement('a.edit_module'));
		});
	});

	$$('#ctrl_merger_data select').addEvent('change', function() {
		merger2updateEditButton($(this).getParent('tr').getElement('a.edit_module'));
	});
	$$('#ctrl_merger_data a.edit_module').each(function(button) {
		merger2updateEditButton(button);
		button.addEvent('click', merger2buttonClick);
	});

	$('opt_merger_container_0').addEvent('change', function() {
		$('ctrl_cssID_0').disabled = !this.checked;
		$('ctrl_cssID_1').disabled = !this.checked;
		$('ctrl_space_0').disabled = !this.checked;
		$('ctrl_space_1').disabled = !this.checked;
	});
});
