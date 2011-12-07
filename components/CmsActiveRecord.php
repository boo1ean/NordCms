<?php
/**
 * CmsActiveRecord class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * Cms base active record class that provides various base functionality.
 * All cms active records should be extended from this class.
 */
class CmsActiveRecord extends CActiveRecord
{
	/**
	 * Actions to be taken before calling find.
	 */
	public function beforeFind()
	{
		if ($this->hasAttribute('deleted'))
		{
			$alias = $this->getTableAlias();
			$attribute = $alias.'.deleted';
			$criteria = $this->getDbCriteria();

			if (strpos($criteria->condition, $attribute) === false)
			{
				$criteria->addCondition($attribute.'=0');
				$this->setDbCriteria($criteria);
			}
		}
	}
	
	/**
     * Actions to be taken before saving the record.
	 * @return boolean
     */
    public function beforeSave()
    {
		if (parent::beforeSave())
		{
			$now = new CDbExpression('NOW()');
			$userId = Yii::app()->user->id;
			
			// We are creating a new record.
			if ($this->isNewRecord)
			{
				if ($this->hasAttribute('created'))
				   $this->created = $now;
				
				if ($this->hasAttribute('creatorId') && $userId !== null)
				   $this->creatorId = $userId;
			}
			// We are updating an existing record.
			else
			{
				if ($this->hasAttribute('updated'))
					$this->updated = $now;
				
				if ($this->hasAttribute('updaterId') && $userId !== null)
				   $this->updaterId = $userId;
			}

			return true;
		}
		else
			return false;
    }

	/**
	 * Actions to be taken before calling delete.
	 * @return boolean
	 */
	public function beforeDelete()
	{
		if (parent::beforeDelete())
		{
			if (!$this->hasAttribute('deleted'))
				return true;

			$this->deleted = 1;
			$this->save(false);
		}

		return false;
	}
}
