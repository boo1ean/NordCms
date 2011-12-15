<?php
/**
 * CmsContent class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 */

Yii::import('cms.components.CmsActiveRecord');

/**
 * This is the model class for table "cms_content".
 *
 * The following properties are available in this model:
 * @property string $id
 * @property string $nodeId
 * @property string $locale
 * @property string $heading
 * @property string $body
 * @property string $css
 * @property string $url
 * @property string $pageTitle
 * @property string $breadcrumb
 * @property string $metaTitle
 * @property string $metaDescription
 * @property string $metaKeywords
 */
class CmsContent extends CmsActiveRecord
{
	public $attachment;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsContent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cms_content';
	}

	/**
	 * @return array validation rules for model attributes
	 */
	public function rules()
	{
		return array(
			array('nodeId, locale', 'required'),
			array('nodeId', 'length', 'max'=>10),
			array('locale', 'length', 'max'=>50),
			array('heading, url, pageTitle, breadcrumb, metaTitle, metaDescription, metaKeywords', 'length', 'max'=>255),
            array('attachment', 'file', 'types'=>Yii::app()->cms->allowedFileTypes, 'maxSize'=>Yii::app()->cms->allowedFileSize, 'allowEmpty'=>true),
			array('body, css', 'filter', 'filter'=>array($obj = new CHtmlPurifier(), 'purify')),
			array('id, nodeId, locale, heading, url, pageTitle, breadcrumb, metaTitle, metaDescription, metaKeywords', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('CmsModule.core', 'Id'),
			'nodeId' => Yii::t('CmsModule.core', 'Node'),
			'locale' => Yii::t('CmsModule.core', 'Locale'),
			'heading' => Yii::t('CmsModule.core', 'Heading'),
			'body' => Yii::t('CmsModule.core', 'Body'),
			'css' => Yii::t('CmsModule.core', 'Stylesheet'),
			'url' => Yii::t('CmsModule.core', 'URL'),
			'pageTitle' => Yii::t('CmsModule.core', 'Page Title'),
			'breadcrumb' => Yii::t('CmsModule.core', 'Breadcrumb'),
			'metaTitle' => Yii::t('CmsModule.core', 'Meta Title'),
			'metaDescription' => Yii::t('CmsModule.core', 'Meta Description'),
			'metaKeywords' => Yii::t('CmsModule.core', 'Meta Keywords'),
			'attachment' => Yii::t('CmsModule.core', 'Add a new attachment'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('contentId',$this->nodeId,true);
		$criteria->compare('locale',$this->locale,true);
		$criteria->compare('heading',$this->heading,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('css',$this->css,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('pageTitle',$this->pageTitle,true);
		$criteria->compare('breadcrumb',$this->breadcrumb,true);
		$criteria->compare('metaTitle',$this->metaTitle,true);
		$criteria->compare('metaDescription',$this->metaDescription,true);
		$criteria->compare('metaKeywords',$this->metaKeywords,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the attachments associated with this content.
	 * @return CActiveDataProvider the attachments
	 */
	public function getAttachments()
	{
		return new CActiveDataProvider('CmsAttachment', array(
			'criteria' => array(
				'condition' => 'contentId=:contentId',
				'params' => array(':contentId' => $this->id),
			),
		));
	}

	/**
	 * Creates an attachment for this content.
	 * @param CUploadedFile $file the uploaded file instance
	 */
	public function createAttachment($file)
	{
		$attachment = new CmsAttachment();
		$attachment->contentId = $this->id;
		$attachment->extension = strtolower($file->getExtensionName());
		$attachment->filename = $file->getName();
		$attachment->mimeType = $file->getType();
		$attachment->byteSize = $file->getSize();
		$attachment->save();
		$attachment->saveFile($file);
	}
}