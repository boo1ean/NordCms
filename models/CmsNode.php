<?php
/**
 * CmsNode class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms
 */

Yii::import('cms.components.CmsActiveRecord');

/**
 * This is the model class for table "cms_node".
 *
 * The following properties are available in this model:
 * @property integer $id
 * @property string $created
 * @property string $updated
 * @property integer $parentId
 * @property string $name
 * @property string $level
 * @property integer $published
 * @property integer $deleted
 *
 * The following relations are available for this model:
 * @property CmsNode $parent the parent node
 * @property CmsNode[] $children the children nodes
 * @property CmsContent $content the content model for the current language
 * @property CmsContent $default the content model for the default language
 * @property CmsContent[] $translations the related content models
 */
class CmsNode extends CmsActiveRecord
{
	const LEVEL_BLOCK = 'block';
	const LEVEL_PAGE = 'page';

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
			array('id, parentId, published, deleted', 'numerical', 'integerOnly'=>true),
			array('name, level', 'length', 'max'=>255),
			array('updated', 'safe'),
			array('id, created, updated, parentId, name, deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules
	 */
	public function relations()
	{
		return array(
			'parent'=>array(self::BELONGS_TO, 'CmsNode', 'parentId'),
			'children'=>array(self::HAS_MANY, 'CmsNode', 'parentId'),
			'translations'=>array(self::HAS_MANY, 'CmsContent', 'nodeId'),
			'content'=>array(self::HAS_ONE, 'CmsContent', 'nodeId',
					'condition'=>'locale=:locale', 'params'=>array(':locale'=>Yii::app()->language)),
			'default'=>array(self::HAS_ONE, 'CmsContent', 'nodeId',
					'condition'=>'locale=:locale', 'params'=>array(':locale'=>Yii::app()->cms->defaultLanguage)),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '#',
			'created' => Yii::t('CmsModule.core', 'Created'),
			'updated' => Yii::t('CmsModule.core', 'Updated'),
			'name' => Yii::t('CmsModule.core', 'Name'),
			'parentId' => Yii::t('CmsModule.core', 'Parent'),
			'level' => '',
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
		$criteria->compare('parentId',$this->updated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the parent select options formatted as a tree.
	 * @return array the options
	 */
	public function getParentOptionTree()
	{
		$nodes = CmsNode::model()->findAll();

		if (!$this->isNewRecord)
		{
			$children = $this->getChildren($nodes, true);
			$exclude = CMap::mergeArray(array($this->id), array_keys($children));
			$nodes = CmsNode::model()->findAll('id NOT IN (:exclude)', array(':exclude'=>implode(',', $exclude)));
		}

		$tree = $this->getTree($nodes);

		$options = array('0' => Yii::t('CmsModule.core', 'No parent'));
		foreach ($tree as $branch)
			$options = CMap::mergeArray($options, $this->getParentOptionBranch($branch));

		return $options;
	}

	/**
	 * Returns a single branch of the parent select option tree.
	 * @param array $branch the branch
	 * @param int $depth the depth of the branch
	 * @return array the options
	 */
	protected function getParentOptionBranch($branch, $depth = 0)
	{
		$options = array();

		$options[$branch['model']->id] = str_repeat('...', $depth + 1).' '.$branch['model']->name;

		if (!empty($branch['children']))
			foreach ($branch['children'] as $leaf)
				$options = CMap::mergeArray($options, $this->getParentOptionBranch($leaf, $depth + 1));

		return $options;
	}

	/**
	 * Returns the given nodes as a tree.
	 * @param CmsNode[] $nodes the nodes to process
	 * @param bool $includeOrphans indicated whether to include nodes which parent has been deleted.
	 * @return array the tree
	 */
	public function getTree($nodes, $includeOrphans = false)
	{
		$tree = $this->getBranch($nodes);

		// Get the orphan nodes as well (i.e. those which parent has been deleted) if necessary.
		if ($includeOrphans)
			foreach($nodes as $node)
				$tree[$node->id] = array('model'=>$node, 'children'=>$this->getBranch($nodes, $node->id));

		return $tree;
	}

	/**
	 * Returns the given nodes as a branch.
	 * @param CmsNode[]$nodes the nodes to process
	 * @param int $parentId the parent id
	 * @return array the branch
	 */
	protected function getBranch(&$nodes, $parentId = 0)
	{
		$children = array();
		/** @var CmsNode $node */
		foreach ($nodes as $idx => $node)
		{
			if ((int) $node->parentId === (int) $parentId)
			{
				$children[$node->id] = array('model'=>$node, 'children'=>$this->getBranch($nodes, $node->id));
				unset($nodes[$idx]);
			}
		}

		return $children;
	}

	/**
	 * Returns the children for this node.
	 * @param CmsNode[] $nodes the nodes to process
	 * @param bool $recursive indicates whether to include grandchildren
	 * @return CmsNode[] the children
	 */
	protected function getChildren(&$nodes, $recursive = false)
	{
		$children = array();

		/** @var CmsNode $node */
		foreach ($nodes as $idx => $node)
		{
			if ((int) $node->parentId === (int) $this->id)
			{
				$children[$node->id] = $node;
				unset($nodes[$idx]);

				if ($recursive)
					$children = CMap::mergeArray($children, $node->getChildren($nodes, $recursive));
			}
		}

		return $children;
	}

	/**
	 * Renders the node tree.
	 */
	public function renderTree()
	{
		$nodes = CmsNode::model()->findAll();
		$tree = $this->getTree($nodes, true);

		echo CHtml::openTag('div', array('class'=>'node-tree'));
		echo CHtml::openTag('ul', array('class'=>'root'));

		foreach ($tree as $branch)
			$this->renderBranch($branch);

		echo '</ul>';
		echo '</div>';
	}

	/**
	 * Renders a single branch in the node tree.
	 * @param array $branch the branch
	 */
	protected function renderBranch($branch)
	{
		echo '<li>';
		echo CHtml::link($branch['model']->name, array('node/update','id'=>$branch['model']->id));

		if (!empty($branch['children']))
		{
			echo CHtml::openTag('ul', array('class'=>'branch'));

			foreach ($branch['children'] as $leaf)
				$this->renderBranch($leaf);

			echo '</ul>';
		}

		echo '</li>';
	}

	/**
	 * Returns the level select options.
	 * @return array the options
	 */
	public function getLevelOptions()
	{
		return array(
			self::LEVEL_BLOCK=>Yii::t('CmsModule.core','Block'),
			self::LEVEL_PAGE=>Yii::t('CmsModule.core','Page'),
		);
	}

	/**
	 * Creates content for this node.
	 * @param string $locale the locale id, e.g. 'en_us'
	 * @return CmsContent the content model
	 */
	public function createContent($locale)
	{
		$content = new CmsContent();
		$content->nodeId = $this->id;
		$content->locale = $locale;
		$content->save();
		return $content;
	}

	/**
	 * Returns the associated content in a specific language.
	 * @param string $locale the locale id, e.g. 'en_us'
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
	 * Returns the breadcrumb text for this node.
	 * @param boolean $link indicates whether to return the breadcrumb as a link
	 * @return string the breadcrumb text
	 */
	public function getBreadcrumbs($link = false)
	{
		$breadcrumbs = array();

		if ($this->parent !== null)
			$breadcrumbs = $this->parent->getBreadcrumbs(true); // get the parent as a link
		else
		{
			// Do not include the module breadcrumbs for pages.
			if (Yii::app()->controller->route !== 'cms/node/page')
			{
				$breadcrumbs[Yii::t('CmsModule.core','Cms')] = array('admin/index');
				$breadcrumbs[Yii::t('CmsModule.core','Nodes')] = array('node/index');
			}
		}

		if ($this->content !== null && !empty($this->content->breadcrumb))
			$text = $this->content->breadcrumb;
		else if ($this->default !== null && !empty($this->default->breadcrumb))
			$text = $this->default->breadcrumb;
		else
			$text = ucfirst($this->name);

		if ($link)
			$breadcrumbs[$text] = $this->getUrl();
		else
			$breadcrumbs[] = $text;

		return $breadcrumbs;
	}

	/**
	 * Returns the URL for this node.
	 * @param array $params additional GET parameters (name=>value)
	 * @return string the URL
	 */
	public function getUrl($params = array())
	{
		return Yii::app()->createUrl('cms/node/page',
				CMap::mergeArray($params, array('id'=>$this->id, 'name'=>$this->getContentUrl())));
	}

	/**
	 * Returns the absolute URL for this model.
	 * @param array $params additional GET parameters (name=>value)
	 * @return string the URL
	 */
	public function getAbsoluteUrl($params = array())
	{
		return Yii::app()->createAbsoluteUrl('cms/node/page',
				CMap::mergeArray($params, array('id'=>$this->id, 'name'=>$this->getContentUrl())));
	}

	/**
	 * Returns the SEO optimized name of this node.
	 * @return string the name
	 */
	public function getContentUrl()
	{
		if ($this->content !== null && !empty($this->content->url))
		    $url = $this->content->url;
	    else if ($this->default !== null && !empty($this->default->url))
		    $url = $this->default->url;
	    else
		    $url = ucfirst($this->name);

		return $url;
	}

    /**
     * Returns the heading for this node.
     * @return string the heading
     */
    public function getHeading()
    {
	    if ($this->content !== null && !empty($this->content->heading))
		    $heading = $this->content->heading;
	    else if ($this->default !== null && !empty($this->default->heading))
		    $heading = $this->default->heading;
	    else
		    $heading = ucfirst($this->name);

	    return $heading;
    }

    /**
     * Returns the body for this node.
     * @return string the body
     */
    public function getBody()
    {
	    if ($this->content !== null && !empty($this->content->body))
            $body = $this->content->body;
        else if ($this->default !== null && !empty($this->default->body))
            $body = $this->default->body;
        else
            $body = '';

        return $body;
    }

	/**
	 * Returns the page title for this node.
	 * @return string the page title
	 */
	public function getPageTitle()
	{
		if ($this->content !== null && !empty($this->content->pageTitle))
	        $pageTitle = $this->content->pageTitle;
	    else if ($this->default !== null && !empty($this->default->pageTitle))
	        $pageTitle = $this->default->pageTitle;
	    else
	        $pageTitle = ucfirst($this->name);

		return $pageTitle;
	}

	/**
	 * Renders this node.
	 * @return string the rendered node
	 */
	public function render()
	{
		return Yii::app()->cms->renderer->render($this);
	}

	/**
	 * Renders this node as a widget.
	 * @return string the rendered widget
	 */
	public function renderWidget()
	{
		return Yii::app()->cms->renderer->renderWidget($this);
	}

	public function getPublished()
	{
		return (bool) $this->published;
	}
}
