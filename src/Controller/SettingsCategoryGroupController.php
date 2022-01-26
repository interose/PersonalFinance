<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategoryGroup;
use App\Form\CategoryGroupType;
use App\Form\CategoryType;
use App\Repository\CategoryGroupRepository;
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
class SettingsCategoryGroupController extends AbstractController
{
    /**
     * @Route("/category-group", name="settings_category_group")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('settings_category_group/index.html.twig');
    }

    /**
     * @Route("/category-group/new", name="settings_category_group_new")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function newCategoryGroupAction(Request $request, EntityManagerInterface $em): Response
    {
        $categoryGroup = new CategoryGroup();
        $form = $this->createForm(CategoryGroupType::class, $categoryGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categoryGroup);
            $em->flush();

            return $this->redirectToRoute('settings_category_group');
        }

        return $this->render('settings_category_group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category-group/edit", name="settings_category_group_edit")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     *
     * @return Response
     */
    public function editCategoryGroupAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $categoryGroup = $em->getRepository(CategoryGroup::class)->findOneBy(['id' => $request->query->getInt('id')]);
        if (!$categoryGroup) {
            return $this->render('settings_category_group/editError.html.twig', [
                'error' => $translator->trans('Categorygroup not found!'),
            ]);
        }

        $form = $this->createForm(CategoryGroupType::class, $categoryGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categoryGroup);
            $em->flush();

            return $this->redirectToRoute('settings_category_group');
        }

        return $this->render('settings_category_group/editSuccess.html.twig', [
            'form' => $form->createView(),
            'categoryGroup' => $categoryGroup,
        ]);
    }

    /**
     * @Route("/category-group/data", name="settings_category_group_data", methods={"GET"})
     *
     * @param CategoryGroupRepository $repository
     *
     * @return JsonResponse
     */
    public function getDataAction(CategoryGroupRepository $repository): JsonResponse
    {
        $src = $repository->findBy([], ['name' => 'asc']);
        $data = [];
        array_walk($src, function (CategoryGroup $item) use (&$data) {
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'children' => $item->getCategories()->count(),
            ];
        });

        return new JsonResponse(['success' => true, 'data' => $data]);
    }
}
