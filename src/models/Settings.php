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
     * @var string Webhook that should receive POST request to trigger a build.
     */
    public $webhookUrl = '';

    /**
     * @var bool Whether or not webhook should be pinged.
     */
    public $active = true;

    /**
     * @var bool Automatically deploy on content change
     */
    public $deployOnContentChange = true;

    /**
     * @var bool Allow deployments when DevMode is turned on
     */
    public $devModeDeploy = false;
}