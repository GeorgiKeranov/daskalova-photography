<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends Controller
{

    private function generateUrlForName($name) {

        return str_replace(' ', '-', $name);
    }

    /**
     * @Route("/categories", name="categories")
     *
     * @Method({"GET"})
     */
    public function getCategoriesAction()
    {

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll([]);

        $categoriesArr = [];

        foreach($categories as $category) {
            $assocArr['name'] = $category->getName();
            $assocArr['link'] = $category->getLink();

            array_push($categoriesArr, $assocArr);
        }

        return new JsonResponse($categoriesArr);
    }

    /**
     * @Route("/admin/categories", name="admin_categories")
     *
     * @Method({"GET"})
     */
    public function adminCategoriesAction(Request $request)
    {
        $error = $request->get('error');

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)->findAll();

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
            'error' => $error
        ]);
    }

    /**
     * @Route("/admin/categories/add", name="admin_category_add")
     *
     * @Method({"POST"})
     */
    public function adminAddCategoryAction(Request $request)
    {
        $newCategory = new Category();
        $newCategory->setName($request->get('category'));
        $newCategory->setLink($this->generateUrlForName($newCategory->getName()));

        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Category::class);

        $categoryExist = $repo->findBy(['name' => $newCategory->getName()]);

        if ($categoryExist) {
            return $this->redirectToRoute('admin_categories', [
                'error' => 'Category with this name is already existing!'
            ]);
        }

        $em->persist($newCategory);
        $em->flush();

        return $this->redirectToRoute('admin_categories');
    }

    /**
     * @Route("/admin/categories/{id}", name="admin_category_edit")
     *
     * @Method({"POST"})
     */
    public function adminEditCategoryAction(Request $request)
    {
        $currentName = $request->get('currentName');
        $newName = $request->get('name');

        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Category::class);

        $category = $repo->findOneBy(['name' => $currentName]);

        if ($category == null) {
            return new JsonResponse(['error' => "Category with that name is not existing!"]);
        }

        if ($newName == "") {
            return new JsonResponse(['error' => "New category name is empty!"]);
        }

        $category->setName($newName);
        $category->setLink($this->generateUrlForName($newName));
        $em->persist($category);
        $em->flush();

        return new JsonResponse(['error' => false]);
    }

    /**
     * @Route("/admin/categories/{name}/delete", name="admin_category_delete")
     *
     * @Method({"POST"})
     */
    public function adminDeleteCategoryAction(Category $category)
    {
        // TODO HANDLE ERROR WHEN THERE ARE PICTURES IN THIS CATEGORY
        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        return new JsonResponse(['error' => false]);
    }
}
