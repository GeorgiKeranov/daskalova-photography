<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Picture;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PicturesController extends Controller
{
    private function saveImage(File $imageFile)
    {
        // Creating unique name for the image.
        $imageName = md5(uniqid()) . '.' . $imageFile->guessExtension();
        // Saving the image in local directory.
        $imageFile->move('uploads', $imageName);

        // Returning the new name for the image.
        return $imageName;
    }

    private function deleteImageByName($name)
    {
        unlink('uploads/' . $name);
    }

    /**
     * @Route("/admin/pictures/add", name="admin_pictures_add")
     *
     * @Method({"GET"})
     */
    public function adminPicturesAddAction(Request $request)
    {
        $error = $request->get('error');

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('admin/picture-add.html.twig', [
            'categories' => $categories,
            'error' => $error
        ]);
    }

    /**
     * @Route("/admin/pictures/add")
     *
     * @Method({"POST"})
     */
    public function postAdminPicturesAddAction(Request $request)
    {
        $picture = $request->files->get('picture');
        $categoryLink = $request->get('category_link');
        $description = $request->get('description');

        if(filesize($picture) == 0)
            return $this->redirectToRoute('admin_pictures_add', [
                'error' => 'Picture is required!'
            ]);

        $repo = $this->getDoctrine()
            ->getRepository(Category::class);
        $category = $repo->findOneBy(['link' => $categoryLink]);

        if ($category == null)
            return $this->redirectToRoute('admin_pictures_add', [
                'error' => 'Category is\'nt chosen or valid!'
            ]);

        $newPicture = new Picture();
        $newPicture->setPictureName($this->saveImage($picture));
        $newPicture->setCategory($category);
        $newPicture->setDescription($description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($newPicture);
        $em->flush();

        return $this->redirectToRoute('admin');
    }



    /**
     * @Route("/admin/pictures", name="admin_pictures_edit")
     *
     * @Method({"GET"})
     */
    public function adminPicturesViewAction()
    {
        $pictures = $this->getDoctrine()
            ->getRepository(Picture::class)->findAll();

        return $this->render('admin/pictures.html.twig', [
            'pictures' => $pictures
        ]);
    }

    /**
     * @Route("/admin/pictures/{id}", name="admin_picture_edit")
     *
     * @Method({"GET"})
     */
    public function getAdminPictureEdit(Picture $picture, Request $request)
    {
        $error = $request->get('error');

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('admin/picture-edit.html.twig', [
            'picture' => $picture,
            'categories' => $categories,
            'error' => $error
        ]);
    }

    /**
     * @Route("/admin/pictures/{id}")
     *
     * @Method({"POST"})
     */
    public function postAdminPictureEdit(Picture $picture, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $pictureFile = $request->files->get('picture');
        $categoryLink = $request->get('category_link');
        $description = $request->get('description');

        if (filesize($pictureFile) != 0) {
            $this->deleteImageByName($picture->getPictureName());
            $picture->setPictureName($this->saveImage($pictureFile));
        }

        $repo = $this->getDoctrine()
            ->getRepository(Category::class);
        $category = $repo->findOneBy(['link' => $categoryLink]);

        if ($category == null)
            return $this->redirectToRoute('admin_pictures_add', [
                'error' => 'Category is\'nt chosen or valid!'
            ]);

        $picture->setCategory($category);
        $picture->setDescription($description);

        $em->flush();

        return $this->redirectToRoute('admin_pictures_edit');
    }


    /**
     * @Route("/admin/pictures/{id}/delete")
     *
     * @Method({"POST"})
     */
    public function deletePictureById(Picture $picture)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($picture);
        $em->flush();

        return new JsonResponse(['error' => false]);
    }

}
