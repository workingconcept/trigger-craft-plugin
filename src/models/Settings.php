<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\trigger\models;

use craft\base\Model;

/**
 * Settings model
 *
 * @package workingconcept\trigger\models
 */
class Settings extends Model
{

    /**
     * @var bool Whether or not webhook should be pinged.
     */
    public $enabled = true;

    /**
     * @var string Webhook that should receive GET request to trigger a build.
     */
    public $webhookUrl = '';

    /**
     * @var bool Flag that determines whether build should be triggered on check.
     */
    public $shouldDeploy = false;
}