<?php

namespace App\Controller;

use App\Entity\CategoryAssignmentRule;
use App\Form\CategoryAssignmentRuleType;
use App\Repository\CategoryAssignmentRuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/settings")
 */
class SettingsAssignmentController extends AbstractController
{
    /**
     * @Route("/assignment", name="settings_assignment")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('settings_assignment/index.html.twig');
    }

    /**
     * @Route("/assignment/new", name="settings_assignment_new")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function newAssignmentRuleAction(Request $request, EntityManagerInterface $em): Response
    {
        $assignmentRule = new CategoryAssignmentRule();
        $form = $this->createForm(CategoryAssignmentRuleType::class, $assignmentRule);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($assignmentRule);
            $em->flush();

            return $this->redirectToRoute('settings_assignment');
        }

        return $this->render('settings_assignment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/assignment/edit", name="settings_assignment_edit")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     *
     * @return Response
     */
    public function editAssignmentRuleAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $assignmentRule = $em->getRepository(CategoryAssignmentRule::class)->findOneBy(['id' => $request->query->getInt('id')]);
        if (!$assignmentRule) {
            return $this->render('settings_assignment/editError.html.twig', [
                'error' => $translator->trans('Rule not found!'),
            ]);
        }
        $form = $this->createForm(CategoryAssignmentRuleType::class, $assignmentRule);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($assignmentRule);
            $em->flush();

            return $this->redirectToRoute('settings_assignment');
        }

        return $this->render('settings_assignment/editSuccess.html.twig', [
            'form' => $form->createView(),
            'assignmentRule' => $assignmentRule,
        ]);
    }

    /**
     * @Route("/assignment/delete", name="settings_assignment_delete")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function deleteAssignmentRuleAction(Request $request, EntityManagerInterface $em): Response
    {
        $assignmentRule = $em->getRepository(CategoryAssignmentRule::class)->findOneBy(['id' => $request->query->getInt('id')]);
        if (!$assignmentRule) {
            return $this->render('settings_assignment/editError.html.twig', [
                'error' => 'Rule not found!',
            ]);
        }

        $em->remove($assignmentRule);
        $em->flush();

        return $this->redirectToRoute('settings_assignment');
    }

    /**
     * @Route("/assignment/data", name="settings_assignment_data", methods={"GET"})
     *
     * @param CategoryAssignmentRuleRepository $repository
     *
     * @return JsonResponse
     */
    public function getDataAction(CategoryAssignmentRuleRepository $repository): JsonResponse
    {
        $src = $repository->findAll();
        $data = [];
        array_walk($src, function (CategoryAssignmentRule $item) use (&$data) {
            $data[] = [
                'id' => $item->getId(),
                'category' => $item->getCategory()->getName(),
                'categoryId' => $item->getCategory()->getId(),
                'rule' => $item->getRule(),
                'type' => $item->getTypeName(),
                'typeId' => $item->getType(),
                'transactionField' => $item->getTransactionFieldName(),
                'transactionFieldId' => $item->getTransactionField(),
            ];
        });

        return new JsonResponse(['success' => true, 'data' => $data]);
    }
}
