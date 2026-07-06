<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250430_171712_create_user_auth_items_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%user_auth_items}}';

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
                ->comment('Название элемента авторизации'),
            'type' => $this->smallInteger()->notNull()
                ->comment('Тип элемента (роль или разрешение)'),
            'description' => $this->text()
                ->comment('Описание элемента'),
            'rule_name' => $this->string(64)
                ->comment('Название связанного правила'),
            'data' => $this->binary()
                ->comment('Дополнительные данные'),
            'created_at' => $this->integer()
                ->comment('Время создания элемента'),
            'updated_at' => $this->integer()
                ->comment('Время последнего обновления элемента'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Элементы авторизации (роли и разрешения)');

        $this->createIndexes(static::TABLE_NAME, 'name', true);
        $this->createIndexes(static::TABLE_NAME, 'type');
        $this->createIndexes(static::TABLE_NAME, 'rule_name');

        $this->insert(static::TABLE_NAME, [
            'name' => '/*',
            'type' => 2,
            'description' => null,
            'rule_name' => null,
            'data' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(static::TABLE_NAME, [
            'name' => 'Debug permissions',
            'type' => 2,
            'description' => null,
            'rule_name' => null,
            'data' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(static::TABLE_NAME, [
            'name' => 'guest-role',
            'type' => 1,
            'description' => 'Гость',
            'rule_name' => 'GuestRule',
            'data' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(static::TABLE_NAME, [
            'name' => 'root-role',
            'type' => 1,
            'description' => 'Суперадмин',
            'rule_name' => null,
            'data' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert(static::TABLE_NAME, [
            'name' => 'user-role',
            'type' => 1,
            'description' => 'Пользователь',
            'rule_name' => null,
            'data' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

    }

}
