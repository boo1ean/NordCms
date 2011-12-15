<?php $this->breadcrumbs = CMap::mergeArray($model->getBreadcrumbs(true), array(Yii::t('CmsModule.core','Update'))) ?>

<div class="node-update">

    <h1><?php echo Yii::t('CmsModule.core','Update :name',array(':name'=>ucfirst($model->name))) ?></h1>

	<?php $form = $this->beginWidget('ext.bootstrap.widgets.BootActiveForm',array(
		'id'=>'cmsUpdateNodeForm',
		//'enableAjaxValidation'=>true,
		'stacked'=>true,
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)) ?>

		<fieldset class="form-node">

			<?php echo $form->uneditableRow($model,'name',array('hint'=>Yii::t('CmsModule.core','Node name cannot be changed.'))) ?>

			<?php echo $form->dropDownListRow($model,'parentId',$model->getParentOptionTree()) ?>

		</fieldset>

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
				<?php echo CHtml::link(Yii::t('CmsModule.core','Delete'),array('delete','id'=>$model->id),array(
					'class'=>'btn',
					'confirm'=>Yii::t('CmsModule.core','Are you sure you want to delete this node?'),
				)) ?>
			</div>
		</div>

	<?php $this->endWidget() ?>

</div>