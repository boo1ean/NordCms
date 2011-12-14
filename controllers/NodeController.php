<?php
/**
 * NodeController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.controllers
 */
class NodeController extends CmsController
{
	/**
	 * @property string the name of the default action
	 */
	public $defaultAction = 'view';
	/**
	 * @string string the layout to use with this controller
	 */
	public $layout = 'cms';

	/**
	 * @return array the action filters for this controller.
	 */
	public function filters()
	{
		return array(
			array('cms.components.CmsPageFilter + page'),
		);
	}

	/**
	 * Display the page to update a particular model.
	 * @param $id the id of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$cms = Yii::app()->cms;
		$model = $this->loadModel($id);

		//$this->performAjaxValidation($model);

		$translations = array();
		foreach (array_keys($cms->languages) as $language)
		{
			$content = $model->getContent($language);

			if ($content === null)
				$content = $model->createContent($language);

			$translations[$language] = $content;
		}

		if (isset($_POST['CmsNode']) && isset($_POST['CmsContent']))
		{
			$valid = true;
			foreach ($translations as $language => $content)
			{
				$content->attributes = $_POST['CmsContent'][$language];
				$content->attachment = $upload = CUploadedFile::getInstance($content, '['.$content->locale.']attachment');
				$valid = $valid && $content->validate();

				if ($upload !== null)
					$content->createAttachment($upload);

				$translations[$language] = $content;
			}

			if ($valid)
			{
				$model->attributes = $_POST['CmsNode'];
				$model->save(); // we need to save the node so that the updated column is updated

				foreach ($translations as $content)
					$content->save();

				Yii::app()->user->setFlash($cms->flashSuccess, Yii::t('CmsModule.core', 'Node updated.'));
				$this->redirect(array('update', 'id'=>$id));
			}
		}

		$this->render('update', array(
			'model'=>$model,
			'translations'=>$translations,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the id of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// we only allow deletion via POST request
		$this->loadModel($id)->delete();
		Yii::app()->user->setFlash(Yii::app()->cms->flashSuccess, Yii::t('CmsModule.core', 'Node deleted.'));

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : Yii::app()->homeUrl);
	}

	/**
	 * Displays a particular page.
	 * @param $id the id of the model to display
	 */
	public function actionPage($id)
	{
		$app = Yii::app();
		$model = $this->loadModel($id);

		if ($model->content !== null)
		{
			/** @var CClientScript $cs */
			$cs = $app->clientScript;
			$cs->registerMetaTag($model->content->metaTitle, 'title');
			$cs->registerMetaTag($model->content->metaDescription, 'description');
			$cs->registerMetaTag($model->content->metaKeywords, 'keywords');

			if (!empty($model->content->css))
				$cs->registerCss($model->name, $model->content->css);

            $this->pageTitle = strtr($app->cms->pageTitleTemplate, array(
				'{title}'=>$model->pageTitle,
				'{appName}'=>Yii::app()->name,
			));

            $this->breadcrumbs = $model->getBreadcrumbs();
		}

		$this->layout = $app->cms->appLayout;

		$this->render('page', array(
			'model'=>$model,
			'content'=>$model->render(),
		));
	}

	/**
	 * Deletes an attachment with the given id.
	 * @param $id the attachment id
	 * @throws CHttpException if the request is not a POST-request
	 */
	public function actionDeleteAttachment($id)
	{
		if (Yii::app()->request->isPostRequest)
		{
			var_dump(CmsAttachment::model()->findByPk($id));

			// we only allow deletion via POST request
			CmsAttachment::model()->findByPk($id)->delete();
			Yii::app()->user->setFlash(Yii::app()->cms->flashSuccess, Yii::t('CmsModule.core', 'Attachment deleted.'));

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 * @return CmsNode the model
	 */
	public function loadModel($id)
	{
		$model = CmsNode::model()->findByPk($id, 'deleted=0');

		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='cms-content-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}