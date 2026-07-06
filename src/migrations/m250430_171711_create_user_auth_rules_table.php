<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250430_171711_create_user_auth_rules_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%user_auth_rules}}';

    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'name' => $this->string(64)->notNull()
                ->comment('Название правила'),
            'data' => $this->binary()
                ->comment('Сериализованные данные правила'),
            'created_at' => $this->integer()
                ->comment('Время создания правила'),
            'updated_at' => $this->integer()
                ->comment('Время последнего обновления правила'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Правила авторизации');

        $this->createIndexes(static::TABLE_NAME, 'name', true);

        $time = time();

        $this->insert(static::TABLE_NAME, [
            'name' => 'GuestRule',
            'data' => "O:33:\"Besnovatyj\User\components\GuestRule\":3:{s:4:\"name\";s:9:\"GuestRule\";s:9:\"createdAt\";i:$time;s:9:\"updatedAt\";i:$time;}",
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        $this->insert(static::TABLE_NAME, [
            'name' => 'RouteRule',
            'data' => "O:33:\"Besnovatyj\User\components\RouteRule\":3:{s:4:\"name\";s:9:\"RouteRule\";s:9:\"createdAt\";i:$time;s:9:\"updatedAt\";i:$time;}",
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        parent::safeUp();
    }

}
