<?php $this->breadcrumbs = array(
	Yii::t('CmsModule.core','Cms'),
) ?>

<div class="admin-index">

	<h1><?php echo Yii::t('CmsModule.core','Cms'); ?></h1>

	<div class="nodes">
		<h2><?php echo CHtml::link(Yii::t('CmsModule.core','Nodes'),array('node/index')); ?></h2>
	</div>

	<!--
	<div class="menus">
		<h2><?php echo Yii::t('CmsModule.core','Menus'); ?></h2>
	</div>
	-->

</div>