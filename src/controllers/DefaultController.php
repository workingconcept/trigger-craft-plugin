<?php
/**
 * Trigger plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2019 Working Concept
 */

namespace workingconcept\trigger\controllers;

use workingconcept\trigger\Trigger;

use Craft;
use craft\web\Controller;
use yii\web\Response;

/**
 * @author    Working Concept
 * @package   Trigger
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    /**
     * Immediately triggers build hook.
     *
     * @return Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionGo(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $success = false;

        if (Trigger::$plugin->deployments->go())
        {
            Craft::$app->getSession()->setNotice(Craft::t(
                'trigger',
                'Triggered a build.'
            ));

            $success = true;
        }
        else 
        {
            Craft::$app->getSession()->setError(Craft::t(
                'trigger',
                'Failed to trigger a build.'
            ));
        }

        if ($request->getAcceptsJson()) 
        {
            return $this->asJson(['success' => $success]);
        }

        return $this->redirectToPostedUrl($success);
    }

    /**
     * Checks whether changes are pending, then triggers build hook if needed.
     *
     * @return Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \craft\errors\MissingComponentException
     */
    public function actionCheck(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $success = false;

        if (Trigger::$plugin->deployments->pending())
        {
            if (Trigger::$plugin->deployments->go())
            {
                Craft::$app->getSession()->setNotice(Craft::t(
                    'trigger',
                    'Triggered a build.'
                ));

                $success = true;
            }
            else 
            {
                Craft::$app->getSession()->setError(Craft::t(
                    'trigger',
                    'Failed to trigger a build.'
                ));
            }
        }
        else 
        {
            Craft::$app->getSession()->setNotice(Craft::t(
                'trigger',
                'No pending changes to build.'
            ));

            $success = true;
        }

        if ($request->getAcceptsJson()) 
        {
            return $this->asJson(['success' => $success]);
        }

        return $this->redirectToPostedUrl($success);
    }
}
