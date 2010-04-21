/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.IrisRule', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			// Register commands
			ed.addCommand('mceIrisRule', function() {
				ed.execCommand('mceInsertContent', false, '<div class="note-hr">&nbsp;</div>');
			});

			// Register buttons
			ed.addButton('irisrule', {title : 'irisrule.irisrule_desc', cmd : 'mceIrisRule'});

			/*if (ed.getParam('nonbreaking_force_tab')) {
				ed.onKeyDown.add(function(ed, e) {
					if (tinymce.isIE && e.keyCode == 9) {
						ed.execCommand('mceIrisRule');
						ed.execCommand('mceIrisRule');
						ed.execCommand('mceIrisRule');
						tinymce.dom.Event.cancel(e);
					}
				});
			}*/
		},

		getInfo : function() {
			return {
				longname : 'Iris Rule',
				author : 'Richard Telford',
				authorurl : 'http://www.lightbulbuk.com',
				infourl : 'http://www.lightbulbuk.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}

		// Private methods
	});

	// Register plugin
	tinymce.PluginManager.add('irisrule', tinymce.plugins.IrisRule);
})();