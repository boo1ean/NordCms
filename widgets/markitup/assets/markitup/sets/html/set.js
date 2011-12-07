// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
var markitup = {
	settings: {
		onShiftEnter:	{keepDefault:false, replaceWith:'<br />\n'},
		onCtrlEnter:	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
		onTab:			{keepDefault:false, openWith:'\t'},
		markupSet: [
			{ name:'Heading 1', className: 'h1', key:'1', openWith:'<h1(!( class="[![Class]!]")!)>', closeWith:'</h1>', placeHolder:'Your title here...' },
			{ name:'Heading 2', className: 'h2', key:'2', openWith:'<h2(!( class="[![Class]!]")!)>', closeWith:'</h2>', placeHolder:'Your title here...' },
			{ name:'Heading 3', className: 'h3', key:'3', openWith:'<h3(!( class="[![Class]!]")!)>', closeWith:'</h3>', placeHolder:'Your title here...' },
			{ name:'Heading 4', className: 'h4', key:'4', openWith:'<h4(!( class="[![Class]!]")!)>', closeWith:'</h4>', placeHolder:'Your title here...' },
			{ name:'Heading 5', className: 'h5', key:'5', openWith:'<h5(!( class="[![Class]!]")!)>', closeWith:'</h5>', placeHolder:'Your title here...' },
			{ name:'Heading 6', className: 'h6', key:'6', openWith:'<h6(!( class="[![Class]!]")!)>', closeWith:'</h6>', placeHolder:'Your title here...' },
			{ name:'Paragraph', className:'paragraph', openWith:'<p(!( class="[![Class]!]")!)>', closeWith:'</p>' },
			{ separator:'---------------' },
			{ name:'Bold', className: 'bold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
			{ name:'Italic', className: 'italic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)' },
			{ name:'Underline', className: 'underline', key:'U', openWith:'<span style="text-decoration: underline;">', closeWith:'</span>' },
			{ separator:'---------------' },
			{ name:'Bulleted list', className: 'list-bullet', openWith:'<ul>\n', closeWith:'</ul>\n' },
			{ name:'Numbered list', className: 'list-numeric', openWith:'<ol>\n', closeWith:'</ol>\n' },
			{ name:'List item', className: 'list-item', openWith:'<li>', closeWith:'</li>' },
			{ separator:'---------------' },
			{ name:'Picture', className: 'picture', key:'P', replaceWith:'<img src="[![Source:!:http://]!]" alt="[![Alternative text]!]" />' },
			{ name:'Link', className: 'link', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link here...' },
			{ separator:'---------------' },
			{ name:'Clean', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },
			{ name:'Preview', className:'preview', call:'preview' }
		]
	}
};