<?php

namespace App\Controller;

use App\Lib\SettingsHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/settings")
 */
class SettingsGuiController extends AbstractController
{
    /**
     * @Route("/gui", name="settings_gui")
     *
     * @param Request         $request
     * @param SettingsHandler $settingsHandler
     *
     * @return Response
     */
    public function indexAction(Request $request, SettingsHandler $settingsHandler): Response
    {
        $form = $settingsHandler->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsHandler->saveFormData($form->getData());
        }

        return $this->render('settings_gui/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
