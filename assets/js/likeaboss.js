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
	l: {
		'h1': 'First level heading',
		'h2': 'Second level heading',
		'h3': 'Third level heading',
		
		'bold': 'Bold text',
		'italic': 'Italic text',
		
		'link': 'Link',
		
		'code': 'Code block',
	},
	
	tag: {
		'h1': {
			open: '#',
			close: ''
		},
		
		'h2': {
			open: '##',
			close: ''
		},
		
		'h3': {
			open: '###',
			close: ''
		},
		
		'h4': {
			open: '####',
			close: ''
		},
		
		'h5': {
			open: '#####',
			close: ''
		},
		
		'h6': {
			open: '######',
			close: ''
		},
		
		'bold': {
			open: '*',
			close: '*'
		},
		
		'italic': {
			open: '__',
			close: '__'
		},
		
		'link': {
			open: '[',
			close: '](http://)'
		},
		
		'code': {
			open: "\n    ",
			close: ""
		}
	},
	
	markup: function(type) {
		this.value = 'a';
	},
	
	locale: function(name) {
		return likeABoss.l[name];
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
			
			function mkbtn(name) {
				button = $('<a href="" class="likeaboss_' + name + '" title="' + likeABoss.locale(name) + '">' + name + '</a>');
				button.click(function(){ markup(name); return false; });
				return button;
			}
			
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
			
			function setCaretPosition(len) {
				setSelection(len, 0);
			}
			
			function getSelection() {
				textarea.focus();
				caretPosition = textarea.selectionStart;
				selection = textarea.value.substring(caretPosition, textarea.selectionEnd);
				return selection;
			}
			
			function setSelection(start, len) {
				textarea.setSelectionRange(start, start + len);
				textarea.focus();
			}
			
			function markup(type) {
				caretPosition = getCaretPosition();
				selection = getSelection();
				
				var block = likeABoss.tag[type].open + selection + likeABoss.tag[type].close;
				
				textarea.value = textarea.value.substr(0, caretPosition) + block + textarea.value.substring(caretPosition + selection.length, textarea.value.length)
				
				setCaretPosition(caretPosition + block.length);
			}
			
			$$.wrap('<div class="likeaboss_container"></div>');
			$$.addClass('likeaboss_editor');
			
			var toolbar = $('<div class="likeaboss_toolbar"></div>');
			toolbar.append(mkbtn('h1'));
			toolbar.append(mkbtn('h2'));
			toolbar.append(mkbtn('h3'));
			
			toolbar.append(mkbtn('bold'));
			toolbar.append(mkbtn('italic'));
			
			toolbar.append(mkbtn('link'));
			
			toolbar.append(mkbtn('code'));
			
			$$.before(toolbar);
		});
	}
})(jQuery);