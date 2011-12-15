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
 * @property integer $deleted
 *
 * The following relations are available for this model:
 * @property CmsNode $parent
 * @property CmsContent $content
 * @property CmsContent[] $translations
 */
class CmsNode extends CmsActiveRecord
{
	protected $_patterns = array(
		'file'=>'/{{file:([\d]+)}}/i',
		'image'=>'/{{image:([\d]+)}}/i',
		'link'=>'/{{([\w\d\._-]+|https?:\/\/[\w\d_-]*(\.[\w\d_-]*)+.*)\|([\w\d\s-]+)}}/i',
		'email'=>'/{{email:([\w\d!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[\w\d!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[\w\d](?:[\w\d-]*[\w\d])?\.)+[\w\d](?:[\w\d-]*[\w\d])?)}}/i',
		'node'=>'/{{node:([\w\d\._-]+)}}/i',
	);

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
			array('id, parentId, deleted', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
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
			'parentId' => Yii::t('CmsModule.core', 'Parent'),
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
		$criteria->compare('parentId',$this->updated);
		$criteria->compare('deleted',$this->deleted);

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
		$children = $this->getChildren($nodes, true);
		$exclude = CMap::mergeArray(array($this->id), array_keys($children));
		$nodes = CmsNode::model()->findAll('id NOT IN (:exclude)', array(':exclude'=>implode(',', $exclude)));

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
	 * @return array the branch.
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
	 * @param $branch the branch.
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
	 * Renders this node.
	 * @return string the rendered node.
	 */
	public function render()
	{
		$heading = str_replace('{heading}', $this->heading, Yii::app()->cms->headingTemplate);
		$content = $this->renderHeading($heading, $this->body);
		$content = $this->renderLinks($content);
		$content = $this->renderEmails($content);
		$content = $this->renderImages($content);
		$content = $this->renderAttachments($content);
		$content = $this->renderNodes($content);

		return $content;
	}

	/**
	 * Renders this node as a widget.
	 * @return string the rendered node.
	 */
	public function renderWidget()
	{
		$heading = str_replace('{heading}', $this->heading, Yii::app()->cms->widgetHeadingTemplate);
		$content = $this->renderHeading($heading, $this->body);
		$content = $this->renderLinks($content);
		$content = $this->renderEmails($content);
		$content = $this->renderImages($content);
		$content = $this->renderAttachments($content);
		$content = preg_replace($this->_patterns['node'], '', $content); // widgets do not render inline nodes

		return $content;
	}

	/**
	 * Renders the heading for this node.
	 * @param string $heading the heading to render
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderHeading($heading, $content)
	{
		return str_replace('{{heading}}', $heading, $content);
	}

	/**
	 * Renders nodes within this node.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderNodes($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['node'], $content, $matches);

		$nodes = array();
		foreach ($matches[1] as $index => $name)
		{
			/** @var CmsNode $node */
			$node = Yii::app()->cms->loadNode($name);
			if ($node instanceof CmsNode)
				$nodes[$index] = $node->renderWidget();
		}

		if (!empty($nodes))
			$content = strtr($content, array_combine($matches[0], $nodes));

		return $content;
	}

	/**
	 * Renders links within this node.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderLinks($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['link'], $content, $matches);

		$links = array();
		foreach ($matches[1] as $index => $target)
		{
			// If the target doesn't include 'http' it's treated as an internal link.
			if (strpos($target, 'http') === false)
			{
				/** @var Cms $cms */
				$cms = Yii::app()->cms;

				/** @var CmsNode $node */
				$node = $cms->loadNode($target);
				if (!$node instanceof CmsNode)
				{
					$cms->createNode($target);
					$node = $cms->loadNode($target);
				}

				$target = $node->getUrl();
			}

			$text = $matches[3][$index];
			$links[$index] = CHtml::link($text, $target);
		}

		if (!empty($links))
			$content = strtr($content, array_combine($matches[0], $links));

		return $content;
	}

	protected function renderEmails($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['email'], $content, $matches);

		$mails = array();
		foreach ($matches[1] as $index => $id)
		{
			$email = str_rot13($matches[1][$index]);
			$mails[$index] = CHtml::mailto($email, $email, array(
				'class'=>'email',
				'rel'=>'nofollow',
			));
		}

		if (!empty($mails))
		{
			$content = strtr($content, array_combine($matches[0], $mails));

			$assetsUrl = Yii::app()->cms->getAssetsUrl();
			Yii::app()->clientScript->registerScriptFile($assetsUrl.'/js/cms-rot13.js');
			Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->id.'_emailObfuscation', "
				(function($) {
					$('.email').each(function() {
						var element = $(this),
							href = $(this).attr('href'),
							address = Cms.Rot13.decode(href.substring(href.indexOf(':') + 1)),
							value = Cms.Rot13.decode($(this).text());

						element.attr('href', 'mailto:' + address);
						element.text(value);
					});
				})(jQuery);
			");
		}

		return $content;
	}

	/**
	 * Renders images within this node.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderImages($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['image'], $content, $matches);

		$images = array();
		foreach ($matches[1] as $index => $id)
		{
			/** @var CmsAttachment $attachment */
			$attachment = CmsAttachment::model()->findByPk($id);
			if ($attachment instanceof CmsAttachment && strpos($attachment->mimeType, 'image') !== false)
			{
				$url = $attachment->getUrl();
				$name = $attachment->resolveName();
				$images[$index] = CHtml::image($url, $name);
			}
		}

		if (!empty($images))
			$content = strtr($content, array_combine($matches[0], $images));

		return $content;
	}

	/**
	 * Renders attachments within this node.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderAttachments($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['file'], $content, $matches);

		$attachments = array();
		foreach ($matches[1] as $index => $id)
		{
			/** @var CmsAttachment $attachment */
			$attachment = CmsAttachment::model()->findByPk($id);
			if ($attachment instanceof CmsAttachment)
			{
				$url = $attachment->getUrl();
				$name = $attachment->resolveName();
				$attachments[$index] = CHtml::link($name, $url);
			}
		}

		if (!empty($attachments))
			$content = strtr($content, array_combine($matches[0], $attachments));

		return $content;
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

		if ($this->content !== null && !empty($this->content->breadcrumb))
			$text = $this->content->breadcrumb;
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
		return $this->content !== null && !empty($this->content->url) ? urldecode($this->content->url) : $this->name;
	}

    /**
     * Returns the heading for this node.
     * @return string the heading
     */
    public function getHeading()
    {
        return $this->content !== null && !empty($this->content->heading) ? $this->content->heading : ucfirst($this->name);
    }

    /**
     * Returns the body for this node.
     * @return string the body
     */
    public function getBody()
    {
        return $this->content !== null && !empty($this->content->body) ? $this->content->body : '';
    }

	/**
	 * Returns the page title for this node.
	 * @return string the page title
	 */
	public function getPageTitle()
	{
		return $this->content !== null && !empty($this->content->pageTitle) ? $this->content->pageTitle : ucfirst($this->name);
	}
}