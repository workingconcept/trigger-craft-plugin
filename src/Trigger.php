<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\trigger;

use workingconcept\trigger\models\Settings;
use workingconcept\trigger\services\Deployments;

use Craft;
use craft\console\Application as ConsoleApplication;
use craft\base\Plugin;
use craft\events\ElementEvent;
use craft\services\Elements;
use yii\base\Event;

/**
 * Class Trigger
 *
 * @author    Working Concept
 * @package   Trigger
 * @since     1.0.0
 *
 * @property  Deployments  $deployments
 */
class Trigger extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Trigger
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'deployments' => Deployments::class,
        ]);

        if ($this->getSettings()->enabled)
        {
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_SAVE_ELEMENT,
                function(ElementEvent $event) {
                    $this->deployments->flagForDeploy();
                }
            );

            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_DELETE_ELEMENT,
                function(ElementEvent $event) {
                    $this->deployments->flagForDeploy();
                }
            );

            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_RESTORE_ELEMENT,
                function(ElementEvent $event) {
                    $this->deployments->flagForDeploy();
                }
            );

            // TODO: catch globals
            // TODO: catch reorganized structures
        }
        
        if (Craft::$app instanceof ConsoleApplication)
        {
            $this->controllerNamespace = 'workingconcept\trigger\console\controllers';
        }
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'trigger/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }



}