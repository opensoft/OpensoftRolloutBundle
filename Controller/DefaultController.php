<?php

namespace Opensoft\RolloutBundle\Controller;

use Opensoft\Rollout\Rollout;
use Opensoft\RolloutBundle\Rollout\UserProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->container->get('templating')->renderResponse('OpensoftRolloutBundle:Default:index.html.twig', array('rollout' => $this->getRollout()));
    }

    /**
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function activateAction($feature)
    {
        $this->getRollout()->activate($feature);

        $this->addFlash('success', sprintf("Feature '%s' is now globally activated", $feature));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function deactivateAction($feature)
    {
        $this->getRollout()->deactivate($feature);

        $this->addFlash('danger', sprintf("Feature '%s' is now globally deactivated", $feature));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function incrementPercentageAction($feature)
    {
        $rollout = $this->getRollout();
        $percentage = $rollout->get($feature)->getPercentage() + 10;
        $rollout->activatePercentage($feature, $percentage);

        $this->addFlash('info', sprintf("Feature '%s' percentage changed to %d%% of all users", $feature, $percentage));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function decrementPercentageAction($feature)
    {
        $rollout = $this->getRollout();
        $percentage = $rollout->get($feature)->getPercentage() - 10;
        $rollout->activatePercentage($feature, $percentage);

        $this->addFlash('info', sprintf("Feature '%s' percentage changed to %d%% of all users", $feature, $percentage));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  string           $feature
     * @param  string           $group
     * @return RedirectResponse
     */
    public function activateGroupAction($feature, $group)
    {
        $this->getRollout()->activateGroup($feature, $group);

        $this->addFlash('info', sprintf("Feature '%s' is now active in group '%s'", $feature, $group));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  string           $feature
     * @param  string           $group
     * @return RedirectResponse
     */
    public function deactivateGroupAction($feature, $group)
    {
        $this->getRollout()->deactivateGroup($feature, $group);

        $this->addFlash('info', sprintf("Feature '%s' is no longer active in group '%s'", $feature, $group));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  Request          $request
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function activateUserAction(Request $request, $feature)
    {
        $requestUser = $request->get('user');
        $user = $this->getRolloutUserProvider()->findByRolloutIdentifier($requestUser);

        if ($user) {
            $this->getRollout()->activateUser($feature, $user);

            $this->addFlash('info', sprintf("User '%s' was activated in feature '%s'", $user->getRolloutIdentifier(), $feature));
        } else {
            $this->addFlash('danger', sprintf("User '%s' not found", $requestUser));
        }

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  string           $feature
     * @param  string           $id
     * @return RedirectResponse
     */
    public function deactivateUserAction($feature, $id)
    {
        $user = $this->getRolloutUserProvider()->findByRolloutIdentifier($id);
        $this->getRollout()->deactivateUser($feature, $user);

        $this->addFlash('info', sprintf("User '%s' was deactivated from feature '%s'", $user->getRolloutIdentifier(), $feature));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function removeAction($feature)
    {
        $this->getRollout()->remove($feature);

        $this->addFlash('info', sprintf("Feature '%s' was removed from rollout.", $feature));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @param Request $request
     * @param string $feature
     * @return RedirectResponse
     */
    public function setRequestParamAction(Request $request, $feature)
    {
        $requestParam = $request->get('requestParam');
        if ($requestParam === null) {
            $this->addFlash('danger', 'Missing "requestParam" value');
            return $this->createRedirectToFeatureListReponse();
        }

        $this->getRollout()->activateRequestParam($feature, $requestParam);

        $this->addFlash('info', sprintf('Feature "%s" requestParam changed to "%s"', $feature, $requestParam));

        return $this->createRedirectToFeatureListReponse();
    }

    /**
     * @return Rollout
     */
    private function getRollout()
    {
        return $this->container->get('rollout');
    }

    /**
     * @return UserProviderInterface
     */
    private function getRolloutUserProvider()
    {
        return $this->container->get('rollout.user_provider');
    }

    /**
     * Helper for adding flash messages
     *
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $this->container->get('session')->getFlashBag()->add($type, $message);
    }

    /**
     * @return RedirectResponse
     */
    private function createRedirectToFeatureListReponse()
    {
        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
    }
}
