<?php
namespace verbb\patrol\validators;

use yii\base\Model;
use yii\validators\Validator;

class AuthorizedIp extends Validator
{
    // Public Methods
    // =========================================================================

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->{$attribute};

        if (!is_array($value)) {
            $value = [];
        }

        // Ensure unique, non-empty values, indexed from zero
        $value = array_values(array_unique(array_filter($value)));

        $model->{$attribute} = empty($value) ? [] : $value;
    }
}
