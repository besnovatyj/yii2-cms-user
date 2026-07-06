<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\validators;

use Closure;
use yii\db\ActiveQuery;
use yii\validators\Validator;

/**
 * Валидатор логина: запрещает зарезервированные слова (root, admin, moderator и т.п.).
 *
 * Поддерживает `filter` в том же формате, что стандартные валидаторы Yii2
 * (`UniqueValidator`/`ExistValidator`): массив уходит в `andWhere()`, Closure получает Query.
 *
 * Если задан `targetClass`, запрет становится «мягким к владельцу»: запись может СОХРАНИТЬ
 * логин, которым уже владеет (совпадающий с текущим значением в БД), но не может занять
 * зарезервированный логин, принадлежащий другому пользователю или ещё никем не занятый.
 *
 * Пример (разрешить пользователю оставить свой логин, но запретить взять чужой/новый):
 * ```php
 * ['username', UsernameValidator::class, 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->_user->id]],
 * ```
 * Без `targetClass` работает как раньше — жёсткий запрет для всех (например, на создании).
 */
class UsernameValidator extends Validator
{
    public $message = 'Данный логин недоступен.';

    /** @var string[] Запрещённые подстроки в логине */
    public array $denyWords = [
        'root', 'admin', 'moderator',
    ];

    /** @var string|null AR-класс, хранящий логины. Задан — включает проверку владения логином. */
    public ?string $targetClass = null;

    /** @var string|null Атрибут логина в targetClass. По умолчанию — валидируемый атрибут. */
    public ?string $targetAttribute = null;

    /**
     * @var array|Closure|null Доп. условие запроса, как в yii\validators\UniqueValidator::$filter.
     * Обычно исключает текущую запись: `['<>', 'id', $this->_user->id]`.
     */
    public array|Closure|null $filter = null;

    public function validateAttribute($model, $attribute): void
    {
        $value = (string)$model->$attribute;

        if (!$this->isDenied($value)) {
            return;
        }

        // Без targetClass — жёсткий запрет для всех.
        if ($this->targetClass === null) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        $targetAttribute = $this->targetAttribute ?? $attribute;

        // Логином владеет кто-то, кого фильтр НЕ исключает (не текущая запись) — запрещаем.
        if ($this->existsBy($targetAttribute, $value, applyFilter: true)) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        // Фильтр никого не нашёл: логин свободен ИЛИ принадлежит только текущей записи.
        // Разрешаем лишь во втором случае — когда запись уже владеет этим логином.
        if (!$this->existsBy($targetAttribute, $value, applyFilter: false)) {
            $this->addError($model, $attribute, $this->message);
        }
    }

    private function isDenied(string $value): bool
    {
        foreach ($this->denyWords as $denyWord) {
            if (str_contains($value, $denyWord)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверяет наличие записи с таким логином, опционально применяя `filter`.
     */
    private function existsBy(string $attribute, string $value, bool $applyFilter): bool
    {
        /** @var ActiveQuery $query */
        $query = ($this->targetClass)::find()->andWhere([$attribute => $value]);

        if ($applyFilter && $this->filter !== null) {
            if ($this->filter instanceof Closure) {
                ($this->filter)($query);
            } else {
                $query->andWhere($this->filter);
            }
        }

        return $query->exists();
    }
}
