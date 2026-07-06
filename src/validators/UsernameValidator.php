<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\validators;

use yii\validators\Validator;

class UsernameValidator extends Validator
{
    public $message = 'Данный логин недоступен.';
    private array $denyWords = [ // в бэкэнде не используется, пусть рут там и редактирует себя
        'root', 'admin', 'moderator',
    ];

    public function validateAttribute($model, $attribute): void
    {
        foreach ($this->denyWords as $denyWord) {
            if (str_contains($model->$attribute, $denyWord)) {
                $this->addError($model, $attribute, $this->message);
                break;
            }
        }
    }
}
