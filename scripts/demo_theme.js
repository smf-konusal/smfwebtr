// Defines the formatting info that SCEditor uses to render a BBCode in the editor.
// See https://www.sceditor.com/documentation/custom-bbcodes for info.
sceditor.formats.bbcode.set(
	'demo_theme', {
		// The HTML tags that this command will act on when toggling view modes.
		tags: {
			div: {
				class: 'demo_theme'
			}
		},
		// Called when toggling from WYSIWYG mode to source mode.
		format: function (element, content) {

			return '[demo_resim][/demo_resim]\n[demo][/demo]\n[download][/download]';
		},
		// Called when toggling from source mode to WYSIWYG mode.
		html: function (token, attrs, content) {
			return '<div class="demo_theme">' + content + '</div>';
		},
		// Other options
		allowsEmpty: true,
		isInline: false,
		quoteType: $.sceditor.BBCodeParser.QuoteType.never,
	}
);

// Defines the command info that SCEditor uses when the user clicks a BBCode's button.
// See https://www.sceditor.com/documentation/custom-commands for info.
sceditor.command.set(
	'demo_theme', {
		// Called when editor is in WYSIWYG mode.
		exec: function(caller) {
			// The insert() method lets you define opening and closing BBC strings, which
			// will be rendered according to the formatting info defined for that BBC.
			// For more advanced possibilites, such as defining the exact HTML you want
			// to use, see the SCEditor documentation.
			this.insert('[demo_resim][/demo_resim]\n[demo][/demo]\n[download][/download]');
		},
		// Called when editor is in source mode.
		txtExec: function(caller) {
			this.insert('[demo_resim][/demo_resim]\n[demo][/demo]\n[download][/download]');
		}
	}
);
