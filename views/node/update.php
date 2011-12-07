<?php $this->breadcrumbs=array(
	$model->title=>$model->getUrl(),
	Yii::t('CmsModule.core','Update'),
) ?>

<div class="cms node-update form">

	<div class="span-18">

		<h1><?php echo Yii::t('CmsModule.core','Update node') ?></h1>

		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'cmsNodeForm',
			//'enableAjaxValidation'=>true,
			'htmlOptions'=>array('enctype'=>'multipart/form-data')
		)); ?>

			<?php $tabs = array();
			foreach ($translations as $locale=>$content) {
				$language = Yii::app()->cms->languages[$locale];
				$tab = array(
					'content'=>$this->renderPartial('_form', array(
						'model'=>$content,
						'form'=>$form,
						'node'=>$model,
						'language'=>$language,
					), true),
				);
				$tabs[$language] = $tab;
			} ?>

			<?php $this->widget('zii.widgets.jui.CJuiTabs', array(
				'headerTemplate'=>'<li><a href="{url}">{title}</a></li>',
				'tabs'=>$tabs,
			)); ?>

			<div class="row buttons">
				<div class="pull-left">
					<?php echo CHtml::submitButton(Yii::t('CmsModule.core', 'Save')) ?>
				</div>
				<div class="pull-right">
					<?php echo CHtml::link(Yii::t('CmsModule.core','Delete'),array('delete','id'=>$model->id)) ?>
				</div>
			</div>

		<?php $this->endWidget() ?>

	</div>

	<div class="span-5 last">

		<div class="sidebar">

			<h3><?php echo Yii::t('CmsModule.core','Nodes') ?></h3>

			<ul class="nodes">
				<?php foreach (CmsNode::model()->findAll() as $node): ?>
					<li><?php echo CHtml::link($node->name, array('node/update','id'=>$node->id)) ?></li>
				<?php endforeach ?>
			</ul>

		</div>

	</div>

</div><!-- form -->