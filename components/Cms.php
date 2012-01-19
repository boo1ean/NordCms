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
	 * @var array the HTML purifier options.
	 */
	public $htmlPurifierOptions = array();
	// todo: do something about the flash message categories, an array maybe instead of 4 properties?
	/**
	 * @var string the flash message error category.
	 */
	public $flashError = 'error';
	/**
	 * @var string the flash message info category.
	 */
	public $flashInfo = 'info';
	/**
	 * @var string the flash message success category.
	 */
	public $flashSuccess = 'success';
	/**
	 * @var string the flash message warning category.
	 */
	public $flashWarning = 'warning';

    protected $_assetsUrl;

    /**
     * Initializes the component.
     */
    public function init()
    {
        parent::init();

		// Create the renderer.
		$this->renderer = Yii::createComponent($this->renderer);

		// Register the assets.
		$assetsUrl = $this->getAssetsUrl();
        Yii::app()->clientScript->registerCssFile($assetsUrl.'/css/cms.css');
        Yii::app()->clientScript->registerScriptFile($assetsUrl.'/js/es5-shim.min.js');
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

		if ($node === null)
		{
			$this->createNode($name);
			$node = $this->loadNode($name);
		}

		return $node->getUrl($params);
	}

	/**
	 * Loads a node model.
	 * @param string $name the node name
	 * @return CmsNode the model
	 */
	public function loadNode($name)
	{
		return CmsNode::model()->findByAttributes(array('name'=>$name));
	}

	/**
	 * Creates a new node model.
	 * @param string $name the node name
	 */
	public function createNode($name)
	{
        // Validate the node name before creation.
        if (preg_match('/^[\w\d\._-]+$/i', $name) === 0)
            throw new CException(__CLASS__.': Failed to create node. Name "'.$name.'" is invalid.');

		$node = new CmsNode();
		$node->name = $name;
		$node->save(false);
	}

	/**
	 * Returns whether a specific page is active.
	 * @param $name the content name
	 * @return boolean
	 */
	public function isActive($name)
	{
		$node = $this->loadNode($name);
		$controller = Yii::app()->getController();
		return $controller->module !== null
				&& $controller->module->id === 'cms'
				&& $controller->id === 'node'
				&& $controller->action->id === 'page'
				&& isset($_GET['id']) && $_GET['id'] === $node->id;
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
