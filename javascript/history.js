jQuery(document ).ready(function( $ ) {
	// we're using an invisible button to do a standard ajax post.
	$('.cms-container').on('click', '.select-version', function(e) {
		e.preventDefault();
		var vid = $(this).closest('tr').data('vid');
		$('#vid').val(vid);
		$('#action_goSelectVersion').trigger('click');
	});

	$('.cms-container').on('click', '#action_goRevert', function(e) {
		var vid = $('.history-list tr.selected').data('vid');
		return confirm('Revert to version ' + vid + '?');
	});
});
