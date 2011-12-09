<fieldset>

    <div class="row">
        <?php echo $form->label($node,'name') ?>
        <span class="uneditable-input"><?php echo CHtml::encode($node->name) ?></span><br />
		<span class="hint"><?php echo Yii::t('CmsModule.core','Node name cannot be changed.') ?></span>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'['.$model->locale.']heading') ?>
		<?php echo $form->textField($model,'['.$model->locale.']heading') ?>
		<?php echo $form->error($model,'['.$model->locale.']heading') ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'['.$model->locale.']body') ?>
        <?php $this->widget('cms.widgets.markitup.CmsMarkItUp',array(
            'model'=>$model,
            'attribute'=>'['.$model->locale.']body',
            'set'=>'html',
            'htmlOptions'=>array('id'=>'body-'.$model->locale),
        )) ?>
        <?php echo $form->error($model,'['.$model->locale.']body') ?>
		<div class="tags">
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

	<div class="row">
		<?php echo $form->labelEx($model,'['.$model->locale.']css') ?>
		<?php echo $form->textArea($model,'['.$model->locale.']css',array('rows'=>6)) ?>
		<?php echo $form->error($model,'['.$model->locale.']css') ?>
	</div>

</fieldset>

<fieldset>

    <legend><?php echo Yii::t('CmsModule.core', 'Page settings') ?></legend>

	<p class="hint"><?php echo Yii::t('CmsModule.core','Please note that the fields below are only used with pages.') ?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'['.$model->locale.']url') ?>
		<?php echo $form->textField($model,'['.$model->locale.']url') ?>
		<?php echo $form->error($model,'['.$model->locale.']url') ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'['.$model->locale.']pageTitle') ?>
		<?php echo $form->textField($model,'['.$model->locale.']pageTitle') ?>
		<?php echo $form->error($model,'['.$model->locale.']pageTitle') ?>
	</div>


    <div class="row">
        <?php echo $form->labelEx($model,'['.$model->locale.']metaTitle') ?>
        <?php echo $form->textField($model,'['.$model->locale.']metaTitle') ?>
        <?php echo $form->error($model,'['.$model->locale.']metaTitle') ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'['.$model->locale.']metaDescription') ?>
        <?php echo $form->textArea($model,'['.$model->locale.']metaDescription') ?>
        <?php echo $form->error($model,'['.$model->locale.']metaDescription') ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'['.$model->locale.']metaKeywords') ?>
        <?php echo $form->textField($model,'['.$model->locale.']metaKeywords') ?>
        <?php echo $form->error($model,'['.$model->locale.']metaKeywords') ?>
    </div>

</fieldset>

<fieldset>

    <legend><?php echo Yii::t('CmsModule.core', 'Attachments') ?></legend>

    <div class="row">
        <?php $this->widget('zii.widgets.grid.CGridView',array(
            'id'=>'attachments_'.$model->locale,
            'dataProvider'=>$model->getAttachments(),
            'template'=>'{items} {pager}',
            'emptyText'=>Yii::t('CmsModule.core','No attachments found.'),
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
				),
				array(
					'header'=>Yii::t('CmsModule.core', 'Tag'),
					'value'=>'$data->renderTag()',
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
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'['.$model->locale.']attachment') ?>
        <?php echo $form->fileField($model,'['.$model->locale.']attachment') ?>
        <?php echo $form->error($model,'['.$model->locale.']attachment') ?>
    </div>

</fieldset>