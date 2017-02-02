<?php
namespace selvinortiz\patrol\assetbundles\plugin;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PatrolPluginAssetBundle extends AssetBundle {

    public function init() {
        $this->sourcePath = '@selvinortiz/patrol/assetbundles/plugin/dist';
        $this->depends    = [CpAsset::class];

        $this->js = [
            'js/vue.js',
            'js/plugin.js',
        ];

        $this->css = [
            'css/plugin.css',
        ];

        parent::init();
    }
}
