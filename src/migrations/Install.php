<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\trigger\migrations;

use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    public $flagsTable = '{{%trigger_flags}}';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ( ! $this->getDb()->tableExists($this->flagsTable))
        {
            $this->createTable($this->flagsTable, [
                'id'          => $this->primaryKey(),
                'siteId'      => $this->integer(),
                'webhookUrl'  => $this->string()->notNull(),
                'params'      => $this->text(),
                'method'      => $this->enum('method', ['get', 'post', 'put'])->defaultValue('post'),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid'         => $this->uid(),
            ]);

            $this->createIndex(null, $this->flagsTable, ['siteId']);
            $this->addForeignKey(null, $this->flagsTable, ['siteId'], '{{%sites}}', ['id'], 'CASCADE');
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

    }

}