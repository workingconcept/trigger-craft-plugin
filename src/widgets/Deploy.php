<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\trigger\widgets;

use workingconcept\trigger\assetbundles\DeployWidgetAsset;
use workingconcept\trigger\Trigger;
use Craft;
use craft\base\Widget;

/**
 * Deploy Widget
 */
class Deploy extends Widget
{
        /**
     * Disallow multiple widget instances.
     *
     * @return bool
     */
    protected static function allowMultipleInstances(): bool
    {
        return false;
    }

    /**
     * Returns the translated widget display name.
     *
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('trigger', 'Build Trigger');
    }

    /**
     * Returns the widget's icon path.
     *
     * @return string
     */
    public static function icon()
    {
        return Craft::getAlias("@workingconcept/trigger/assetbundles/dist/img/widget-icon.svg");
    }

    /**
     * Sets the maximum column span to 1.
     *
     * @return int
     */
    public static function maxColspan()
    {
        return 1;
    }

    /**
     * Returns the translated widget title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return '';
    }

    /**
     * Returns the widget body HTML.
     *
     * @return false|string
     * @throws \RuntimeException
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */

    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(DeployWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'trigger/widget',
            [
                'settings' => Trigger::$plugin->getSettings(),
                'pending' => Trigger::$plugin->deployments->pending()
            ]
        );
    }
}
