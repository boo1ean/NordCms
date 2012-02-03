<?php $this->breadcrumbs = array(
	Yii::t('CmsModule.core','Cms')=>array('admin/index'),
	Yii::t('CmsModule.core','Nodes'),
) ?>

<div class="node-index">

	<h1><?php echo Yii::t('CmsModule.core','Nodes'); ?></h1>

	<p><?php echo CHtml::link(Yii::t('CmsModule.core','Create a new node'),array('node/create'),array('class'=>'btn')) ?></p>

	<?php $this->widget('bootstrap.widgets.BootGridView',array(
		'dataProvider'=>$model->search(),
		'columns'=>array(
			'id',
			'name',
			array(
				'name'=>'parentId',
				'value'=>'$data->parent !== null ? $data->parent->name : ""',
			),
			array(
				'class'=>'BootButtonColumn',
				'viewButtonUrl'=>'Yii::app()->cms->createUrl($data->name)',
			),
		),
	)) ?>

</div>