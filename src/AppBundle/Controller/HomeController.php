<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\SiteText;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{

    private function getSiteText($name) {
        $siteText = $this->getDoctrine()
            ->getRepository(SiteText::class)
            ->findOneBy(['name' => $name]);

        if($siteText != null) {
            return $siteText->getText();
        }

        return "";
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $homeLargeText = $this->getSiteText('home_page_text');
        $homeDescription = $this->getSiteText('home_page_description');
        $homeImage = $this->getSiteText('home_page_image');

        return $this->render('home/index.html.twig', [
            'home_page_image' => $homeImage,
            'home_page_large_text' => $homeLargeText,
            'home_page_description' => $homeDescription
        ]);
    }

}
