<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\components;

/**
 * Description of UserStatus
 */
enum UserStatus: int
{
    case STATUS_WAIT = 0;
    case STATUS_ACTIVE = 10;
    case STATUS_BLOCKED = 20;

    /**
     * Человекочитаемое название статуса.
     */
    public function label(): string
    {
        return match ($this) {
            self::STATUS_WAIT => 'Ожидает',
            self::STATUS_ACTIVE => 'Активный',
            self::STATUS_BLOCKED => 'Заблокирован',
        };
    }

    /**
     * Список `числовое значение => название` для фильтров GridView и выпадающих списков.
     * @return array<int, string>
     */
    public static function labels(): array
    {
        return array_combine(
            self::values(),
            array_map(static fn(self $status): string => $status->label(), self::cases())
        );
    }

    /**
     * Допустимые числовые значения статуса — для валидаторов `in` и условий запросов.
     * @return int[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
