<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept Inc.
 */
namespace workingconcept\trigger\console\controllers;

use workingconcept\trigger\Trigger;
use yii\console\Controller;
use yii\console\ExitCode;

class DeployController extends Controller
{
    /**
     * Immediately triggers a deploy build.
     *
     * @return integer
     */
    public function actionGo(): int
    {
        return Trigger::$plugin->deployments->go() ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * Triggers a build if changes are pending.
     *
     * @return integer
     */
    public function actionCheck(): int
    {
        if (Trigger::$plugin->deployments->pending())
        {
            $this->stdout('triggering deployment!' . PHP_EOL);
            return Trigger::$plugin->deployments->go() ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('nothing pending' . PHP_EOL);

        return ExitCode::OK;
    }
}
