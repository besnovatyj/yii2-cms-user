<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use Besnovatyj\User\migrations\m250430_171715_create_users_table;
use Yii;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250430_171716_create_user_networks_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%user_networks}}';

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
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()
                ->comment('Идентификатор пользователя'),
            'identity' => $this->string(255)->notNull()
                ->comment('Идентификатор в социальной сети'),
            'network' => $this->string(16)->notNull()
                ->comment('Название социальной сети'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Социальные сети пользователей');

        $this->createIndexes(static::TABLE_NAME, 'user_id');
        $this->createIndexes(static::TABLE_NAME, ['identity', 'network']);

    }

}
