<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\SiteText;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactsController extends Controller
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
     * @Route("/contacts", name="contacts")
     */
    public function contactsAction()
    {
        $email = $this->getSiteText('email');
        $facebook = $this->getSiteText('facebook');

        return $this->render('contacts/contacts.html.twig', [
            'email' => $email,
            'facebook' => $facebook
        ]);
    }

    /**
     * @Route("/contacts/message", name="send_message")
     * @Method({"POST"})
     */
    public function sendMessageAction(Request $request)
    {

        $message = new Message();
        $message->setName($request->request->get('name'));
        $message->setEmail($request->request->get('email'));
        $message->setPhone($request->request->get('phone'));
        $message->setTitle($request->request->get('title'));
        $message->setMessage($request->request->get('message'));
        $message->setDateSent(new \DateTime());

        if($message->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $responseParams = [
                'error' => false
            ];
        }

        else {
            $responseParams = [
                'error' => 'Some of fields -> name/email/message are empty'
            ];
        }

        return new JsonResponse($responseParams);
    }
}
