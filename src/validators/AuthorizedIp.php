<?php
namespace selvinortiz\patrol\validators;

use yii\validators\Validator;

/**
 * Class AuthorizedIp
 *
 * @package selvinortiz\patrol\validators
 */
class AuthorizedIp extends Validator {

    /**
     * @param \yii\base\Model $model
     * @param string          $attribute
     */
    public function validateAttribute($model, $attribute) {
        $value = $model->{$attribute};

        if (! is_array($value)) {
            $value = [];
        }

        // Ensure unique, non-empty values, indexed from zero
        $value = array_values(array_unique(array_filter($value)));

        $model->{$attribute} = empty($value) ? [] : $value;
    }
}
