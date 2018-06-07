<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GalleryController extends Controller
{

    /**
     * @Route("/gallery", name="gallery")
     */
    public function galleryAction()
    {
        return $this->render('gallery/gallery.html.twig');
    }
}
