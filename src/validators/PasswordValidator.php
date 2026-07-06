<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\User\validators;

use yii\validators\Validator;

/**
 * Валидатор для проверки сложности пароля
 *
 * Требования:
 * - Минимум 8 символов
 * - Наличие заглавных букв (A-Z)
 * - Наличие строчных букв (a-z)
 * - Наличие цифр (0-9)
 * - Наличие знаков препинания
 */
class PasswordValidator extends Validator
{
    public int $minLength = 8;
    public string $tooShortMessage = 'Пароль должен содержать не менее {min} символов.';
    public string $noUppercaseMessage = 'Пароль должен содержать хотя бы одну заглавную букву.';
    public string $noLowercaseMessage = 'Пароль должен содержать хотя бы одну строчную букву.';
    public string $noDigitMessage = 'Пароль должен содержать хотя бы одну цифру.';
    public string $noPunctuationMessage = 'Пароль должен содержать хотя бы один знак препинания.';

    /**
     * Валидация пароля
     *
     * @param mixed $model Модель
     * @param string $attribute Атрибут для валидации
     * @return void
     */
    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;

        // Проверка минимальной длины
        if (mb_strlen($value) < $this->minLength) {
            $this->addError($model, $attribute, $this->tooShortMessage, ['min' => $this->minLength]);
            return;
        }

        // Проверка наличия заглавных букв
        if (!preg_match('/[A-Z]/', $value)) {
            $this->addError($model, $attribute, $this->noUppercaseMessage);
            return;
        }

        // Проверка наличия строчных букв
        if (!preg_match('/[a-z]/', $value)) {
            $this->addError($model, $attribute, $this->noLowercaseMessage);
            return;
        }

        // Проверка наличия цифр
        if (!preg_match('/[0-9]/', $value)) {
            $this->addError($model, $attribute, $this->noDigitMessage);
            return;
        }

        // Проверка наличия знаков препинания
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $value)) {
            $this->addError($model, $attribute, $this->noPunctuationMessage);
            return;
        }
    }
}
