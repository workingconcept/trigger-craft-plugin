<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */

namespace workingconcept\trigger\migrations;

use workingconcept\trigger\Trigger;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Working Concept Inc.
 * @package   Trigger
 * @since     0.3.0
 */
class Install extends Migration
{
    /**
     * @var string The database driver to use
     */
    public $driver;

    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    /**
     * @return bool
     */
    protected function createTables(): bool
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%trigger_status}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%trigger_status}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'status' => $this->string(11)->notNull()->defaultValue('idle'),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function removeTables(): void
    {
        $this->dropTableIfExists('{{%trigger_status}}');
    }
}