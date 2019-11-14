<?php

namespace workingconcept\trigger\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\helpers\ElementHelper;
use GuzzleHttp\Client;
use workingconcept\trigger\events\DeployEvent;
use workingconcept\trigger\events\CheckEvent;
use workingconcept\trigger\models\Status as StatusModel;
use workingconcept\trigger\records\Status as StatusRecord;
use workingconcept\trigger\Trigger;

/**
 * Triggers webhook deployments.
 *
 * @package workingconcept\trigger
 */
class Deployments extends Component 
{
    // Events
    // =========================================================================

    /**
     * @event DeployEvent  Triggered immediately before deployment webhook post.
     */
    public const EVENT_BEFORE_DEPLOY = 'beforeDeploy';

    /**
     * @event CheckEvent  Triggered when a changed Element is checked for deployability.
     */
    public const EVENT_CHECK_ELEMENT = 'checkElement';


    // Public Methods
    // =========================================================================

    /**
     * Sends a POST request to the specified webhook.
     *
     * @return bool Returns `true` if post seemed successful.
     */
    public function go(): bool
    {
        $settings = Trigger::$plugin->getSettings();
        $success = false;

        if ($settings->active && $settings->webhookUrl)
        {
            $webhookUrl = Craft::parseEnv($settings->webhookUrl);

            if (filter_var($webhookUrl, FILTER_VALIDATE_URL) === FALSE) 
            {
                // let's not embarrass ourselves
                Craft::error(
                    'Build hook is not a valid URL.',
                    'trigger'
                );

                return $success;
            }

            if ($this->hasEventHandlers(self::EVENT_BEFORE_DEPLOY))
            {
                $event = new DeployEvent();

                $this->trigger(self::EVENT_BEFORE_DEPLOY, $event);
            }

            $shouldCancel = $event->shouldCancel ?? false;

            if ($shouldCancel === false)
            {
                $client = new Client();
                $response = $client->post($webhookUrl);
                $success = $response->getStatusCode() === 200;

                if ($success)
                {
                    Craft::info(
                        'Triggered deploy.',
                        'trigger'
                    );

                    $this->resetDeployFlag();
                }
                else
                {
                    Craft::error(
                        'Deploy trigger failed!',
                        'trigger'
                    );
                }
            }
            else
            {
                Craft::warning(
                    'Deploy trigger cancelled!',
                    'trigger'
                );
            }
        }

        return $success;
    }

    /**
     * Evaluates an updated Element to see whether a deploy (or deploy flag)
     * is needed.
     *
     * @param  ElementInterface  $element  the item to evaluate, most likely
     *                                     passed by a Craft event
     */
    public function checkElement(ElementInterface $element): void
    {
        $elementId = $element->getId();
        $elementClass = get_class($element);

        if ($this->hasEventHandlers(self::EVENT_CHECK_ELEMENT))
        {
            $event = new CheckEvent([
                'element' => $element
            ]);

            $this->trigger(self::EVENT_CHECK_ELEMENT, $event);
        }

        $shouldIgnore = $event->shouldIgnore ?? false;

        if (ElementHelper::isDraftOrRevision($element) || $shouldIgnore)
        {
            // don't trigger deployments for draft edits!
            Craft::info(
                "Ignored save for ${elementClass} #${elementId}.",
                'trigger'
            );
        }
        else
        {
            // trigger deployment
            Craft::info(
                "Flagged deploy for ${elementClass} #${elementId}.",
                'trigger'
            );

            // deploy immediately, or wait until next 'check'
            if (Trigger::$plugin->getSettings()->deployOnContentChange) {
                $this->go();
            } else {
                $this->flagForDeploy();
            }
        }
    }

    /**
     * Returns true if changes are waiting to be deployed.
     * @return bool
     */
    public function pending(): bool
    {
        return $this->_getDeploymentStatus() === StatusModel::STATUS_PENDING;
    }

    /**
     * Sets flag indicating that changes are ready to be deployed.
     */
    public function flagForDeploy(): bool
    {
        return $this->_updateDeploymentStatus(StatusModel::STATUS_PENDING);
    }

    /**
     * Turns off flag indicating that changes are ready to be deployed.
     */
    public function resetDeployFlag(): bool
    {
        return $this->_updateDeploymentStatus();
    }


    // Private Methods
    // =========================================================================

    /**
     * Returns value of trigger_status "status" column
     * @return string
     */
    private function _getDeploymentStatus(): string
    {
        $status = StatusRecord::find()->one();

        if ($status) {
            return $status->status;
        }

        $this->resetDeployFlag();
        return StatusModel::STATUS_IDLE;
    }

    /**
     * Updates (or creates) trigger_status "status" record
     *
     * @param  string  $newStatus  The new deployment status that will be set.
     *                             Accepts: idle, pending
     *
     * @return bool
     */
    private function _updateDeploymentStatus(string $newStatus = StatusModel::STATUS_IDLE): bool
    {
        $status = StatusRecord::find()->one();

        if (empty($status)) {
            $status = new StatusRecord();
        }

        $statusModel = new StatusModel(['status' => $newStatus]);
        
        if ($statusModel) {
            $status->status = $statusModel->status;

            if ($status->save()) {
                return true;
            }
        }

        return false;
    }
}