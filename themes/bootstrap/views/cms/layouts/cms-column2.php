<?php $this->beginContent(Yii::app()->cms->appLayout) ?>

<div class="cms boot column2">

	<div class="row">

		<div class="span9">

			<div class="cms-content">

				<?php echo $content ?>

			</div>

		</div>

		<div class="span3">

			<div class="cms-sidebar">

				<h3><?php echo Yii::t('CmsModule.core','Nodes') ?></h3>
				
				<?php echo CmsNode::model()->renderTree() ?>

				<p><?php echo CHtml::link(Yii::t('CmsModule.core','Create a new node'),array('node/create'),array('class'=>'btn btn-small')) ?></p>

			</div>

		</div>

	</div>

</div>

<?php $this->endContent() ?>