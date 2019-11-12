<?php

namespace workingconcept\trigger\services;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
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

        return $success;
    }

    /**
     * Returns true if changes are waiting to be deployed.
     * @return bool
     */
    public function pending(): bool
    {
        return $this->_getDeploymentStatus() == 'pending';
    }

    /**
     * Sets flag indicating that changes are ready to be deployed.
     */
    public function flagForDeploy(): void
    {
        $this->_updateDeploymentStatus('pending');
    }

    /**
     * Turns off flag indicating that changes are ready to be deployed.
     */
    public function resetDeployFlag(): void
    {
        $this->_updateDeploymentStatus();
    }

    // Private Methods
    // =========================================================================

    /**
     * Returns value of trigger_status "status" column
     * @return array
     */
    private function _getDeploymentStatus(): string
    {
        $status = StatusRecord::findOne([]);

        if ($status) {
            return $status->status;
        } else {
            $this->resetDeployFlag();
            return 'idle';
        }

        return $status->status;
    }

    /**
     * Updates (or creates) trigger_status "status" record
     * @param $newStatus The new deployment status that will be set. Accepts: idle, pending
     * @return string
     */
    private function _updateDeploymentStatus(string $newStatus = 'idle'): string
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