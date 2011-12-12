<?php $this->beginContent(Yii::app()->cms->appLayout) ?>

<div class="cms">

	<div class="row">

		<div class="span12">

			<div class="cms-content">

				<?php echo $content ?>

			</div>

		</div>

		<div class="span4">

			<div class="cms-sidebar">

				<h3><?php echo Yii::t('CmsModule.core','Nodes') ?></h3>
				
				<ul class="nodes">
					<?php foreach (CmsNode::model()->findAll() as $node): ?>
						<li><?php echo CHtml::link($node->name, array('node/update','id'=>$node->id)) ?></li>
					<?php endforeach ?>
				</ul>

			</div>

		</div>

	</div>

</div>

<?php $this->endContent() ?>