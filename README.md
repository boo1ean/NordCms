_Current version 0.9.0_

Nord CMS is a stand-alone module that provides the core content management functionality such as multilingual content to any Yii project. Nord CMS has been developed under the New BSD License, please see the LICENSE file.

##Links

* [Discussion]()
* [Report an issue](https://bitbucket.org/NordLabs/nordcms/issues/new)
* [Fork on Bitbucket](https://bitbucket.org/NordLabs/nordcms)

##What's included?

* In place updating of nodes
* Rendering of nodes as pages and widgets
* Attaching of images and other files to nodes
* Multilingual content
* Search engine optimization for pages
* Support for meta tags for pages
* Theme for projects using Twitter's Bootstrap library

##Setup

Unzip the extension to e.g. protected/modules/cms and add the following to your application configuration:

~~~
[php]
'modules'=>array(
	.....
	'cms',
),
'components'=>array(
	.....
	'urlManager'=>array(
		.....
		'rules'=>array(
			.....
			'page/<name>-<id:\d+>.html'=>'cms/node/page',
		),
	'cms'=>array('class'=>'cms.components.Cms'),
),
~~~

###Configuration

The cms application component supports the following configuration parameters:
~~~
[php]
'cms'=>array(
	// the names of the web users with access to the cms
	'users'=>array('admin'),
	// the langauges enabled for the cms
	'languages'=>array('en_us'=>'English'),
	// the default language
	'defaultLanguage'=>'en_us',
	// the types of files that can be uploaded as attachments
	'allowedFileTypes'=>'jpg, gif, png',
	// the path to save the attachments
	'attachmentPath'=>'/files/cms/attachments/',
	// the template to use for node headings
	'headingTemplate'=>'<h1 class="heading">\{heading\}</h1>',
	// the template to use for widget headings
	'widgetHeadingTemplate'=>'<h3 class="heading">\{heading\}</h3>',
	// the template to use for page titles
	'pageTitleTemplate'=>'\{title\} | \{appName\}',
	// the name of the error flash message categories
	'flashError'=>'error',
	'flashInfo'=>'info',
	'flashSuccess'=>'success',
	'flashWarning'=>'warning',
),
~~~

##Usage

###Creating a page

Pages are created by linking to them. To create a page add the following to one of your views:

~~~
[php]
Yii::app()->cms->createUrl('foo');
~~~

The above code creates a node with the name 'foo' (if it doesn't already exist) and returns the URL to that node.

You can also set the following page properties: URL, page title, meta title, meta description, meta keywords.

###Creating a block

Blocks are created using the CmsBlock widget. To add a block add the following to one of your views:

~~~
[php]
<?php $this->widget('cms.widgets.CmsBlock',array('bar')) ?>
~~~

###Adding content to a node

Nodes have a set of properties that can be specified per language:

* Heading - _the main heading_
* Body - _the content_
* Stylesheet - _stylesheet associated with the content_
* URL - _the page URL (page/{url}-{id}.html)_
* Page Title - _the page title_
* Meta Title - _the page meta title_
* Meta Description - _the page meta description_
* Meta Keywords - _the page meta keywords_

Please note that the page properties are only used with pages.

###Editing content

You can use various tags within the node body:

* {heading} - _the main heading_
* {image:id} - _displays an attached image_
* {attachment:id} - _a link to an attached file_
* {node:name} - _displays an inline node

Please note that you cannot render inline nodes using the block widget.

##What's next?

* ?

##Changes

##Dec ?, 2011
* Initial release