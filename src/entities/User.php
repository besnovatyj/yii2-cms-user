<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\entities;

use Besnovatyj\DomainEvents\AggregateRoot;
use Besnovatyj\DomainEvents\EventTrait;
use DateTimeImmutable;
use DomainException;
use Besnovatyj\User\components\Rbac;
use Besnovatyj\User\components\UserStatus;
use Besnovatyj\User\entities\events\UserBlocked;
use Besnovatyj\User\entities\events\UserDeleted;
use Besnovatyj\User\entities\events\UserEditEmailConfirmed;
use Besnovatyj\User\entities\events\UserEditEmailRequested;
use Besnovatyj\User\entities\events\UserPasswordResetConfirmed;
use Besnovatyj\User\entities\events\UserPasswordResetRequested;
use Besnovatyj\User\entities\events\UserSignUpConfirmed;
use Besnovatyj\User\entities\events\UserSignUpRequested;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * ActiveRecord класс пользователя с поддержкой событий.
 * Реализация аутентификации `\Besnovatyj\User\entities\Identity`.
 * Все служебные данные в `\Besnovatyj\User\entities\Profile`.
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $new_email
 * @property string $email_confirm_token
 * @property string $phone
 * @property string $auth_key "remember me" authentication key
 * @property integer $status
 * @property integer $created_at - `new \DateTimeImmutable()->format('Y.m.d H:i:s')`
 * @property integer $updated_at - `new \DateTimeImmutable()->format('Y.m.d H:i:s')`
 * @property string $password write-only password
 *
 * @property string $new_phone
 * @property string $phone_confirm_token
 * @property string $phone_confirm_token_expire
 * @property string $phone_confirm_token_limit
 * @property string $description
 *
 * @property Network[] $networks
 * @property Profile $profile
 */
