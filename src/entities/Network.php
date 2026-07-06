<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\entities;

use InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $identity
 * @property string $network
 */
class Network extends ActiveRecord
{
    public static function create($network, $identity): self
    {

        if (empty($network) || empty($identity)) {
            throw new InvalidArgumentException('Expected a non-empty `network` or `identity`');
        }

        $item = new static();
        $item->network = $network;
        $item->identity = $identity;
        return $item;
    }

    public static function tableName(): string
    {
        return '{{%user_networks}}';
    }

    public function isFor($network, $identity): bool
    {
        return $this->network === $network && $this->identity === $identity;
    }

    public function isThis($id, $user_id): bool
    {
        return $this->id == $id && $this->user_id == $user_id;
    }
}
