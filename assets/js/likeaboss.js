/*
 * Like a Boss
 * Copyright (C) 2012 Jack Polgar
 * All Rights Reserved
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Jack Polgar nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL JACK POLGAR BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

var likeABoss = {
	strings: {
		// Tags
		'h1': 'First level heading',
		'h2': 'Second level heading',
		'h3': 'Third level heading',
		'h4': 'Fourth level heading',
		'h5': 'Fifth level heading',
		'h6': 'Sixth level heading',
		
		'bold': 'Bold text',
		'italic': 'Italic text',
		
		'bullet_list': 'Bulleted list',
		'number_list': 'Numbered list',
		
		'link': 'Link',
		'image': 'Image',
		
		'code': 'Code block',
		
		// Other
		'link_text': 'Link text',
		'url': 'URL',
	},
	
	tag: {
		'h1': {open: '#'},
		'h2': {open: '##'},
		'h3': {open: '###'},
		'h4': {open: '####'},
		'h5': {open: '#####'},
		'h6': {open: '######'},
		//
		'bold': {open: '**', close: '**'},
		'italic': {open: '_', close: '_'},
		//
		'bullet_list': {open: '- ',
			func: function(tag, selection) {
				if (selection.indexOf('\n') != -1) {
					selection = selection.replace(/\n/g, '\n' + tag.open);
				}
				
				return tag.open + selection;
			}
		},
		'number_list': {open: '1. ',
			func: function(tag, selection) {
				if (selection.indexOf('\n') != -1) {
					var list = selection.split('\n');
					selection = '';
					
					for (var i = 1; i <= list.length; ++i) {
						selection += i + '. ' + list[i-1] + (i < list.length ? '\n' : '');
					}
					
					return selection;
				}	
							
				return tag.open + selection;
			}
		},
		//
		'link': {open: '[', middle: '](', close: ')',
			func: function(tag, selection) {
				var text = prompt(likeABoss.locale('link_text'), selection);
				if (!text || text == null) {
					return;
				}
				
				var url = prompt(likeABoss.locale('url'), selection ? selection : 'http://');
				if (!url || url == null) {
					return;
				}
				
				return tag.open + text + tag.middle + url + tag.close;
			}
		},
		//
		'image': {open: '![An Image](', close: ')',
			func: function(tag, selection) {
				if (!selection || selection == null) {
					var url = prompt(likeABoss.locale('url'));
					if (!url || url == null) {
						return;
					}
				}
				
				var val = url ? url : selection;
				return tag.open + val + tag.close;
			}
		},
		'code': {open: '    ',
			func: function(tag, selection) {
				if (selection.indexOf('\n') != -1) {
					selection = selection.replace(/\n/g, '\n' + tag.open);
				}
				
				return tag.open + selection;
			}
		},
	},
	
	/**
	 * Fetches the localised string.
	 *
	 * @param string
	 *
	 * @return string
	 */
	locale: function(name) {
		return likeABoss.strings[name];
	}
};

(function($){
	$.fn.likeaboss = function() {
		this.each(function() {
			var scrollPosition, caretPosition;
			var selection;
			
			var $$ = $(this);
			var textarea = this;
			
			scrollPosition = caretPosition = 0;
			
			/**
			 * Creates a link element for the editor toolbar
			 * with the appropriate class, title and text
			 * and binds a click event to it
			 *
			 * @param string name
			 *
			 * @return object
			 */
			function mkbtn(name) {
				button = $('<a href="#" class="likeaboss_' + name + '" title="' + likeABoss.locale(name) + '">' + name + '</a>');
				button.click(function(){ markup(name); return false; });
				return button;
			}
			
			/**
			 * Gets the caret position.
			 *
			 * @return integer
			 */
			function getCaretPosition() {
				if (document.selection) {
					var slct = document.selection.createRange();
					slct.moveStart('character', -textarea.value.length);
					caretPosition = slct.text.length;
				} else if (textarea.selectionStart || textarea.selectionStart == 0) {
					caretPosition = textarea.selectionStart;
				}
				
				return caretPosition;
			}
			
			/**
			 * Sets the caret position.
			 *
			 * @param integer pos
			 */
			function setCaretPosition(pos) {
				setSelection(pos, 0);
			}
			
			/**
			 * Gets the selected text.
			 *
			 * @return string
			 */
			function getSelection() {
				textarea.focus();
				caretPosition = textarea.selectionStart;
				selection = textarea.value.substring(caretPosition, textarea.selectionEnd);
				return selection;
			}
			
			/**
			 * Selects the text in the specified range.
			 *
			 * @param integer start
			 * @param integer len Length of the selection from the start.
			 */
			function setSelection(start, len) {
				textarea.setSelectionRange(start, start + len);
				textarea.focus();
			}
			
			/**
			 * Adds the markup to the textarea, or around
			 * the users current selection.
			 *
			 * @param string type
			 */
			function markup(type) {
				caretPosition = getCaretPosition();
				selection = getSelection();
				
				var block;
				if (likeABoss.tag[type].func) {
					block = likeABoss.tag[type].func(likeABoss.tag[type], selection);
				} else {
					block = (likeABoss.tag[type].open ? likeABoss.tag[type].open : '') + selection + (likeABoss.tag[type].close ? likeABoss.tag[type].close : '');
				}
				
				if (!block || block == '' || block == null) {
					return false;
				}
				
				textarea.value = textarea.value.substr(0, caretPosition) + block + textarea.value.substring(caretPosition + selection.length, textarea.value.length)
				setCaretPosition(caretPosition + block.length);
			}
			
			// Wrap the textarea in a container
			$$.wrap('<div class="likeaboss_container"></div>');
			$$.addClass('likeaboss_editor');
			
			// Create the toolbar and add the buttons.
			var toolbar = $('<div class="likeaboss_toolbar"></div>');
			toolbar.append(mkbtn('h2'));
			toolbar.append(mkbtn('h3'));
			toolbar.append(mkbtn('h4'));
			
			toolbar.append(mkbtn('bold'));
			toolbar.append(mkbtn('italic'));
			
			toolbar.append(mkbtn('bullet_list'));
			toolbar.append(mkbtn('number_list'));
			
			toolbar.append(mkbtn('link'));
			toolbar.append(mkbtn('image'));
			
			toolbar.append(mkbtn('code'));
			
			// Add the toolbar to the container
			$$.before(toolbar);
		});
	}
})(jQuery);