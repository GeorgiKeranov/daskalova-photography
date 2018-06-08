<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GalleryController extends Controller
{

    /**
     * @Route("/gallery/{link}", name="gallery")
     */
    public function getGalleryAction(Category $category)
    {

        return $this->render('gallery/gallery.html.twig', [
            'category' => $category->getName()
        ]);
    }

}
