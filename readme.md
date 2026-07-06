# besnovatyj/yii2-cms-user

Модуль пользователей, аутентификации и RBAC для Yii2 CMS.

Содержит: сущности `User`/`Identity`/`Profile`/`Network`/`AuthItem`, вход/регистрацию/сброс
пароля/подтверждение email (frontend), личный кабинет, GUI-менеджер RBAC (роли, правила,
разрешения, маршруты, назначения — backend), доменные события пользователя и их слушатели,
компоненты доступа (`AccessControl`, `Rbac`, `RouteRule`, `GuestRule`).

Namespace: `Besnovatyj\User` (Yii-идентификатор модуля — `user`, маршруты `/user/...` неизменны).

## Установка

```bash
composer require besnovatyj/yii2-cms-user
```

Модуль устанавливается/удаляется через modman (миграции таблиц `user_*` и RBAC — в `src/migrations`).

### Проводка в приложении (composition root)

Модуль — инфраструктура авторизации приложения, поэтому его проводка остаётся в конфигах
приложения (`app/backend/config/main.php`, `app/frontend/config/main.php`, `app/rest/config/main.php`):

- регистрация модуля в секции `modules` (`'user' => ['class' => \Besnovatyj\User\Module::class]`);
- `components.user.identityClass => \Besnovatyj\User\entities\Identity::class`, `loginUrl`;
- глобальный `AccessControl` (`\Besnovatyj\User\components\AccessControl`);
- RBAC, URL-правила, bootstrap, слушатели событий.

## Зависимости

`php >=8.4`, `ext-mbstring`, `yiisoft/yii2`, `yiisoft/yii2-bootstrap5`, и пакеты экосистемы:
`kernel`, `contracts`, `helpers`, `backend-widgets` (Backend\Widgets), `forms` (Forms + CompositeForm),
`smart-domain-events`, `altcha-widget`, `datetime-widgets`, `oauth2`.

- Зависимость на **`oauth2`**: `Identity` валидирует Bearer-токены через `ResourceServer`/`Psr7Factory`
  (REST-аутентификация). Обратная связь oauth2 → user (репозиторий password-grant) реализована как
  мягкая, через DI-контейнер, и в composer НЕ объявлена — цикла нет.

## ⚠️ Остаточная зависимость от ядра (B1.1)

`src/entities/Profile.php` использует `common\components\upload\behaviors\ImageUploadBehavior` —
класса нет в текущем ядре (фантомная зависимость, общий блокер B1.1 в `GITHUB_MIGRATION_READINESS.md`).
Закрывается вместе с выносом upload-поведения в пакет `besnovatyj/yii2-cms-upload`; после этого
переключить `use` и добавить зависимость.

## Лицензия

MIT.
