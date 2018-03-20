<?php

namespace Opensoft\RolloutBundle\Controller;

use Opensoft\RolloutBundle\Rollout\GroupDefinitionAwareRollout;
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
     * @param GroupDefinitionAwareRollout $rollout
     *
     * @return Response
     */
    public function indexAction(GroupDefinitionAwareRollout $rollout)
    {
        return $this->render('OpensoftRolloutBundle:Default:index.html.twig', array('rollout' => $rollout));
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function activateAction(GroupDefinitionAwareRollout $rollout, string $feature)
    {
        $rollout->activate($feature);

        $this->addFlash('success', sprintf("Feature '%s' is now globally activated", $feature));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function deactivateAction(GroupDefinitionAwareRollout $rollout, string $feature)
    {
        $rollout->deactivate($feature);

        $this->addFlash('danger', sprintf("Feature '%s' is now globally deactivated", $feature));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function incrementPercentageAction(GroupDefinitionAwareRollout $rollout, string $feature)
    {
        return $this->changePercentage($rollout, $feature, 10);
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function decrementPercentageAction(GroupDefinitionAwareRollout $rollout, string $feature)
    {
        return $this->changePercentage($rollout, $feature, -10);
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     * @param string $group
     *
     * @return RedirectResponse
     */
    public function activateGroupAction(GroupDefinitionAwareRollout $rollout, string $feature, string $group)
    {
        $rollout->activateGroup($feature, $group);

        $this->addFlash('info', sprintf("Feature '%s' is now active in group '%s'", $feature, $group));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     * @param string $group
     *
     * @return RedirectResponse
     */
    public function deactivateGroupAction(GroupDefinitionAwareRollout $rollout, string $feature, string $group)
    {
        $rollout->deactivateGroup($feature, $group);

        $this->addFlash('info', sprintf("Feature '%s' is no longer active in group '%s'", $feature, $group));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param Request $request
     * @param GroupDefinitionAwareRollout $rollout
     * @param UserProviderInterface $userProvider
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function activateUserAction(
        Request $request,
        GroupDefinitionAwareRollout $rollout,
        UserProviderInterface $userProvider,
        string $feature
    ) {
        $requestUser = $request->get('user');
        $user = $userProvider->findByRolloutIdentifier($requestUser);

        if ($user) {
            $rollout->activateUser($feature, $user);

            $this->addFlash(
                'info',
                sprintf(
                    "User '%s' was activated in feature '%s'",
                    $user->getRolloutIdentifier(),
                    $feature
                )
            );
        } else {
            $this->addFlash('danger', sprintf("User '%s' not found", $requestUser));
        }

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param UserProviderInterface $userProvider
     * @param string $feature
     * @param string $id
     *
     * @return RedirectResponse
     */
    public function deactivateUserAction(
        GroupDefinitionAwareRollout $rollout,
        UserProviderInterface $userProvider,
        string $feature,
        string $id
    ) {
        $user = $userProvider->findByRolloutIdentifier($id);

        if ($user) {
            $rollout->deactivateUser($feature, $user);

            $this->addFlash(
                'info',
                sprintf(
                    "User '%s' was deactivated from feature '%s'",
                    $user->getRolloutIdentifier(),
                    $feature
                )
            );
        } else {
            $this->addFlash('danger', sprintf("User '%s' not found", $id));
        }

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function removeAction(GroupDefinitionAwareRollout $rollout, string $feature)
    {
        $rollout->remove($feature);

        $this->addFlash('info', sprintf("Feature '%s' was removed from rollout.", $feature));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param Request $request
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function setRequestParamAction(Request $request, GroupDefinitionAwareRollout $rollout, string $feature)
    {
        $requestParam = $request->get('requestParam');

        if ($requestParam === null) {
            $this->addFlash('danger', 'Missing "requestParam" value');
            
            return $this->redirectToRoute('opensoft_rollout');
        }

        $rollout->activateRequestParam($feature, $requestParam);

        $this->addFlash('info', sprintf('Feature "%s" requestParam changed to "%s"', $feature, $requestParam));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * Abstract out common functionality
     *
     * @param GroupDefinitionAwareRollout $rollout
     * @param string $feature
     * @param int $increment
     *
     * @return RedirectResponse
     */
    private function changePercentage(GroupDefinitionAwareRollout $rollout, string $feature, int $increment)
    {
        $percentage = $rollout->get($feature)->getPercentage() + $increment;
        $rollout->activatePercentage($feature, $percentage);

        $this->addFlash('info', sprintf("Feature '%s' percentage changed to %d%% of all users", $feature, $percentage));

        return $this->redirectToRoute('opensoft_rollout');
    }
}
