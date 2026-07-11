<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User;

use Besnovatyj\Kernel\module\CmsModule;
use Besnovatyj\Contracts\module\DeclaresModule;
use Besnovatyj\Contracts\module\ProvidesAdminMenu;
use Besnovatyj\Contracts\module\ProvidesAppConfig;
use Besnovatyj\Contracts\module\ProvidesComponents;
use Besnovatyj\Contracts\module\ProvidesDependencies;
use Besnovatyj\Contracts\module\ProvidesDirectories;
use Besnovatyj\Contracts\module\ProvidesMigrations;
use Besnovatyj\Contracts\module\ProvidesOptions;
use Yii;

/**
 * GUI manager for RBAC.
 */
class Module extends CmsModule implements
    DeclaresModule, ProvidesMigrations,
    ProvidesAdminMenu, ProvidesOptions,
    ProvidesDependencies, ProvidesDirectories,
    ProvidesComponents, ProvidesAppConfig
{
    public function init(): void
    {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['rbac-admin'])) {
            Yii::$app->i18n->translations['rbac-admin'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => __DIR__ . '/messages',
            ];
        }
    }

    public static function moduleId(): string       { return 'User'; }
    public static function moduleVersion(): string  { return '1.0.0'; }
    public static function isEditable(): bool       { return YII_DEBUG;  }
    public static function adminMenu(): array       { return require __DIR__.'/config/adminMenu.php'; }
    public static function moduleConfig(): array    { return require __DIR__.'/config/config.php'; }
    public static function options(): array         { return require __DIR__.'/config/options.php'; }
    public static function components(): array       { return require __DIR__.'/config/components.php'; }
    public static function appConfig(): array        { return require __DIR__.'/config/appConfig.php'; }
    public static function dependencies(): array    { return require __DIR__.'/config/dependencies.php'; }
    public static function migrationPath(): string       { return __DIR__.'/migrations'; }
    public static function migrationNamespace(): ?string { return __NAMESPACE__.'\\migrations'; }
    public static function directories(): array          { return ['@static/origin/User','@static/cache/User'];}
}
