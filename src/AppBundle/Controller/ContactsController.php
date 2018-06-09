<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SiteText;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactsController extends Controller
{

    private function getSiteText($name)
    {
        $siteText = $this->getDoctrine()
            ->getRepository(SiteText::class)
            ->findOneBy(['name' => $name]);

        if ($siteText != null) {
            return $siteText->getText();
        }

        return "";
    }

    /**
     * @Route("/contacts", name="contacts")
     */
    public function contactsAction()
    {
        $email = $this->getSiteText('email');
        $facebook = $this->getSiteText('facebook');
        $image = $this->getSiteText('home_page_image');

        return $this->render('contacts/contacts.html.twig', [
            'email' => $email,
            'facebook' => $facebook,
            'home_page_image' => $image
        ]);
    }

}
