<?php
/**
 * CmsModule class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms
 */

class CmsModule extends CWebModule
{
	/**
	 * @var string the name of the default controller
	 */
	public $defaultController = 'admin';

	/**
	 * Initializes the module.
	 */
	public function init()
	{
		// Register module imports.
		$this->setImport(array(
			'cms.components.*',
			'cms.models.*',
      'ext.bootstrap.widgets.*'
		));
	}

	/**
	 * Performs access check to this module.
	 * @param CController $controller the controller to be accessed
	 * @param CAction $action the action to be accessed
	 * @return boolean whether the action should be executed
	 */
	public function beforeControllerAction($controller, $action)
	{
		if (parent::beforeControllerAction($controller, $action))
		{
			$route = $controller->id.'/'.$action->id;
			if (!Yii::app()->cms->checkAccess() && $route !== 'node/page')
				throw new CHttpException(403, Yii::t('CmsModule.core', 'You are not allowed to access this page.'));

			$publicPages = array('node/page');

			if (Yii::app()->user->isGuest && !in_array($route, $publicPages))
				Yii::app()->user->loginRequired();
			else
				return true;
		}

		return false;
	}
	
	public function getVersion() 
	{
		return '0.9.1';	
	}
}
