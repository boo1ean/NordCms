<?php
/**
 * Cms class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

Yii::import('cms.models.*');

/**
 * Cms application component that allows for application-wide access to the cms.
 */
class Cms extends CApplicationComponent
{
	// todo: consider moving the configurations to the module.
	/**
	 * @var array the names of the users that are allowed to updated the cms.
	 */
	public $users = array('admin');
	/**
	 * @var array the languages that content can be translated in.
	 */
	public $languages = array('en_us'=>'English');
	/**
	 * @var string the default locale.
	 */
	public $defaultLanguage = 'en_us';
	/**
	 * @var string the allowed attachment files types.
	 */
	public $allowedFileTypes = 'jpg, gif, png';
    /**
     * @var integer the maximum allowed attachment file size in bytes.
     */
    public $allowedFileSize = 1024;
	/**
	 * @var string the path for saving attached files.
	 */
	public $attachmentPath = '/files/cms/attachments/';
	/**
	 * @var array the flash message categories.
	 */
	public $flashes = array();
	/**
	 * @var string the template to use for node headings.
	 */
	public $headingTemplate = '<h1 class="heading">{heading}</h1>';
	/**
	 * @var string the template to use for widget headings.
	 */
	public $widgetHeadingTemplate = '<h3 class="heading">{heading}</h3>';
	/**
	 * @var string the template to use for page titles.
	 */
	public $pageTitleTemplate = '{title} - {appName}';
	/**
	 * @var string the application layout to use with the cms.
	 */
	public $appLayout = 'application.views.layouts.main';
	/**
	 * @var array the renderer configuration.
	 */
	public $renderer = array('class'=>'cms.components.CmsBaseRenderer');
	/**
	 * @var boolean indicates whether to auto create nodes when they are requested.
	 * Defaults to true.
	 */
	public $autoCreate = true;
	/**
	 * @var array the HTML purifier options.
	 */
	public $htmlPurifierOptions = array();

    protected $_assetsUrl;
	protected $_flashCategories = array(
		'error'=>'error',
		'info'=>'info',
		'success'=>'success',
		'warning'=>'warning',
	);

    /**
     * Initializes the component.
     */
    public function init()
    {
        parent::init();

	$this->flashes = CMap::mergeArray($this->_flashCategories, $this->flashes);

	// Create the renderer.
	$this->renderer = Yii::createComponent($this->renderer);

	// Register the assets.
	$assetsUrl = $this->getAssetsUrl();
        Yii::app()->clientScript->registerCssFile($assetsUrl.'/css/cms.css');
        Yii::app()->clientScript->registerScriptFile($assetsUrl.'/js/es5-shim.js');
    }

    /**
    * Returns the url to assets publishing the folder if necessary.
    * @return string the assets url
    */
    public function getAssetsUrl()
    {
        if ($this->_assetsUrl !== null)
            return $this->_assetsUrl;
        else
        {
            $assetsPath = Yii::getPathOfAlias('cms.assets');

            if (YII_DEBUG)
                $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, true);
            else
                $assetsUrl = Yii::app()->assetManager->publish($assetsPath);

            return $this->_assetsUrl = $assetsUrl;
        }
    }

	/**
	 * Creates the URL to a content page.
	 * @param string $name the content name
	 * @param array $params additional parameters
	 * @return string the URL
	 */
	public function createUrl($name, $params=array())
	{
		$node = $this->loadNode($name);
		return $node->getUrl($params);
	}

	/**
	 * Loads a node model.
	 * @param string $name the node name
	 * @return CmsNode the model
	 */
	public function loadNode($name)
	{
		$node = CmsNode::model()->findByAttributes(array('name'=>$name));
		return $node;
	}

	/**
	 * Returns whether a specific page is active.
	 * @param string $name the content name
	 * @return boolean the result
	 */
	public function isActive($name)
	{
		$node = $this->loadNode($name);
		$controller = Yii::app()->getController();
		return ($controller->module !== null
				&& $controller->module->id === 'cms'
				&& $controller->id === 'node'
				&& $controller->action->id === 'page'
				&& isset($_GET['id']) && $_GET['id'] === $node->id)
				|| $this->isChildActive($node);
	}

	/**
	 * Returns whether a child node of a specific page is active.
	 * @param CmsNode $node the node
	 * @return boolean the result
	 */
	protected function isChildActive($node)
	{
		foreach ($node->children as $child)
			if ($this->isActive($child->name) || $this->isChildActive($child))
				return true;

		return false;
	}

	/**
	 * Creates a new node model.
	 * @param string $name the node name
	 * @return boolean whether the node was created
	 * @throws CException if the node could not be created
	 */
	protected function createNode($name)
	{
		if (!$this->autoCreate)
			throw new CException(__CLASS__.': Failed to create node. Node creation is disabled.');

		// Validate the node name before creation.
		if (preg_match('/^[\w\d\._-]+$/i', $name) === 0)
			throw new CException(__CLASS__.': Failed to create node. Name "'.$name.'" is invalid.');

		$node = new CmsNode();
		$node->name = $name;
		return $node->save(false);
	}
	
	/**
	 * Returns whether the currently logged in user has access to update cms content.
	 * Override this method to implement your own access control.
	 * @return boolean
	 */
	public function checkAccess()
	{
		return in_array(Yii::app()->user->name, $this->users);
	}
}
