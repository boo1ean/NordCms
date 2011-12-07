<?php
/**
 * MarkItUp class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

class CmsMarkItUp extends CInputWidget
{
	/**
	 * @property string the markitup set.
	 */
	public $set='default';
	/**
	 * @property string the markitup skin.
	 */
	public $skin='simple';

	/**
	* Initializes the widget.
	*/
	public function init()
	{
		$app = Yii::app();
		$identifiers=$this->resolveNameID();

		if ($this->name===null)
			$this->name=$identifiers[0];
		
		$this->setId($identifiers[1]);

		if (YII_DEBUG)
			$assetPath=$app->assetManager->publish(dirname(__FILE__).'/assets', false, -1, true);
		else
			$assetPath=$app->assetManager->publish(dirname(__FILE__).'/assets');

		// Register the necessary scripts
		$cs = $app->clientScript;
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($assetPath.'/markitup/jquery.markitup.js');
		$cs->registerScriptFile($assetPath.'/markitup/sets/'.$this->set.'/set.js');
		$cs->registerCssFile($assetPath.'/markitup/sets/'.$this->set.'/style.css');
		$cs->registerCssFile($assetPath.'/markitup/skins/'.$this->skin.'/style.css');

		// Build the script for registering the plugin and place the editor on the page with the given config.
		$script = "jQuery('#".$this->id."').markItUp(markitup.settings);";
		$cs->registerScript('MarkItUp#'.$this->id,$script,CClientScript::POS_READY);
	}

	/**
	* Runs the widget.
	*/
	public function run()
	{
		echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
	}
}