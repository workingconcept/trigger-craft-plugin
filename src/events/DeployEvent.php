<?php

namespace workingconcept\trigger\events;

use yii\base\Event;

/**
 * Deploy event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */
class DeployEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var bool  set to `true` to cancel an imminent deployment
     */
    public $shouldCancel = false;
}
