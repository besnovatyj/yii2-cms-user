<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\assets;

use yii\web\AssetBundle;

class AnimateAsset extends AssetBundle
{
    public $sourcePath;

    public $css = [
        'animate.css',
    ];

    public function init(): void
    {
        // Путь к ассетам пакета (ранее алиас '@modules/user/assets/media' до выноса в пакет).
        $this->sourcePath = __DIR__ . '/media';
        parent::init();
    }

}
