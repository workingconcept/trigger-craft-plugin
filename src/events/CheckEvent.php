<?php

namespace workingconcept\trigger\events;

use yii\base\Event;
use craft\base\Element;

/**
 * Check event class.
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */
class CheckEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var Element  the Element to be checked for deployable changes
     */
    public $element;

    /**
     * @var bool  set `true` if the Element should not trigger a deployment,
     *            though this will *not* reset the deploy flag
     */
    public $shouldIgnore = false;
}
