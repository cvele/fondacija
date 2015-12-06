$(document).ready(function() {
	$("#selectAll").change(function () {
		$("input:checkbox").prop('checked', $(this).prop("checked"));
	});

	$('#delete-companies').confirmation({
		'popout': true,
		'singleton': true,
		'btnOkIcon': 'fa fa-check',
		'btnCancelIcon': 'fa fa-remove',
		'btnOkClass': 'ce apn btn-xs',
		'btnCancelClass': 'ce apn btn-xs',
		'onConfirm': deleteCompanies,
		'title': 'This will delete selected companies, are you sure?'
	});

	$('#delete-contacts').confirmation({
		'popout': true,
		'singleton': true,
		'btnOkIcon': 'fa fa-check',
		'btnCancelIcon': 'fa fa-remove',
		'btnOkClass': 'ce apn btn-xs',
		'btnCancelClass': 'ce apn btn-xs',
		'onConfirm': deleteContacts,
		'title': 'This will delete selected contacts, are you sure?'
	});

	$('#delete-documents').confirmation({
		'popout': true,
		'singleton': true,
		'btnOkIcon': 'fa fa-check',
		'btnCancelIcon': 'fa fa-remove',
		'btnOkClass': 'ce apn btn-xs',
		'btnCancelClass': 'ce apn btn-xs',
		'onConfirm': deleteDocuments,
		'title': 'This will delete selected documents, are you sure?'
	});
});


var deleteCompanies = function() {
	var ids = [];
	$('.company-checkbox:checked').each(function(k,v){
		ids.push($(v).data('id'));
	});

	$.post(Routing.generate('company_delete_multiple'), {'ids': ids}, function(data) {
		$(data.deleted).each(function(k,v){
			$('tr.company-row[data-id="'+v+'"]').remove();
		});
	});

}

var deleteContacts = function() {
	var ids = [];
	$('.contact-checkbox:checked').each(function(k,v){
		ids.push($(v).data('id'));
	});

	$.post(Routing.generate('contacts_delete_multiple'), {'ids': ids}, function(data) {
		$(data.deleted).each(function(k,v){
			$('tr.contact-row[data-id="'+v+'"]').remove();
		});
	});

}
var deleteDocuments = function() {
	var ids = [];
	$('.document-checkbox:checked').each(function(k,v){
		ids.push($(v).data('id'));
	});

	$.post(Routing.generate('documents_delete_multiple'), {'ids': ids}, function(data) {
		$(data.deleted).each(function(k,v){
			$('tr.document-row[data-id="'+v+'"]').remove();
		});
	});

}