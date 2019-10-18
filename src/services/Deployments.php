<?php

namespace workingconcept\trigger\services;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use workingconcept\trigger\Trigger;

/**
 * Triggers webhook deployments.
 *
 * @package workingconcept\cloudflare
 */
class Deployments extends Component 
{

    public function go(): bool
    {
        $settings = Trigger::$plugin->getSettings();
        $success = false;

        if ($settings->enabled && $settings->webhookUrl)
        {
            $webhookUrl = Craft::parseEnv($settings->webhookUrl);
            $client = new Client();
            $response = $client->get($webhookUrl);
            $success = $response->getStatusCode() === 200;

            if ($success)
            {
                $this->resetDeployFlag();
            }
        }

        return $success;
    }

    public function pending(): bool
    {
        return $this->_getSettingsArray()['shouldDeploy'];
    }

    public function flagForDeploy(): void
    {
        $settings = $this->_getSettingsArray();
        $settings['shouldDeploy'] = true;
        Craft::$app->plugins->savePluginSettings(Trigger::$plugin, $settings);
    }

    public function resetDeployFlag(): void
    {
        $settings = $this->_getSettingsArray();
        $settings['shouldDeploy'] = false;
        Craft::$app->plugins->savePluginSettings(Trigger::$plugin, $settings);
    }

    private function _getSettingsArray(): array
    {
        return (array) Trigger::$plugin->getSettings();
    }

}