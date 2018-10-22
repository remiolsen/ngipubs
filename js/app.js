$(document).foundation();

$(document).ready(function() {
	updateStatus();
	
	$("#load_clarity").on("click", function() {
		$(this).prop("disabled",true).text("Working...");
		clarityLoad('labs');
	});
	
	function clarityLoad(type) {
		$.ajax({
			url: '_clarity_load.php', 
			data: { task: 'load', type: type }, 
			dataType: 'json', 
			beforeSend: function() {
				$("#clarity_status_message").append('Loading ' + type + '...<br>');
			}, 
			success: function(json) {
				$("#clarity_status_message").append(json.message + '<br>');
				updateDB(type);
			}
		});
	}
	
	function updateDB(type) {
		$.ajax({
			url: '_clarity_load.php', 
			data: { task: 'update', type: type }, 
			dataType: 'json', 
			beforeSend: function() {
				$("#clarity_status_message").append('Updating database for ' + type + '...<br>');
			}, 
			success: function(json) {
				$("#clarity_status_message").append(json.message + '<br>');
				if(type == 'labs') {
					clarityLoad('researchers');
				} else {
					$("#clarity_status_message").append('Updating process finished!<br>');
					$("#load_clarity").prop("disabled",false).text("Update");
					updateStatus();
				}
			}
		});
	}
	
	function updateStatus() {
		$.ajax({
			url: '_clarity_status.php', 
			dataType: 'html', 
			success: function(html) {
				$("#clarity_status").html(html);
			}
		});
	}
	
	$('.find_pub').on('click', function() {
		var button = this;
		var id = $(this).attr('id').split('-')[1];
 		$.ajax({
			url: '_publication_add.php', 
			data: { lab_id: id }, 
			dataType: 'json', 
			beforeSend: function() {
				$(button).text('Processing...');
			}, 
			success: function(json) {
				if(json.found>0) {
					foundstring = '(found ' + json.found + ' already in database)';
				} else {
					foundstring = '';
				}
				
				$(button).text('Added ' + json.added + ' publications ' + foundstring);
			}
		});
	});

	$('.start_trawl').on('click', function() {
		$.getJSON( "cache/trawltest.json", function( data ) {
			var items = [];
			$.each( data.lab_list, function( id, lab ) {
				var trawl = $.ajax({
					url: '_publication_add.php', 
					data: { lab_id: id }, 
					dataType: 'json', 
					beforeSend: function() {
						$('#status-' + id).text('Processing...');
					}, 
					success: function(json) {
						if(json.found>0) {
							foundstring = '(found ' + json.found + ' already in database)';
						} else {
							foundstring = '';
						}
						
						$('#result-' + id).text('Added ' + json.added + ' publications ' + foundstring);
						$('#status-' + id).addClass('success').text('Done');
					}
				});
			});
		});	
	});
	
	$('.pause_trawl').on('click', function() {
		
	});

	$('.start_sync').on('click', function() {
		$.ajax({
			url: '_sync_db.php',
			beforeSend: function() {
				$("#sync_status_message").append(
					'<div class="callout primary", id="syncing_ongoing">(imagine a rolling circle) Syncing database against publications.scilifelab.se</div>'
				);
			},
			success: function(json_str) {
				json = JSON.parse(json_str);
				$("#syncing_ongoing").remove();
				if (json["mismatch"] != null && json["mismatch"].length > 0){
					$("#sync_status_message").append(
						'<div class="callout alert"> Warning: These publications from publications.scilifelab.se are marked as "maybe" or "discarded": ' + json["mismatch"] + '</br> - Consider to remove these from publications.scilifelab.se </div>'
					);
				};
				if (json["other_unknown_status"] != null && json["other_unknown_status"].length > 0) {
					$("#sync_status_message").append(
						'<div class="callout alert"> Warning: These publications from publications.scilifelab.se have unknown statuses in the local database: ' + json["other_unknown_status"] + '</div>'
					);
				};
				if (json["missing"] != null && json["missing"].length > 0) {
					$("#sync_status_message").append(
						'<div class="callout alert"> Warning: These publications from publications.scilifelab.se are missing in the local database: ' + json["missing"] + '</div>'
					);
				};
				$("#sync_status_message").append(
					'<div class="callout success"><ul>' +
						'<li>' + json['total'] + ' papers fetched in total</li>' +
						'<li>' + json['verified_and_added'] + ' papers were verified and also added.</li>' +
						'<li>' + json['auto'] + ' papers were added but not verified (auto)</li>' +
						'<li>' + json['no_change'] + ' papers were already noted as auto or added in database</li></ul>' +
					'</div>'
				);
			},
			error: function(xhr, error){
        		console.debug(xhr); console.debug(error);
				$("#sync_status_message").append(
					'<div class="callout alert">ERROR: Syncing database failed!</div>'
				);
 			}
		})
	});

	$('.verify_button').on('click', function() {
		var button = this;
		var id = $(this).attr('id').split('-')[1];
		$.ajax({
			url: '_publication_verify.php', 
			data: { publication_id: id, type: 'verify'}, 
			dataType: 'json', 
			success: function(json) {
				$('#status_label-' + id).text('Verified').addClass('success');
				$('#publ-' + id).addClass('success');
			}
		});
	});
	
	$('.discard_button').on('click', function() {
		var button = this;
		var id = $(this).attr('id').split('-')[1];
		$.ajax({
			url: '_publication_verify.php', 
			data: { publication_id: id, type: 'discard'}, 
			dataType: 'json', 
			success: function(json) {
				$('#status_label-' + id).text('Discarded').addClass('alert');
				$('#publ-' + id).addClass('alert');
			}
		});
	});
	
	$('.maybe_button').on('click', function() {
		var button = this;
		var id = $(this).attr('id').split('-')[1];
		$.ajax({
			url: '_publication_verify.php', 
			data: { publication_id: id, type: 'maybe'}, 
			dataType: 'json', 
			success: function(json) {
				$('#status_label-' + id).text('Maybe').addClass('warning');
				$('#publ-' + id).addClass('warning');
			}
		});
	});
});
