<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Entity\SiteText;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    private function addSiteText($name, $content)
    {

        $siteText = $this->getDoctrine()
            ->getRepository(SiteText::class)
            ->findOneBy(['name' => $name]);

        if ($siteText == null) {
            $siteText = new SiteText();
            $siteText->setName($name);
        }

        $siteText->setText($content);

        $em = $this->getDoctrine()->getManager();
        $em->persist($siteText);
        $em->flush();
    }

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

        $homeImage = $this->getSiteText('home_page_image');
        $homeLargeText = $this->getSiteText('home_page_text');
        $homeDescription = $this->getSiteText('home_page_description');
        $email = $this->getSiteText('email');
        $facebook = $this->getSiteText('facebook');

        return $this->render('admin/settings.html.twig', [
            'home_page_image' => $homeImage,
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

        if ($image != null) {
            $currentImageName = $this->getSiteText('home_page_image');

            if($currentImageName != null) {
                // Delete the old image.
                $this->deleteImageByName($currentImageName);
            }

            $this->addSiteText('home_page_image', $this->saveImage($image));
        }

        if ($largeText) {
            $this->addSiteText('home_page_text', $largeText);
        }

        if ($description) {
            $this->addSiteText('home_page_description', $description);
        }

        if ($email) {
            $this->addSiteText('email', $email);
        }

        if ($facebook) {
            $this->addSiteText('facebook', $facebook);
        }

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/password", name="admin_password")
     */
    public function adminChangePassword(Request $request)
    {
        $currentPassword = $request->get('current-password');
        $newPassword1 = $request->get('new-password-1');
        $newPassword2 = $request->get('new-password-2');

        if($request->isMethod('POST')) {

            if ($currentPassword && $newPassword1 && $newPassword2) {

                // Check if the two new passwords are not equal.
                if ($newPassword1 !== $newPassword2) {
                    return $this->render('admin/change-password.html.twig', [
                        'error' => 'Your new and confirm password are not the same!'
                    ]);
                }

                $repo = $this->getDoctrine()->getRepository(Admin::class);
                $em = $this->getDoctrine()->getManager();

                // Get admin object.
                $admin = $repo->findAll()[0];

                $encoder = $this->container->get('security.password_encoder');
                // Check if currrentPassword is not true.
                if (!$encoder->isPasswordValid($admin, $currentPassword)) {
                    return $this->render('admin/change-password.html.twig', [
                        'error' => 'You have entered wrong your current password'
                    ]);
                }

                // Encrypting the new password.
                $newPasswordEncrypted = $encoder->encodePassword($admin, $newPassword1);
                $admin->setPassword($newPasswordEncrypted);

                $em->flush();

                return $this->redirectToRoute('admin');
            }
            else {
                return $this->render('admin/change-password.html.twig', [
                    'error' => 'All fields are required!'
                ]);
            }
        }

        return $this->render('admin/change-password.html.twig');
    }

    /**
     * @Route("/admin/username", name="admin_username")
     */
    public function adminChangeUsername(Request $request)
    {
        $admin = $this->getDoctrine()
            ->getRepository(Admin::class)
            ->findAll()[0];

        $currentUsername = $admin->getUsername();

        if ($request->isMethod('POST')) {

            $newUsername = $request->get('new-username');
            $currentPassword = $request->get('current-password');

            if ($currentPassword && $newUsername) {

                $encoder = $this->container->get('security.password_encoder');
                // Check if currrentPassword is not true.
                if (!$encoder->isPasswordValid($admin, $currentPassword)) {
                    return $this->render('admin/change-username.html.twig', [
                        'error' => 'You have entered wrong your current password',
                        'username' => $currentUsername
                    ]);
                }

                $em = $this->getDoctrine()->getManager();
                $admin->setUsername($newUsername);
                $em->flush();

                return $this->redirectToRoute('admin');

            } else {
                return $this->render('admin/change-username.html.twig', [
                    'error' => 'All fields are required!',
                    'username' => $currentUsername
                ]);
            }
        }

        return $this->render('admin/change-username.html.twig', [
            'username' => $currentUsername
        ]);
    }

}
