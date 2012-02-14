<div class="cms boot">

	<div class="node-page">

		<div class="node-content"><?php echo $content ?></div>

		<?php if (Yii::app()->cms->checkAccess()): ?>
			<?php echo CHtml::link('<i class="icon-pencil"></i> '.Yii::t('CmsModule.core','Update'),
					array('node/update', 'id'=>$model->id), array('class'=>'btn update-link', 'title'=>Yii::t('CmsModule.core', 'Update'))) ?>
		<?php endif ?>

	</div>

</div>