_Current version 0.9.0_

CMS is a stand-alone module that provide the core CMS functionality such as multilingual content to any Yii project. CMS have been developed under the New BSD License, please see the LICENSE file.

##Links

* [Try out the Demo](http://www.cniska.net/cmsdemo)
* [Discussion](http://www.yiiframework.com/forum/index.php?/topic/26809-extension-nordcms)
* [Report an issue](https://bitbucket.org/NordLabs/nordcms/issues/new)
* [Fork on Bitbucket](https://bitbucket.org/NordLabs/nordcms)

##What's included?

* In place updating of nodes
* Rendering of nodes as pages and widgets
* Attaching of image and other files to nodes
* Multilingual content
* Search engine optimization for pages
* Support for meta tags for pages
* Theme for projects using Twitter's Bootstrap library

##Setup

Unzip the extension under protected/modules/cms and add the following to your application configuration:

~~~
[php]
'imports'=>array(
	.....
	'application.modules.cms.CmsModule',
),
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
	),
	'cms'=>array(
		'class'=>'cms.components.Cms'
	),
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
	// the types of files that can uploaded as attachments
	'allowedFileTypes'=>'jpg, gif, png',
	// the maximum allowed filesize for attachments
	'allowedFileSize'=>1024,
	// the path to save the attachments
	'attachmentPath'=>'/files/cms/attachments/',
	// the template to use for node headings
	'headingTemplate'=>'<h1 class="heading">\{heading\}</h1>',
	// the template to use for widget headings
	'widgetHeadingTemplate'=>'<h3 class="heading">\{heading\}</h3>',
	// the template to use for page titles
	'pageTitleTemplate'=>'\{title\} | \{appName\}',
	// the application layout to use with the cms
	'appLayout'=>'application.views.layouts.main',
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

What the above code does it creates a node with the name 'foo' (if it doesn't already exist) and returns the URL to that node.

You can also set the following page properties: URL, page title, meta title, meta description, meta keywords.

###Creating a block

Blocks are used for displaying Cms content within views and they can be created using the CmsBlock widget. To add a block, add the following code to one of your views:

~~~
[php]
<?php $this->widget('cms.widgets.CmsBlock',array('bar')) ?>
~~~

###Adding content to a node

If you have permissions to update Cms content an 'Update' link will be displayed below the content. Nodes have a set of properties that can be specified per language:

* Heading - _the main heading_
* Body - _the content_
* Stylesheet - _stylesheet associated with the content_
* URL - _the page URL (page/{url}-{id}.html)_
* Page Title - _the page title_
* Breadcrumb - _the breadcrumb text_
* Meta Title - _the page meta title_
* Meta Description - _the page meta description_
* Meta Keywords - _the page meta keywords_

Please note that the page properties are only used with pages.

It is possible to create relations between nodes by setting the parent. This will help you organize your content and it will also automatically set the correct breadcrumbs.

###Editing content

You can use various tags within the body-field:

* {{heading}} - _the main heading_
* {{image:id}} - _displays an attached image_
* {{node:name}} - _displays an inline node
* {{file:id}} - _creates a link to an attached file_
* {{email:address}} - _creates a mailto link_
* {{name|text}} - _creates an internal link_
* {{address|text}} - _creates an external link_

Please note that you cannot render inline nodes using the block widget.

##What's next?

* ?

##Changes

##Dec 15, 2011
* Initial release