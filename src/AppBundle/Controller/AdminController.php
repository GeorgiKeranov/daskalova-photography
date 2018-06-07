<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Category;
use AppBundle\Entity\Message;
use AppBundle\Entity\SiteText;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    private function addSiteText($name, $content) {

        $siteText = $this->getDoctrine()
            ->getRepository(SiteText::class)
            ->findOneBy(['name' => $name]);

        if($siteText == null) {
            $siteText = new SiteText();
            $siteText->setName($name);
        }

        $siteText->setText($content);

        $em = $this->getDoctrine()->getManager();
        $em->persist($siteText);
        $em->flush();
    }

    private function getSiteText($name) {
        $siteText = $this->getDoctrine()
            ->getRepository(SiteText::class)
            ->findOneBy(['name' => $name]);

        if($siteText != null) {
            return $siteText->getText();
        }

        return "";
    }

    private function generateUrlForName($name) {
        $newName = str_replace(' ', '-', $name);

        return $newName;
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        return $this->render("admin/login.html.twig");
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        return $this->render("admin/admin.html.twig");
    }

    /**
     * @Route("/admin/settings", name="admin_settings")
     *
     * @Method({"GET"})
     */
    public function adminSettingsAction()
    {

        $homeLargeText = $this->getSiteText('home_page_text');
        $homeDescription = $this->getSiteText('home_page_description');
        $email = $this->getSiteText('email');
        $facebook = $this->getSiteText('facebook');

        return $this->render('admin/settings.html.twig', [
            'home_large_text' => $homeLargeText,
            'home_description' => $homeDescription,
            'email' => $email,
            'facebook' => $facebook
        ]);
    }

    /**
     * @Route("/admin/settings", name="admin_settings_edit")
     *
     * @Method({"POST"})
     */
    public function adminEditSettingsAction(Request $request)
    {
        $image = $request->files->get('portfolio_picture');
        $largeText = $request->get('home_text');
        $description = $request->get('home_description');
        $email = $request->get('email');
        $facebook = $request->get('facebook');

        if($image != null) {
            $image->move('img/', 'alexandra_index_image.jpg');
        }

        if($largeText) {
            $this->addSiteText('home_page_text', $largeText);
        }

        if($description) {
            $this->addSiteText('home_page_description', $description);
        }

        if($email) {
            $this->addSiteText('email', $email);
        }

        if($facebook) {
            $this->addSiteText('facebook', $facebook);
        }

        return $this->redirectToRoute('admin');
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

        if($category == null) {
            return new JsonResponse(['error' => "Category with that name is not existing!"]);
        }

        if($newName == "") {
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
        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        return new JsonResponse(['error' => false]);
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
     */
    public function readMessageAction($id) {

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
    public function deleteMessageAction($id) {

        $responseParams = [ 'error' => false ];

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

    /**
     * @Route("/admin/password", name="admin_password")
     */
    public function adminChangePassword(Request $request)
    {
        $currentPassword = $request->get('current-password');
        $newPassword1 = $request->get('new-password-1');
        $newPassword2 = $request->get('new-password-2');

        if ($currentPassword && $newPassword1 && $newPassword2) {
            // TODO
        }

        return $this->render('admin/change-password.html.twig');
    }

    /**
     * @Route("/admin/username", name="admin_username")
     */
    public function adminChangeUsername(Request $request)
    {
        $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();

        $oldUsername = $admins[0]->getUsername();

        $newUsername = $request->get('newUsername');
        $currentPassword = $request->get('current-password');

        if ($currentPassword && $newUsername) {
            //TODO
        }

        return $this->render('admin/change-username.html.twig');
    }

}
