<?php
namespace craft\plugins\patrol\validators;

use Craft;
use yii\validators\Validator;

/**
 * Class AuthorizedIp
 *
 * @package craft\plugins\patrol\validators
 */
class AuthorizedIp extends Validator
{
	/**
	 * @param \yii\base\Model $model
	 * @param string          $attribute
	 */
	public function validateAttribute($model, $attribute)
	{
		$value = $model->{$attribute};

		if (!is_array($value))
		{
			$value = [];
		}

		// Ensure unique, non-empty values, indexed from zero
		$value = array_values(array_unique(array_filter($value)));

		$model->{$attribute} = empty($value) ? [] : $value;
	}
}
