<?php $this->breadcrumbs = array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Create node')
) ?>

<div class="node-create form">

    <h1><?php echo Yii::t('CmsModule.core','Create node') ?></h1>

	<?php $form = $this->beginWidget('CActiveForm',array(
		'id'=>'cmsCreateNodeForm',
		'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	)) ?>

		<div class="row">
	        <?php echo $form->label($model,'name') ?>
	        <?php echo $form->textField($model,'name') ?>
			<span class="hint"><?php echo Yii::t('CmsModule.core','Node name cannot be changed after creation.') ?></span>
	    </div>

		<div class="row">
			<?php echo $form->label($model,'parentId') ?>
			<?php echo $form->dropDownList($model,'parentId',$model->getParentOptionTree()) ?>
			<?php echo $form->error($model,'parentId') ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton(Yii::t('CmsModule.core', 'Create')) ?>
		</div>

	<?php $this->endWidget() ?>

</div>