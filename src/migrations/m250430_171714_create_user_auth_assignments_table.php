<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250430_171714_create_user_auth_assignments_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%user_auth_assignments}}';

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
            'item_name' => $this->string(64)->notNull()
                ->comment('Название элемента авторизации'),
            'user_id' => $this->string(64)->notNull()
                ->comment('Идентификатор пользователя'),
            'created_at' => $this->integer()
                ->comment('Время создания назначения'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Назначения элементов авторизации пользователям');

        $this->createIndexes(static::TABLE_NAME, ['item_name', 'user_id'], true);
        $this->createIndexes(static::TABLE_NAME, 'user_id');

        $this->insert(static::TABLE_NAME, [
            'item_name' => 'root-role',
            'user_id' => 1,
            'created_at' => time(),
        ]);

        $this->insert(static::TABLE_NAME, [
            'item_name' => 'user-role',
            'user_id' => 2,
            'created_at' => time(),
        ]);

    }

}
