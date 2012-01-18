<?php
/**
 * AdminController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 * @since 0.9.1
 */

class AdminController extends CmsController
{
	public function actionIndex()
	{
		$nodeDp = new CActiveDataProvider('CmsNode', array(
			'criteria'=>array(
				'order'=>'parentId ASC, id ASC',
			),
		));

		$this->render('index', array(
			'nodeDp'=>$nodeDp,
		));
	}
}
