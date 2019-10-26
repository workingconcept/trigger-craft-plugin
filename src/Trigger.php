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
use craft\elements\Entry;
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

        // is Craft in devMode?
        $isDevMode = Craft::$app->getConfig()->general->devMode;

        if ($this->getSettings()->enabled && $isDevMode === false)
        {
            Craft::info('Listening for changes.', 'trigger');

            Event::on(
                Elements::class,
                Elements::EVENT_AFTER_SAVE_ELEMENT,
                function(ElementEvent $event) {
                    $element = $event->element;
                    $elementId = $element->getId();
                    $isEntry = get_class($element) === Entry::class;
                    $isDraft = ! empty($element->draftId);

                    // don't trigger deployments for draft edits!
                    if ($isEntry && ! $isDraft)
                    {
                        Craft::info(
                            'Flagged deploy for Element #' . $elementId . '.',
                            'trigger'
                        );

                        $this->deployments->flagForDeploy();
                    }
                    else
                    {
                        Craft::info(
                            'Ignored save for Element #' . $elementId . '.',
                            'trigger'
                        );
                    }
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
                'settings' => $this->getSettings(),
            ]
        );
    }



}