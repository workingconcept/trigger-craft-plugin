<?php

namespace workingconcept\trigger\services;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
use workingconcept\trigger\Trigger;
use workingconcept\trigger\records\DeployFlag;

/**
 * Triggers webhook deployments.
 *
 * @package workingconcept\cloudflare
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

        // TODO: process all stored flags with their settings (support multiple)

        if ($settings->active && $settings->webhookUrl)
        {
            $webhookUrl = Craft::parseEnv($settings->webhookUrl);
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
        return $this->_getFlag() !== null;
    }

    /**
     * Sets flag indicating that changes are ready to be deployed.
     */
    public function flagForDeploy(): void
    {
        if ($this->pending())
        {
            return;
        }

        $settings = Trigger::$plugin->getSettings();

        $flag = new DeployFlag([
            'siteId' => Craft::$app->sites->currentSite->id,
            'method' => 'post',
            'params' => json_encode([]),
            'webhookUrl' => Craft::parseEnv($settings->webhookUrl)
        ]);

        $flag->save();
    }

    /**
     * Turns off flag indicating that changes are ready to be deployed.
     */
    public function resetDeployFlag(): void
    {
        DeployFlag::deleteAll();
    }


    // Private Methods
    // =========================================================================

    /**
     * @return DeployFlag|null
     */
    private function _getFlag(): ?DeployFlag
    {
        return DeployFlag::findOne(['siteId' => Craft::$app->sites->currentSite->id]);
    }

}