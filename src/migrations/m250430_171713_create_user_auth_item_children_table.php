<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250430_171713_create_user_auth_item_children_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%user_auth_item_children}}';

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
            'parent' => $this->string(64)->notNull()
                ->comment('Название родительского элемента авторизации'),
            'child' => $this->string(64)->notNull()
                ->comment('Название дочернего элемента авторизации'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Связь между родительскими и дочерними элементами авторизации');

        $this->createIndexes(static::TABLE_NAME, ['parent', 'child'], true);

        $this->insert(static::TABLE_NAME, [
            'parent' => 'root-role',
            'child' => '/*',
        ]);

    }

}
