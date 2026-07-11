<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\User\Module;

/**
 * Пер-аппликационный вклад модуля user в приложение app-rest для движка yiisoft/config.
 *
 * Оживляет приложение, перекрывая ядро (слой vendor поверх отсутствующего дефолта в root):
 * identityClass -> Identity, accessAuthorizer -> RbacAuthorizer, whitelist входа. Класс гейта
 * (as access.class) задаёт только ядро (root) — модуль его не касается. Источник — {@see Module::appConfig()}.
 */
return Module::appConfig()['app-rest'] ?? [];
