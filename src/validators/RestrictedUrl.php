<?php
namespace selvinortiz\patrol\validators;

use yii\validators\Validator;

/**
 * Class RestrictedUrl
 *
 * @package selvinortiz\patrol\validators
 */
class RestrictedUrl extends Validator
{
    /**
     * @param \yii\base\Model $model
     * @param string          $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        if (! is_array($value))
        {
            $value = [];
        }

        $value = array_values(array_unique(array_filter($value)));

        $model->{$attribute} = empty($value) ? [] : $value;
    }
}
