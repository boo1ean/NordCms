// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
var markitup = {
	settings: {
		onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
		onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
		onTab:    		{keepDefault:false, replaceWith:'    '},
		markupSet:  [
			{name:'Bold', className: 'bold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
			{name:'Italic', className: 'italic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
			{name:'Stroke through', className: 'stroke', key:'S', openWith:'<del>', closeWith:'</del>' },
			{separator:'---------------' },
			{name:'Bulleted List', className: 'list-bullet', openWith:'\t<li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>'},
			{name:'Numbered List', className: 'list-numeric', openWith:'\t<li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>'},
			{separator:'---------------' },
			{name:'Picture', className: 'picture', key:'P', replaceWith:'<img src="[![Source:!:http://]!]" alt="[![Alternative text]!]" />' },
			{name:'Link', className: 'link', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>' },
			{separator:'---------------' },
			{name:'Clean', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },
			{name:'Preview', className:'preview',  call:'preview'}
		]
	}
};