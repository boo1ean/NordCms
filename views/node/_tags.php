<p><strong><?php echo Yii::t('CmsModule.core','Available tags'); ?></strong></p>
<ul>
	<li><strong>{{heading}}</strong> &mdash; <em><?php echo Yii::t('CmsModule.core','displays the page heading'); ?></em></li>
	<li><strong>{{node:name}}</strong> &mdash; <em><?php echo Yii::t('CmsModule.core','displays an inline node'); ?></em></li>
	<li><strong>{{image:id}}</strong> &mdash; <em><?php echo Yii::t('CmsModule.core','displays an attached image'); ?></em></li>
	<li><strong>{{file:id}}</strong> &mdash; <em><?php echo Yii::t('CmsModule.core','creates a link to an attached file'); ?></em></li>
	<li><strong>{{name|text}}</strong> &mdash; <em><?php echo Yii::t('CmsModule.core','creates an internal link'); ?></em></li>
</ul>