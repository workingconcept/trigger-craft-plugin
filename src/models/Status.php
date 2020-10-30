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
 * @author    Working Concept Inc.
 * @package   Trigger
 * @since     0.3.0
 */
class Status extends Model
{
    /**
     * @var string  Changes are waiting to be deployed.
     */
    public const STATUS_PENDING = 'pending';

    /**
     * @var string  No changes are waiting to be deployed.
     */
    public const STATUS_IDLE = 'idle';

    /**
     * @var string
     */
    public $status = self::STATUS_IDLE;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['status', 'string'],
            ['status', 'string'],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_IDLE,
            ]],
            ['status', 'default', 'value' => self::STATUS_IDLE],
        ];
    }
}
