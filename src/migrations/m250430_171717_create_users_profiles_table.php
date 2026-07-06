<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use Yii;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250430_171717_create_users_profiles_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%users_profiles}}';

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
            'photo' => $this->string(255)->null()
                ->comment('Фото профиля'),
            'firstName' => $this->string(255)->null()
                ->comment('Имя'),
            'lastName' => $this->string(255)->null()
                ->comment('Фамилия'),
            'sex' => $this->smallInteger(1)->null()
                ->comment('Пол'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Профили пользователей');

        $this->createIndexes(static::TABLE_NAME, 'user_id', false, true);
    }

}
