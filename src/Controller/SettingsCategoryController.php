<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
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
class SettingsCategoryController extends AbstractController
{
    /**
     * @Route("/category", name="settings_category")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('settings_category/index.html.twig');
    }

    /**
     * @Route("/category/new", name="settings_category_new")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function newCategoryAction(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('settings_category');
        }

        return $this->render('settings_category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/edit", name="settings_category_edit")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     *
     * @return Response
     */
    public function editCategoryAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $category = $em->getRepository(Category::class)->findOneBy(['id' => $request->query->getInt('id')]);
        if (!$category) {
            return $this->render('settings_category/editError.html.twig', [
                'error' => $translator->trans('Category not found!'),
            ]);
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('settings_category');
        }

        return $this->render('settings_category/editSuccess.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/category/data", name="settings_category_data", methods={"GET"})
     *
     * @param CategoryRepository $repository
     *
     * @return JsonResponse
     */
    public function getDataAction(CategoryRepository $repository): JsonResponse
    {
        $src = $repository->getAllWithGroup();
        $data = [];
        array_walk($src, function ($item) use (&$data) {
            $data[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'groupId' => $item['groupId'],
                'groupName' => $item['groupName'],
                'treeIgnore' => $item['treeIgnore'],
                'dashboardIgnore' => $item['dashboardIgnore'],
            ];
        });

        return new JsonResponse(['success' => true, 'data' => $data]);
    }
}
