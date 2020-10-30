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
use workingconcept\trigger\widgets\Deploy;

use Craft;
use craft\console\Application as ConsoleApplication;
use craft\base\Plugin;
use craft\events\ElementEvent;
use craft\events\GlobalSetEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Dashboard;
use craft\services\Elements;
use craft\services\Globals;
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
    /**
     * @var Trigger
     */
    public static $plugin;

    /**
     * @var string
     */
    public $schemaVersion = '0.3.0';

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

        // register the widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = Deploy::class;
            }
        );

        // is Craft in devMode?
        $isDevMode = Craft::$app->getConfig()->general->devMode;

        if ($this->getSettings()->active && (! $isDevMode || $this->getSettings()->devModeDeploy))
        {
            Craft::info('Listening for changes.', 'trigger');

            // deploy after saving element
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_SAVE_ELEMENT,
                function (ElementEvent $event) {
                    $this->deployments->checkElement($event->element);
                }
            );

            // deploy after deleting element
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_DELETE_ELEMENT,
                function (ElementEvent $event) {
                    $this->deployments->checkElement($event->element);
                }
            );

            // deploy after restoring element
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_RESTORE_ELEMENT,
                function (ElementEvent $event) {
                    $this->deployments->checkElement($event->element);
                }
            );

            // deploy after reorganized structures
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_UPDATE_SLUG_AND_URI,
                function (ElementEvent $event) {
                    $this->deployments->checkElement($event->element);
                }
            );

            // deploy after saving globals
            Event::on(
                Globals::class,
                Globals::EVENT_AFTER_SAVE_GLOBAL_SET,
                function (GlobalSetEvent $event) {
                    $this->deployments->checkElement($event->globalSet);
                }
            );
        }
        else
        {
            Craft::info('Not listening for changes; disabled or in dev mode.', 'trigger');
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
                'devMode' => Craft::$app->getConfig()->general->devMode,
                'settings' => $this->getSettings(),
            ]
        );
    }
}
