<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\User\entities;

use Besnovatyj\Oauth2\bridge\Psr7Factory;
use Besnovatyj\Oauth2\ResourceServer;
use Exception;
use Besnovatyj\User\repositories\UserReadRepository;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\di\NotInstantiableException;
use yii\web\IdentityInterface;

/**
 * Класс реализующий ЛОГИКУ аутентификации (процедура проверки подлинности юзера (а не его прав)).
 * (yii\web\User — это класс компонента приложения, отвечающий за УПРАВЛЕНИЕ состоянием аутентификации пользователя.)
 * TODO - Данный класс, следуя принципу единой ответственности, должен отвечать ТОЛЬКО за логику аутентификации
 * Можно получить `identity` текущего пользователя, используя выражение `Yii::$app->user->identity`.
 * Оно вернёт экземпляр [identity class](https://www.yiiframework.com/doc/api/2.0/yii-web-user#$identityClass-detail),
 * представляющий текущего аутентифицированного пользователя, или null, если текущий пользователь не аутентифицирован (например, гость).
 * Метод `yii\web\User::login()` устанавливает `identity` текущего пользователя в `yii\web\User`.
 *
 * TODO - правильно ли делать данный класс адаптером к сущности юзера?
 * Адаптер для класса пользователя для случаев когда нужно получить данные о пользователе только если он аутентифицирован.
 * `$this->email = !\Yii::$app->user->isGuest ? \Yii::$app->user->identity->email : '';`
 *
 * Даже если адаптер, то:
 * Magic-методы __get(), __call(), __callStatic() делают класс хрупким. Identity — это адаптер, и он должен явно делегировать конкретные методы.
 *
 * @mixin User
 */
class Identity extends Model implements IdentityInterface
{
    public static string $userClass = User::class;
    private ?User $user;

    public function __construct(?User $user = null)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * Этот метод находит экземпляр identity class, используя ID пользователя.
     * Этот метод используется, когда необходимо поддерживать состояние аутентификации через сессии.
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    public static function findIdentity($id): ?Identity
    {
        $user = self::getRepository()->findActiveById($id);
        return $user ? new self($user) : null;
    }

    /**
     * Этот метод находит экземпляр identity class, используя токен доступа.
     * Метод используется, когда требуется аутентифицировать пользователя только по секретному токену
     * (например в RESTful приложениях, не сохраняющих состояние между запросами).
     */
    public static function findIdentityByAccessToken($token, $type = null): ?Identity
    {
        try {
            /** @var ResourceServer $resourceServer */
            $resourceServer = Yii::$app->get('oauth2ResourceServer');

            $psrRequest = Psr7Factory::createServerRequest(Yii::$app->request);
            $psrRequest = $resourceServer->getServer()->validateAuthenticatedRequest($psrRequest);

            $userId = $psrRequest->getAttribute('oauth_user_id');

            if ($userId === null) {
                return null;
            }

            return static::findIdentity((int)$userId);
        } catch (Exception $e) {
            Yii::error('OAuth2 token validation failed: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * Этот метод возвращает ID пользователя, представленного данным экземпляром identity
     */
    public function getId(): int
    {
        return $this->user->id;
    }

    /**
     * Этот метод возвращает ключ, используемый для основанной на cookie аутентификации.
     * Ключ сохраняется в аутентификационной cookie и позже сравнивается с версией, находящейся на сервере,
     * чтобы удостоверится, что аутентификационная cookie верная.
     */
    public function getAuthKey(): string
    {
        return $this->user->auth_key;
    }

    /**
     * Этот метод реализует логику проверки ключа для основанной на cookie аутентификации.
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Получает репозиторий через DI-контейнер.
     * Используется Service Locator намеренно: статический метод findIdentity() требуется
     * интерфейсом IdentityInterface и не может получать зависимости через конструктор.
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    private static function getRepository(): UserReadRepository
    {
        return Yii::$container->get(UserReadRepository::class);
    }

    /**
     * Делегирует обращение к свойствам обёрнутой сущности User.
     * Позволяет использовать Yii::$app->user->identity->email и другие свойства пользователя
     * без явного обращения к getUser().
     */
    public function __get($name)
    {
        return $this->user->$name;
    }

    /**
     * Возвращает обёрнутую сущность User для случаев, когда нужен прямой доступ.
     */
    public function getUser(): User
    {
        return $this->user;
    }

}
