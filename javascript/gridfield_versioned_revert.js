(function($) {
	$.entwine('ss', function($) {
		// Adds back in functionality blocked by betterbuttons
		$('.do-version-viewer-form.better-buttons-form.cms-edit-form.changed').entwine({
			onmatch: function(e) {
				this.find('button[name=action_save]').button('option', 'showingAlternate', true);
				this.find('button[name=action_publish]').button('option', 'showingAlternate', true);
				this._super(e);
			},
			onunmatch: function(e) {
				var saveButton = this.find('button[name=action_save]');
				if(saveButton.data('button')) saveButton.button('option', 'showingAlternate', false);
				var publishButton = this.find('button[name=action_publish]');
				if(publishButton.data('button')) publishButton.button('option', 'showingAlternate', false);
				this._super(e);
			}
		});

	});
})(jQuery);