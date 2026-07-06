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
class m250430_171715_create_users_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%user_users}}';

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
            'username' => $this->string(255)->null()
                ->comment('Логин пользователя'),
            'auth_key' => $this->string(32)->notNull()
                ->comment('Ключ авторизации'),
            'password_hash' => $this->string(255)->null()
                ->comment('Хеш пароля'),
            'password_reset_token' => $this->string(255)->null()
                ->comment('Токен сброса пароля'),
            'email' => $this->string(255)->null()
                ->comment('E-mail'),
            'email_confirm_token' => $this->string(255)->null()
                ->comment('Токен подтверждения e-mail'),
            'status' => $this->smallInteger(2)->notNull()->defaultValue(0)
                ->comment('Статус пользователя'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()')
                ->comment('Дата создания пользователя'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()')->append('ON UPDATE NOW()')
                ->comment('Дата последнего редактирования пользователя'),
            'phone' => $this->string(255)->null()
                ->comment('Номер телефона'),
            'description' => $this->text()->null()
                ->comment('Описание'),
            'phone_confirm_token' => $this->string(255)->null()
                ->comment('Токен подтверждения номера телефона'),
            'new_email' => $this->string(255)->null()
                ->comment('Новый e-mail'),
            'new_phone' => $this->string(255)->null()
                ->comment('Новый номер телефона'),
            'phone_confirm_token_expire' => $this->integer()->null()
                ->comment('Время жизни токена подтверждения нового номера телефона'),
            'phone_confirm_token_limit' => $this->integer()->null()
                ->comment('Лимит запросов на подтверждение номера телефона'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Пользователи системы');

        $this->createIndexes(static::TABLE_NAME, 'email');
        $this->createIndexes(static::TABLE_NAME, 'username');
        $this->createIndexes(static::TABLE_NAME, 'password_reset_token');
        $this->createIndexes(static::TABLE_NAME, 'email_confirm_token');

        $this->insert(static::TABLE_NAME, [
            'id' => 1,
            'username' => 'root',
            'auth_key' => '3izmf9mXsfst3ZVCpSi9zTC6UEy4-KlC',
            'password_hash' => '$2y$13$HaHyGub/HzUDBbbGBp4qN.1zCk.gZ2b4s5a3PeMfEDO6YduwtntLC',
            'password_reset_token' => NULL,
            'email' => 'metr31@yandex.ru',
            'email_confirm_token' => NULL,
            'status' => 10,
            'created_at' => new \DateTimeImmutable()->format('Y.m.d H:i:s'),
            'updated_at' => new \DateTimeImmutable()->format('Y.m.d H:i:s'),
            'phone' => '89258745896',
            'description' => 'Просто описание пользователя',
            'phone_confirm_token' => NULL,
            'new_email' => 'metr3133@yandex.ru',
            'new_phone' => NULL,
            'phone_confirm_token_expire' => NULL,
            'phone_confirm_token_limit' => NULL,
        ]);

        $this->insert(static::TABLE_NAME, [
            'id' => 2,
            'username' => 'test-client',
            'auth_key' => 'E6oXwfg2s8uZ_kIoOeU7JT-Q25ZeJrHU',
            'password_hash' => '$2y$13$9QzqglegKi.9q2khEhk0u.94BILfGiry4twcBP3NqW.uHQG9i9hRC',
            'password_reset_token' => NULL,
            'email' => 'test@test.ru',
            'email_confirm_token' => NULL,
            'status' => 10,
            'created_at' => new \DateTimeImmutable()->format('Y.m.d H:i:s'),
            'updated_at' => new \DateTimeImmutable()->format('Y.m.d H:i:s'),
            'phone' => '',
            'description' => 'Просто описание пользователя',
            'phone_confirm_token' => NULL,
            'new_email' => '',
            'new_phone' => NULL,
            'phone_confirm_token_expire' => NULL,
            'phone_confirm_token_limit' => NULL,
        ]);

    }

}
