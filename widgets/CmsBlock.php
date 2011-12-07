<?php
/**
 * CmsBlock class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.widgets
 */

/**
 * Widget that renders the node with the given name.
 */
class CmsBlock extends CWidget
{
	/**
	 * @property string the content name.
	 */
	public $name;

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$app = Yii::app();
		$model = $app->cms->loadNode($this->name);

        if ($model === null)
        {
            $cms = Yii::app()->cms;
            $cms->createNode($this->name);
            $model = $cms->loadNode($this->name);
        }

		if ($model->content !== null && !empty($model->content->css))
			$app->clientScript->registerCss($model->name.'#'.$this->getId(), $model->content->css);

        $this->render('block', array(
            'model'=>$model,
            'content'=>$model->renderWidget(),
        ));
	}
}
