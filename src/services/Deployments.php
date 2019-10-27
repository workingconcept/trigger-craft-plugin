<?php

namespace workingconcept\trigger\services;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
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
        return $this->_getSettingsArray()['shouldDeploy'];
    }

    /**
     * Sets flag indicating that changes are ready to be deployed.
     */
    public function flagForDeploy(): void
    {
        $settings = $this->_getSettingsArray();
        $settings['shouldDeploy'] = true;
        Craft::$app->plugins->savePluginSettings(Trigger::$plugin, $settings);
    }

    /**
     * Turns off flag indicating that changes are ready to be deployed.
     */
    public function resetDeployFlag(): void
    {
        $settings = $this->_getSettingsArray();
        $settings['shouldDeploy'] = false;
        Craft::$app->plugins->savePluginSettings(Trigger::$plugin, $settings);
    }

    // Private Methods
    // =========================================================================

    /**
     * Returns plugin setting as an array.
     * @return array
     */
    private function _getSettingsArray(): array
    {
        return (array) Trigger::$plugin->getSettings();
    }

}