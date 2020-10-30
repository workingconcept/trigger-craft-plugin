<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept
 */

namespace workingconcept\trigger\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Working Concept
 * @package   Trigger
 * @since     1.0.0
 */
class DeployWidgetAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = "@workingconcept/trigger/assetbundles/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/widget.js',
        ];

        $this->css = [
            'css/widget.css',
        ];

        parent::init();
    }
}
