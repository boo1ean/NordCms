<fieldset>

	<?php echo $form->uneditableRow($node,'name',array('hint'=>Yii::t('CmsModule.core','Node name cannot be changed.'))) ?>

    <?php echo $form->textFieldRow($model,'['.$model->locale.']heading',array('class'=>'span8')) ?>

    <div class="clearfix">
        <?php echo $form->labelEx($model,'['.$model->locale.']body') ?>
        <?php $this->widget('cms.widgets.markitup.CmsMarkItUp',array(
            'model'=>$model,
            'attribute'=>'['.$model->locale.']body',
            'set'=>'html',
        )) ?>
        <?php echo $form->error($model,'['.$model->locale.']body') ?>
		<div class="tags well">
			<p><strong><?php echo Yii::t('CmsModule.core','Available tags'); ?></strong></p>
			<ul>
				<li><strong>{heading}</strong> &ndash; <em><?php echo Yii::t('CmsModule.core','the page heading'); ?></em></li>
				<li><strong>{image:id}</strong> &ndash; <em><?php echo Yii::t('CmsModule.core','displays an attached image'); ?></em></li>
				<li><strong>{attachment:id}</strong> &ndash; <em><?php echo Yii::t('CmsModule.core','a link to an attached file'); ?></em></li>
				<li><strong>{link:name}</strong> &ndash; <em><?php echo Yii::t('CmsModule.core','a link to another node'); ?></em></li>
				<li><strong>{node:name}</strong> &ndash; <em><?php echo Yii::t('CmsModule.core','displays an inline node'); ?></em></li>
			</ul>
		</div>
    </div>

	<?php echo $form->textAreaRow($model,'['.$model->locale.']css',array('class'=>'span11','rows'=>6)) ?>

</fieldset>

<fieldset>

	<legend><?php echo Yii::t('CmsModule.core','Page settings') ?></legend>
	
	<p class="help-block"><?php echo Yii::t('CmsModule.core','Please note that the fields below are only used with pages.') ?></p>

	<?php echo $form->textFieldRow($model,'['.$model->locale.']url',array('class'=>'span8')) ?>

	<?php echo $form->textFieldRow($model,'['.$model->locale.']pageTitle',array('class'=>'span8')) ?>

    <?php echo $form->textFieldRow($model,'['.$model->locale.']metaTitle',array('class'=>'span8')) ?>

    <?php echo $form->textAreaRow($model,'['.$model->locale.']metaDescription',array('class'=>'span8','rows'=>3)) ?>

    <?php echo $form->textFieldRow($model,'['.$model->locale.']metaKeywords',array('class'=>'span8')) ?>

</fieldset>

<fieldset>

    <legend><?php echo Yii::t('CmsModule.core', 'Attachments') ?></legend>

    <?php $this->widget('ext.bootstrap.widgets.BootGridView',array(
        'id'=>'attachments_'.$model->locale,
        'dataProvider'=>$model->getAttachments(),
        'template'=>'{items} {pager}',
        'emptyText'=>Yii::t('CmsModule.core', 'No attachments found.'),
        'showTableOnEmpty'=>false,
        'columns'=>array(
			array(
				'name'=>'id',
				'header'=>'#',
				'value'=>'$data->id',
			),
			array(
                'header'=>Yii::t('CmsModule.core', 'URL'),
                'value'=>'$data->resolveName()',
				'sortable'=>false,
            ),
			array(
				'header'=>Yii::t('CmsModule.core', 'Tag'),
				'value'=>'$data->renderTag()',
				'sortable'=>false,
			),
            array(
                'class'=>'CButtonColumn',
                'template'=>'{delete}',
                'buttons'=>array(
                    'delete'=>array(
                        'url'=>'Yii::app()->controller->createUrl("deleteAttachment", array("id"=>$data->id))',
                    ),
                ),
            ),
        ),
    )) ?>

    <?php echo $form->fileFieldRow($model,'['.$model->locale.']attachment') ?>

</fieldset>