<?php
/**
 * CmsAttachment class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 */

Yii::import('cms.components.CmsActiveRecord');

/**
 * This is the model class for table "cms_file".
 *
 * The following are the available columns in this model:
 * @property string $id
 * @property string $created
 * @property string $contentId
 * @property string $extension
 * @property string $filename
 * @property string $mimeType
 * @property string $byteSize
 *
 * The following relations are available for this model:
 * @property CmsNode $owner
 */
class CmsAttachment extends CmsActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsAttachment the static model class
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
		return 'cms_attachment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('contentId, extension, filename, mimeType, byteSize', 'required'),
			array('contentId, byteSize', 'length', 'max'=>10),
			array('extension', 'length', 'max'=>50),
			array('filename, mimeType', 'length', 'max'=>255),
			array('id, created, contentId, extension, filename, mimeType, byteSize', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'owner'=>array(self::BELONGS_TO, 'CmsContent', 'contentId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('CmsModule.core', 'Id'),
			'created' => Yii::t('CmsModule.core', 'Created'),
			'contentId' => Yii::t('CmsModule.core', 'Content'),
			'extension' => Yii::t('CmsModule.core', 'Extension'),
			'filename' => Yii::t('CmsModule.core', 'Filename'),
			'mimeType' => Yii::t('CmsModule.core', 'Mime Type'),
			'byteSize' => Yii::t('CmsModule.core', 'Size (bytes)'),
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
		$criteria->compare('created',$this->created,true);
		$criteria->compare('contentId',$this->contentId,true);
		$criteria->compare('extension',$this->extension,true);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('mimeType',$this->mimeType,true);
		$criteria->compare('byteSize',$this->byteSize,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Saves a file for this attachment.
	 * @param CUploadedFile $file the uploaded path
	 */
	public function saveFile($file)
	{
		$path = realpath(Yii::app()->basePath.'/..').'/'.Yii::app()->cms->attachmentPath;

		if (!file_exists($path))
			mkdir($path, 0777, true);

		$file->saveAs($path.$this->resolveName());
	}

	/**
	 * Returns the URL to this attachment.
	 * @return string the URL
	 */
	public function getUrl()
	{
		return Yii::app()->request->baseUrl.Yii::app()->cms->attachmentPath.$this->resolveName();
	}

	/**
	 * Returns the tag for this attachment.
	 * @return string the tag
	 */
	public function renderTag()
	{
		return strpos($this->mimeType, 'image') !== false ? '{{image:'.$this->id.'}}' : '{{file:'.$this->id.'}}';
	}

	/**
	 * Returns the filename for this attachment.
	 * @return string the filename
	 */
	public function resolveName()
	{
		return substr($this->filename, 0, strrpos($this->filename, '.')).'-'.$this->id.'.'.strtolower($this->extension);
	}
}