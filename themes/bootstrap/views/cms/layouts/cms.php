<?php $this->beginContent(Yii::app()->cms->appLayout) ?>

<div class="cms boot">

	<div class="row">

		<div class="span12">

			<div class="cms-content">

				<?php echo $content ?>

			</div>

		</div>

		<div class="span4">

			<div class="cms-sidebar">

				<h3><?php echo Yii::t('CmsModule.core','Nodes') ?></h3>
				
				<?php echo CmsNode::model()->renderTree() ?>

				<p><?php echo CHtml::link(Yii::t('CmsModule.core','Create a new node'),array('node/create')) ?></p>

			</div>

		</div>

	</div>

</div>

<?php $this->endContent() ?>