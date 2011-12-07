<div class="cms node-block">

	<div class="node-content"><?php echo $content ?></div>

	<?php if (Yii::app()->cms->checkAccess()): ?>
		<?php echo CHtml::link(Yii::t('CmsModule.core', 'Update'),
				array('cms/node/update', 'id'=>$model->id), array('class'=>'update-link')) ?>
	<?php endif ?>

</div>