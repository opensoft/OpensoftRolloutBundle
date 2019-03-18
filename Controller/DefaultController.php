<?php

namespace Opensoft\RolloutBundle\Controller;

use Opensoft\RolloutBundle\Rollout\GroupDefinitionAwareRollout;
use Opensoft\RolloutBundle\Rollout\UserProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class DefaultController extends AbstractController
{
    /**
     * @var GroupDefinitionAwareRollout
     */
    private $rollout;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @param GroupDefinitionAwareRollout $rollout
     * @param UserProviderInterface $userProvider
     */
    public function __construct(GroupDefinitionAwareRollout $rollout, UserProviderInterface $userProvider)
    {
        $this->rollout = $rollout;
        $this->userProvider = $userProvider;
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('@OpensoftRolloutBundle/Default/index.html.twig', array('rollout' => $this->rollout));
    }

    /**
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function activateAction($feature)
    {
        $this->rollout->activate($feature);

        $this->addFlash('success', sprintf("Feature '%s' is now globally activated", $feature));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function deactivateAction($feature)
    {
        $this->rollout->deactivate($feature);

        $this->addFlash('danger', sprintf("Feature '%s' is now globally deactivated", $feature));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function incrementPercentageAction($feature)
    {
        return $this->changePercentage($feature, 10);
    }

    /**
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function decrementPercentageAction($feature)
    {
        return $this->changePercentage($feature, -10);
    }

    /**
     * @param string $feature
     * @param string $group
     *
     * @return RedirectResponse
     */
    public function activateGroupAction($feature, $group)
    {
        $this->rollout->activateGroup($feature, $group);

        $this->addFlash('info', sprintf("Feature '%s' is now active in group '%s'", $feature, $group));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param string $feature
     * @param string $group
     *
     * @return RedirectResponse
     */
    public function deactivateGroupAction($feature, $group)
    {
        $this->rollout->deactivateGroup($feature, $group);

        $this->addFlash('info', sprintf("Feature '%s' is no longer active in group '%s'", $feature, $group));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param Request $request
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function activateUserAction(Request $request, $feature)
    {
        $requestUser = $request->get('user');
        $user = $this->userProvider->findByRolloutIdentifier($requestUser);

        if ($user) {
            $this->rollout->activateUser($feature, $user);

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
     * @param string $feature
     * @param string $id
     *
     * @return RedirectResponse
     */
    public function deactivateUserAction($feature, $id)
    {
        $user = $this->userProvider->findByRolloutIdentifier($id);

        if ($user) {
            $this->rollout->deactivateUser($feature, $user);

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
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function removeAction($feature)
    {
        $this->rollout->remove($feature);

        $this->addFlash('info', sprintf("Feature '%s' was removed from rollout.", $feature));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * @param Request $request
     * @param string $feature
     *
     * @return RedirectResponse
     */
    public function setRequestParamAction(Request $request, $feature)
    {
        $requestParam = $request->get('requestParam');

        if ($requestParam === null) {
            $this->addFlash('danger', 'Missing "requestParam" value');

            return $this->redirectToRoute('opensoft_rollout');
        }

        $this->rollout->activateRequestParam($feature, $requestParam);

        $this->addFlash('info', sprintf('Feature "%s" requestParam changed to "%s"', $feature, $requestParam));

        return $this->redirectToRoute('opensoft_rollout');
    }

    /**
     * Abstract out common functionality
     *
     * @param string $feature
     * @param int $increment
     *
     * @return RedirectResponse
     */
    private function changePercentage($feature, $increment)
    {
        $percentage = $this->rollout->get($feature)->getPercentage() + $increment;
        $this->rollout->activatePercentage($feature, $percentage);

        $this->addFlash('info', sprintf("Feature '%s' percentage changed to %d%% of all users", $feature, $percentage));

        return $this->redirectToRoute('opensoft_rollout');
    }
}
