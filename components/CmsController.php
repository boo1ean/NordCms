<?php
/**
 * CmsController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * Cms base controller class that provides various base functionality.
 * All cms controllers should be extended from this class.
 */
class CmsController extends CController
{
	/**
	 * @property string the default layout for the controller view
	 */
	public $layout = '//layouts/column1';
	/**
	 * @property array context menu items
	 */
	public $menu = array();
	/**
	 * @property array the breadcrumbs of the current page
	 */
	public $breadcrumbs = array();
}
