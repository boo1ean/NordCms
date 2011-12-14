<?php $this->beginContent(Yii::app()->cms->appLayout) ?>

<div class="cms bp">

	<div class="clearfix">

		<div class="span-18">

			<div class="cms-content">

				<?php echo $content ?>

			</div>

		</div>

		<div class="span-6 last">

			<div class="cms-sidebar">

				<h3><?php echo Yii::t('CmsModule.core','Nodes') ?></h3>
				
				<?php echo CmsNode::model()->renderTree() ?>

			</div>

		</div>

	</div>

</div>

<?php $this->endContent() ?>