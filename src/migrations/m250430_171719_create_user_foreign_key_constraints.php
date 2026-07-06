<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use Yii;
use yii\db\Exception;

class m250430_171719_create_user_foreign_key_constraints extends BaseMigration
{
    /**
     * @throws Exception
     */
    public function safeUp(): void
    {
        parent::safeUp();

        Yii::$app->getDb()->createCommand("SET foreign_key_checks = 0")->execute();

        // auth_items
        $this->createFKs(
            m250430_171712_create_user_auth_items_table::TABLE_NAME,
            'rule_name',
            m250430_171711_create_user_auth_rules_table::TABLE_NAME,
            'name',
            'SET NULL',
            'CASCADE',
        );

        // auth_item_children
        $this->createFKs(
            m250430_171713_create_user_auth_item_children_table::TABLE_NAME,
            'parent',
            m250430_171712_create_user_auth_items_table::TABLE_NAME,
            'name',
            'CASCADE',
            'CASCADE',
        );
        $this->createFKs(
            m250430_171713_create_user_auth_item_children_table::TABLE_NAME,
            'child',
            m250430_171712_create_user_auth_items_table::TABLE_NAME,
            'name',
            'CASCADE',
            'CASCADE',
        );

        // auth_assignments
        $this->createFKs(
            m250430_171714_create_user_auth_assignments_table::TABLE_NAME,
            'item_name',
            m250430_171712_create_user_auth_items_table::TABLE_NAME,
            'name',
            'CASCADE',
            'CASCADE',
        );

        // user_networks
        $this->createFKs(
            m250430_171716_create_user_networks_table::TABLE_NAME,
            'user_id',
            m250430_171715_create_users_table::TABLE_NAME,
            'id',
            'CASCADE',
        );


        Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

    }

    public function safeDown(): void
    {
        // Отменяем действия по умолчанию,
        // так как \Besnovatyj\Kernel\migration\BaseMigration::safeDown() вызывает static::TABLE_NAME,
        // которого в данной миграции не существует.
        // Так же, \Besnovatyj\Kernel\migration\BaseMigration::safeDown() при удалении таблиц сам удалит у них все индексы и внешние ключи.

        // parent::safeDown();
    }

}
