<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Picture;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GalleryController extends Controller
{

    /**
     * @Route("/gallery/{link}", name="gallery")
     */
    public function getGalleryAction(Category $category)
    {
        $pictures = $this->getDoctrine()
            ->getRepository(Picture::class)
            ->findBy(['category' => $category]);

        return $this->render('gallery/gallery.html.twig', [
            "pictures" => $pictures,
            "category" => $category->getName()
        ]);
    }

}
