<div class="cms boot">

	<div class="node-page">

		<div class="node-content"><?php echo $content ?></div>

		<?php if (Yii::app()->cms->checkAccess()): ?>
			<?php echo CHtml::link(Yii::t('CmsModule.core', 'Update'),
					array('node/update', 'id'=>$model->id), array('class'=>'btn small update-link')) ?>
		<?php endif ?>

	</div>

</div>