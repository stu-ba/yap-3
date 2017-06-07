<?php

namespace Yap\Foundation\Validators;

class ArrayUnique
{

    public function validate($attribute, $value, $parameters, $validator)
    {
        //TODO: refactor me, move around some code and reduce it to 10 lines
        if (count($parameters) !== 1) {
            throw new \InvalidArgumentException('Validation rule needs parameter.');
        }

        $dataOfSecondArray = $validator->getData()[$parameters[0]] ?? [];

        if (empty($dataOfSecondArray)) {
            return true;
        }

        $intersection = array_intersect($value, $dataOfSecondArray);
        if (count($intersection) === 0) {
            return true;
        }

        return false;
    }


    public function replace($message, $attribute, $rule, $parameters)
    {
        return str_replace(':parameter', str_slug($parameters[0]), $message);
    }

}