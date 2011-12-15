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
	/**
	 * @property array the names of the users that are allowed to updated the cms.
	 */
	public $users = array('admin');
	/**
	 * @property array the languages that content can be translated in.
	 */
	public $languages = array('en_us'=>'English');
	/**
	 * @property string the default locale.
	 */
	public $defaultLanguage = 'en_us';
	/**
	 * @property string the allowed attachment files types.
	 */
	public $allowedFileTypes = 'jpg, gif, png';
    /**
     * @property integer the maximum allowed filesize for attachments.
     */
    public $allowedFileSize = 1024;
	/**
	 * @property string the path for saving attached files.
	 */
	public $attachmentPath = '/files/cms/attachments/';
	/**
	 * @property string the template to use for node headings.
	 */
	public $headingTemplate = '<h1 class="heading">{heading}</h1>';
	/**
	 * @property string the template to use for widget headings.
	 */
	public $widgetHeadingTemplate = '<h3 class="heading">{heading}</h3>';
	/**
	 * @property string the template to use for page titles.
	 */
	public $pageTitleTemplate = '{title} | {appName}';
	/**
	 * @property string the application layout to use with the cms.
	 */
	public $appLayout = 'application.views.layouts.main';
	/**
	 * @property string the flash message error category.
	 */
	public $flashError = 'error';
	/**
	 * @property string the flash message info category.
	 */
	public $flashInfo = 'info';
	/**
	 * @property string the flash message success category.
	 */
	public $flashSuccess = 'success';
	/**
	 * @property string the flash message warning category.
	 */
	public $flashWarning = 'warning';

    protected $_assetsUrl;

    /**
     * Initializes the component.
     */
    public function init()
    {
        parent::init();

        Yii::app()->getClientScript()->registerCssFile($this->getAssetsUrl().'/css/cms.css');
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
		$controller = Yii::app()->controller;
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
