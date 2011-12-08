<?php $this->breadcrumbs = array(
	$model->heading=>$model->getUrl(),
	Yii::t('CmsModule.core','Update'),
) ?>

<div class="cms node-update">

	<div class="row">

		<div class="span12">

			<div class="form">

				<h1><?php echo Yii::t('CmsModule.core','Update node') ?></h1>

				<?php $form = $this->beginWidget('ext.bootstrap.widgets.BootActiveForm',array(
					'id'=>'cmsNodeForm',
					//'enableAjaxValidation'=>true,
					'stacked'=>true,
					'htmlOptions'=>array('enctype'=>'multipart/form-data'),
				)) ?>

					<?php $tabs = array();
					foreach ($translations as $locale => $content) {
						$language = Yii::app()->cms->languages[$locale];
						$tab = $this->renderPartial('_form',array(
							'model'=>$content,
							'form'=>$form,
							'node'=>$model,
							'language'=>$language,
						), true);
						$tabs[$language] = $tab;
					} ?>

					<?php $this->widget('ext.bootstrap.widgets.BootTabs',array(
						'tabs'=>$tabs,
					)); ?>

					<div class="actions clearfix">
						<div class="pull-left">
							<?php echo CHtml::submitButton(Yii::t('CmsModule.core','Save'),array('class'=>'btn primary')) ?>
						</div>
						<div class="pull-right">
							<?php echo CHtml::link(Yii::t('CmsModule.core','Delete'),array('delete','id'=>$model->id),array('class'=>'btn')) ?>
						</div>
					</div>

				<?php $this->endWidget() ?>

			</div>

		</div>

		<div class="span4">

			<div class="sidebar">

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