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
use craft\events\GlobalSetContentEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\ElementHelper;
use craft\services\Dashboard;
use craft\services\Elements;
use craft\elements\Entry;
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
    public $schemaVersion = '0.3.0';


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

        // register the widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Deploy::class;
            }
        );

        // is Craft in devMode?
        $isDevMode = Craft::$app->getConfig()->general->devMode;

        if ($this->getSettings()->active && (! $isDevMode || $this->getSettings()->devModeDeploy))
        {
            Craft::info('Listening for changes.', 'trigger');

            $elementCallback = function(ElementEvent $event) {
                $element = $event->element;
                $elementId = $element->getId();

                if (ElementHelper::isDraftOrRevision($element)) {
                    // don't trigger deployments for draft edits!
                    Craft::info(
                        'Ignored save for Element #' . $elementId . '.',
                        'trigger'
                    );
                } else {
                    // trigger deployment
                    Craft::info(
                        'Flagged deploy for Element #' . $elementId . '.',
                        'trigger'
                    );

                    // deploy immediately, or wait until next 'check'
                    if ($this->getSettings()->deployOnContentChange) {
                        $this->deployments->go();
                    } else {
                        $this->deployments->flagForDeploy();
                    }
                }
            };

            // deploy after saving element
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_SAVE_ELEMENT,
                $elementCallback
            );

            // deploy after deleting element
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_DELETE_ELEMENT,
                $elementCallback
            );

            // deploy after restoring element
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_RESTORE_ELEMENT,
                $elementCallback
            );

            // deploy after reorganized structures
            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_UPDATE_SLUG_AND_URI,
                $elementCallback
            );

            // deploy after saving globals
            Event::on(
                Globals::class,
                Globals::EVENT_AFTER_SAVE_GLOBAL_SET,
                function(GlobalSetContentEvent $event) {
                    Craft::dd($event);
                    $setId = $event->globalSet->id;

                    Craft::info(
                        'Flagged deploy for Global Set #' . $setId . '.',
                        'trigger'
                    );

                    // deploy immediately, or wait until next 'check'
                    if ($this->getSettings()->deployOnContentChange) {
                        $this->deployments->go();
                    } else {
                        $this->deployments->flagForDeploy();
                    }
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