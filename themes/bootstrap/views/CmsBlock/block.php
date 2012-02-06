<div class="cms node-block">

	<div class="node-content"><?php echo $content ?></div>

	<?php if (Yii::app()->cms->checkAccess()): ?>
		<?php echo CHtml::link('<i class="icon-pencil"></i> '.Yii::t('CmsModule.core','Update'),
				array('/cms/node/update', 'id'=>$model->id), array('class'=>'btn small update-link', 'title'=>Yii::t('CmsModule.core', 'Update'))) ?>
	<?php endif ?>

</div>