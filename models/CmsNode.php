<?php
/**
 * CmsNode class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.models
 */

/**
 * This is the model class for table "cms_node".
 *
 * The following properties are available in this model:
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property string $name
 * @property integer $deleted
 *
 * The following relations are available for this model:
 * @property CmsContent $content
 * @property CmsContent[] $translations
 */
Yii::import('cms.components.CmsActiveRecord');
class CmsNode extends CmsActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className the class name
	 * @return CmsNode the static model class
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
		return 'cms_node';
	}

	/**
	 * @return array validation rules for model attributes
	 */
	public function rules()
	{
		return array(
			array('deleted', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('updated', 'safe'),
			array('id, created, updated, name, deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules
	 */
	public function relations()
	{
		return array(
			'translations'=>array(self::HAS_MANY, 'CmsContent', 'nodeId'),
			'content'=>array(self::HAS_ONE, 'CmsContent', 'nodeId',
					'condition'=>'locale=:locale', 'params'=>array(':locale'=>Yii::app()->language)),
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
			'updated' => Yii::t('CmsModule.core', 'Updated'),
			'name' => Yii::t('CmsModule.core', 'Name'),
			'deleted' => Yii::t('CmsModule.core', 'Deleted'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('deleted',$this->deleted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Renders this node.
	 * @return string the rendered node.
	 */
	public function render()
	{
		$title = str_replace('{title}', $this->title, Yii::app()->cms->titleTemplate);
		$content = str_replace('{title}', $title, $this->body);
		$content = $this->renderNodes($content);
		$content = $this->renderAttachments($content);

		return $content;
	}

	/**
	 * Renders this node as a widget.
	 * @return string the rendered node.
	 */
	public function renderWidget()
	{
		$title = str_replace('{title}', $this->title, Yii::app()->cms->widgetTitleTemplate);
		$content = str_replace('{title}', $title, $this->body);
		$content = preg_replace('/{node:(\w+)}/i', '', $content); // widgets do not render inline nodes
		$content = $this->renderAttachments($content);

		return $content;
	}

	/**
	 * Renders nodes within this node.
	 * @param $content the content being rendered.
	 * @return string the rendered content.
	 */
	protected function renderNodes($content)
	{
		$matches = array();
		preg_match_all('/{node:(\w+)}/i', $content, $matches);

		$nodes = array();
		foreach ($matches[1] as $name)
		{
			/** @var CmsNode $node */
			$node = Yii::app()->cms->loadNode($name);
			if ($node instanceof CmsNode)
				$nodes[$name] = $node->renderWidget();
		}

		foreach ($nodes as $name => $replace)
			$content = preg_replace('/{node:'.$name.'}/i', $replace, $content);

		return $content;
	}

	/**
	 * Renders attachments within this node.
	 * @param $content the content being rendered.
	 * @return string the rendered content.
	 */
	protected function renderAttachments($content)
	{
		$matches = array();
		preg_match_all('/{attachment:(\d+)}/i', $content, $matches);

		$attachments = array();
		foreach ($matches[1] as $id)
		{
			/** @var CmsAttachment $attachment */
			$attachment = CmsAttachment::model()->findByPk($id);
			if ($attachment instanceof CmsAttachment)
			{
				$url = $attachment->createUrl();
				$name = $attachment->resolveName();
				$attachments[$id] = strpos($attachment->mimeType, 'image') !== false
						? '<img src="'.$url.'" alt="'.$name.'" />'
						: '<a href="'.$url.'">'.$name.'</a>';
			}
		}

		foreach ($attachments as $id => $replace)
			$content = preg_replace('/{attachment:'.$id.'}/i', $replace, $content);

		return $content;
	}

	/**
	 * Returns the associated content in a specific language.
	 * @param $locale the locale id, e.g. 'en_us'
	 * @return CmsContent the content model
	 */
	public function getContent($locale)
	{
		return CmsContent::model()->findByAttributes(array(
			'nodeId'=>$this->id,
			'locale'=>$locale,
		));
	}

	/**
	 * Returns the URL for this node.
	 * @param array $params additional GET parameters (name=>value)
	 * @return string the URL
	 */
	public function getUrl($params = array())
	{
		return Yii::app()->createUrl('cms/node/page',
				CMap::mergeArray($params, array('id'=>$this->id, 'name'=>$this->seoName)));
	}

	/**
	 * Returns the absolute URL for this model.
	 * @param array $params additional GET parameters (name=>value)
	 * @return string the URL
	 */
	public function getAbsoluteUrl($params = array())
	{
		return Yii::app()->createAbsoluteUrl('cms/node/page',
				CMap::mergeArray($params, array('id'=>$this->id, 'name'=>$this->seoName)));
	}

	/**
	 * Returns the SEO optimized name of this node.
	 * @return string the name
	 */
	public function getSeoName()
	{
		return $this->content !== null && !empty($this->content->url) ? urldecode($this->content->url) : $this->name;
	}

    /**
     * Returns the title for this node.
     * @return string the title
     */
    public function getTitle()
    {
        return $this->content !== null && !empty($this->content->title) ? $this->content->title : $this->name;
    }

    /**
     * Returns the body for this node.
     * @return string the body
     */
    public function getBody()
    {
        return $this->content !== null && !empty($this->content->body) ? $this->content->body : '';
    }
}