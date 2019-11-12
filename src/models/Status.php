<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\trigger\models;

use workingconcept\trigger\Trigger;

use Craft;
use craft\base\Model;

/**
 * @author    Working Concept Inc.
 * @package   Trigger
 * @since     0.3.0
 */
class Status extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $status = 'idle';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'string'],
            ['status', 'default', 'value' => 'idle'],
        ];
    }
}
