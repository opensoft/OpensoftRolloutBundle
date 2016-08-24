<?php

namespace Opensoft\RolloutBundle\Controller;

use Opensoft\Rollout\Rollout;
use Opensoft\RolloutBundle\Rollout\UserProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class DefaultController
{
    use ContainerAwareTrait;

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

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
    }

    /**
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function deactivateAction($feature)
    {
        $this->getRollout()->deactivate($feature);

        $this->addFlash('danger', sprintf("Feature '%s' is now globally deactivated", $feature));

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
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

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
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

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
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

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
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

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
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

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
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

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
    }

    /**
     * @param  string           $feature
     * @return RedirectResponse
     */
    public function removeAction($feature)
    {
        $this->getRollout()->remove($feature);

        $this->addFlash('info', sprintf("Feature '%s' was removed from rollout.", $feature));

        return new RedirectResponse($this->container->get('router')->generate('opensoft_rollout'));
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
    private function addFlash($type, $message)
    {
        $this->container->get('session')->getFlashBag()->add($type, $message);
    }
}
