<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessagesController extends Controller
{

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

    /**
     * @Route("/admin/messages", name="admin_messages")
     *
     * @Method({"GET"})
     */
    public function adminMessagesAction()
    {

        $messages = $this->getDoctrine()
            ->getRepository(Message::class)->findAll();

        return $this->render('admin/messages.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/admin/messages/{id}", name="admin_message")
     *
     * @Method({"GET"})
     */
    public function readMessageAction($id)
    {

        $message = $this->getDoctrine()
            ->getManager()->find(Message::class, $id);

        if ($message == null) {
            $this->redirectToRoute('admin_messages');
        }

        return $this->render('admin/message-read.html.twig', [
            'message' => $message
        ]);
    }

    /**
     * @Route("/admin/messages/{id}/delete", name="admin_message_delete")
     *
     * @Method({"POST"})
     */
    public function deleteMessageAction($id)
    {

        $responseParams = ['error' => false];

        $em = $this->getDoctrine()->getManager();

        $message = $em->find(Message::class, $id);

        // If message is not existing.
        if(!$message) {
            $responseParams = [
                'error' => true,
                'message' => 'Message with this id is not existing'
            ];
        }

        // If message is existing.
        else {
            // Deleting message from database.
            $em->remove($message);
            $em->flush();
        }

        return new JsonResponse($responseParams);
    }

}