// TODO при удалении или блокировке пользователя генерить события.
// TODO При удалении, удалять хотелки и заказы. При блокировке забронированные билеты возвращать в продажу (а с выкупленными пусть менеджеры разбираются ).
// TODO сущность пользователя не должна содержать в себе логики аутентификации, только логику работы с данными самой сущности пользователя
class User extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    /**
     * @throws Exception
     */
    public static function create(string $username, string $email, string $phone, string $description, string $password): self
    {
        $user = new User();
        $user->username = preg_replace('/\s+/', '', $username);
        $user->email = preg_replace('/\s+/', '', $email);
        $user->phone = preg_replace('/\s+/', '', $phone);
        $user->description = $description;
        $user->setPassword($password);
        $user->created_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
        $user->status = UserStatus::STATUS_ACTIVE;
        $user->generateAuthKey();
        return $user;
    }

    public function edit(string $username, string $email, string $phone, string $description): void // Бекенд
    {
        $this->username = preg_replace('/\s+/', '', $username);
        $this->email = preg_replace('/\s+/', '', $email);
        $this->phone = preg_replace('/\s+/', '', $phone);
        $this->description = $description;
        $this->updated_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
    }

    /**
     * @throws Exception
     */
    public static function signupRequest(string $username, string $email, string $phone, string $password): self
    {
        $user = new User();
        $user->username = preg_replace('/\s+/', '', $username);
        $user->email = preg_replace('/\s+/', '', $email);
        $user->phone = preg_replace('/\s+/', '', $phone);
        $user->setPassword($password);
        $user->created_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
        $user->status = UserStatus::STATUS_WAIT;
        $user->generateEmailConfirmToken();
        $user->generateAuthKey();
        $user->recordEvent(new UserSignUpRequested($user));
        return $user;
    }

    public function signupConfirm(): void
    {
        $this->activate();
        $this->resetEmailConfirmToken();
        $this->recordEvent(new UserSignUpConfirmed($this));
    }

    /**
     * @throws Exception
     */
    public static function signupByNetwork(): self
    {
        $user = new User();
        $user->created_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
        $user->status = UserStatus::STATUS_ACTIVE;
        $user->generateAuthKey();
        return $user;
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates "remember me" authentication key
     * @throws Exception
     */
    private function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function passwordUpdate(string $password): void
    {
        $this->setPassword($password);
        $this->updated_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     * @throws
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Редактирование профиля пользователем с фронтенда.
     * Возвращает true если email был изменён и отправлен запрос на подтверждение.
     * @throws Exception
     */
    public function editByUser(string $username, string $email, string $phone): bool // Фронтэнд
    {
        $this->username = preg_replace('/\s+/', '', $username);
        $emailChanged = false;
        if ($this->email != preg_replace('/\s+/', '', $email)) {
            $this->new_email = preg_replace('/\s+/', '', $email);
            $this->generateEmailConfirmToken();
            $this->recordEvent(new UserEditEmailRequested($this));
            $emailChanged = true;
        }

        $this->phone = $phone;

        $this->updated_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
        return $emailChanged;
    }

    // Profile methods moved to ProfileService and SignupService

    public function confirmEmail(): void
    {
        $this->email = $this->new_email;
        $this->resetEmailConfirmToken();
        $this->new_email = null;
        $this->recordEvent(new UserEditEmailConfirmed($this));
    }

    /**
     * Запрос на смену номера телефона с отправкой SMS-кода.
     * Flash-уведомление должно устанавливаться вызывающим кодом (контроллером/сервисом).
     * Yii::$app->session->setFlash('success', 'Wait until you receive an SMS with a confirmation code.');
     * @throws \Exception
     */
    public function requestPhoneChange(string $phone): void
    {
        if (!empty($this->phone_confirm_token_expire) && $this->phone_confirm_token_expire > time()) {
            throw new DomainException('Confirmation SMS has already been sent.');
        }
        $this->new_phone = $phone;
        $this->phone_confirm_token = random_int(1000, 9999);
        $this->phone_confirm_token_expire = time() + 600;
        $this->phone_confirm_token_limit = 3;
        // Где-то здесь должна быть отправка смс с кодом на старый номер для подтверждения смены номера телефона на новый
        // НЕТ! Старый номер может быть утерян. Здесь проверка только владения новым номером, не важно что стало со старым.
        //$this->recordEvent(new UserEditPhoneRequested($this));
    }

    public function confirmPhoneChange($token): bool
    {
        if (empty($this->phone_confirm_token)) {
            throw new DomainException('Invalid confirmation code.');
        }

        if ($this->phone_confirm_token_limit <= 0) {
            throw new DomainException('The number of attempts to change the phone has been exhausted.');
        }

        if ($token === $this->phone_confirm_token) {
            $this->phone = $this->new_phone;
            $this->new_phone = null;
            $this->phone_confirm_token = null;
            return true;
        }

        $this->phone_confirm_token_limit--;
        return false;
    }

    // Network methods moved to NetworkService

    /**
     * @throws Exception
     */
    public function requestPasswordReset(): void
    {
        if (!empty($this->password_reset_token) && self::isPasswordResetTokenValid($this->password_reset_token)) {
            throw new DomainException('Запрос на восстановление пароля уже был недавно отправлен.');
        }
        $this->generatePasswordResetToken();
        $this->recordEvent(new UserPasswordResetRequested($this));
    }

    public function resetPassword($password): void
    {
        if (empty($this->password_reset_token)) {
            throw new DomainException('Сначала отправьте запрос на сброс пароля.');
        }

        if (!self::isPasswordResetTokenValid($this->password_reset_token)) {
            throw new DomainException('Время сессии истекло. Запросите сброс пароля еще раз.');
        }
        $this->setPassword($password);
        $this->resetPasswordResetToken();
        $this->recordEvent(new UserPasswordResetConfirmed($this));
    }

    /**
     * Finds out if password reset token is valid
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid(string $token): bool
    {
        $expire = Yii::$app->getModule('user')->params['passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::STATUS_ACTIVE;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Пользователь уже активен');
        }
        $this->status = UserStatus::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === UserStatus::STATUS_BLOCKED;
    }

    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new DomainException('Пользователь уже заблокирован');
        }
        $this->status = UserStatus::STATUS_BLOCKED;
        $this->updated_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
        $this->recordEvent(new UserBlocked($this));
    }

    public function isWait(): bool
    {
        return $this->status === UserStatus::STATUS_WAIT;
    }

    public function draft(): void
    {
        if ($this->isWait()) {
            throw new DomainException('User is already waited.');
        }
        $this->status = UserStatus::STATUS_WAIT;
    }

    /**
     * Для полностью и правильно заполненного профиля возвращает TRUE
     * Должен присутствовать номер телефона и логин, должна быть подтверждена почта
     */
    public function isProfileCorrectlyFilled(): bool
    {
        return !empty($this->email) && !empty($this->username) && !empty($this->phone);
    }

    /**
     * Проверяет, является ли пользователь root-администратором.
     * Используется для защиты от удаления/блокировки привилегированных аккаунтов.
     */
    public function isRoot(): bool
    {
        return array_key_exists(Rbac::ROLE_ROOT, Yii::$app->authManager->getRolesByUser($this->id));
    }

    /**
     * @throws Exception
     */
    public function generateEmailConfirmToken(): void
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    public function resetEmailConfirmToken(): void
    {
        $this->email_confirm_token = null;
    }

    /**
     * @throws Exception
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function resetPasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    public function getProfile(): ActiveQuery
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    public function getNetworks(): ActiveQuery
    {
        return $this->hasMany(Network::class, ['user_id' => 'id']);
    }

    public function afterDelete(): void
    {
        // TODO - удалять профайл при удалении юзера
        $this->recordEvent(new UserDeleted($this));
        parent::afterDelete();
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new DateTimeImmutable()->format('Y.m.d H:i:s'),
            ],
            ...parent::behaviors(),
        ];
    }

    public static function tableName(): string
    {
        return '{{%user_users}}';
    }

}
