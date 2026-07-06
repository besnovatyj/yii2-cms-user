<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\components;

use yii\rbac\Rule;

/**
 * RouteRule Rule for check route with extra params.
 */
class RouteRule extends Rule
{
    const string RULE_NAME = 'RouteRule';

    public $name = self::RULE_NAME;

    public function execute($user, $item, $params): bool
    {
        $routeParams = $item->data['params'] ?? [];
        foreach ($routeParams as $key => $value) {
            if (!array_key_exists($key, $params) || $params[$key] != $value) {
                return false;
            }
        }
        return true;
    }
}
