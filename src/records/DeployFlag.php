<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\trigger\records;

use craft\db\ActiveRecord;

/**
 * Class DeployFlag
 *
 * @package workingconcept\snipcart\records
 *
 * @property int       $id
 * @property int       $siteId
 * @property string    $webhookUrl
 * @property string    $params
 * @property string    $method
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 * @property string    $uid
 */
class DeployFlag extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%trigger_flags}}';
    }
}
