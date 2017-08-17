<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 12.08.17
 * Time: 23:33
 */

namespace AppBundle\Controller;

use AppBundle\Form\MessageType;
use AppBundle\Entity\Message;
use AppBundle\Entity\MyCategory;
use AppBundle\Entity\MyProduct;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use AppBundle\Form\UserUpdateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{


    /**
     * @Route("/user/", name="user")
     */
    public function userAction($username=1) {
        //admin cannot reach this site, because it's behind the firewall
        //if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
         //   return $this->redirectToRoute('admin');
        //else
            return $this->redirectToRoute('user_redirect', array('username' => $this->getUser()->getUsername()));
    }

    /**
     * @Route("/user/{username}", name="user_redirect")
     */
    public function userNameAction(Request $request, $username)
    {
        $updatable = 0;
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY', null, 'Unable to access this page!');
        $tmpUser = $this->getUser();  //only works in Controllers
        /*
        //test trying to create a blog for each user
        //scince where behind a firewall, every user must be logged in *
        //$msg = new Message();
        // relate this msg to the user
        $msg->setUser($tmpUser); //*

        $form = $this->createForm(MessageType::class, $msg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $msg->setDate(new \DateTime('NOW'));
            $msg->setAuthor($tmpUser->getUsername()); //
            //form submitted -> make msg persist
            $em = $this->getDoctrine()->getManager();
            $em->persist($msg);
            $em->flush();
        }
        */

        //find all msgs for this user
        //if user is the owner
        if($tmpUser->getUsername() === $username) {
            $userId = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($tmpUser->getId());
            $updatable = 1;
        } else {
            $userId = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(array('username' => $username));
        }

        $messages = $userId->getMessages();

        return $this->render('user/user.html.twig', array(
            //'form' => $form->createView(),
            'user' => $userId,
            'messages' => $messages,
            'updatable' => $updatable

        ));
    }

    /**
     * @Route("/user/{username}/update", name="user_update")
     */
    public function userUpdateAction(Request $request, $username, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        //if user is the owner
        if($user->getUsername() !== $username) {
            throw $this->createAccessDeniedException();
        }
        /*
        $userDb = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($user->getId());
        */
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //file upload
            if ($user->getUserAvatarFilename()) {
                $file = $user->getUserAvatarFilename();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('img_directory'),
                    $fileName
                );
                $user->setUserAvatarFilename($fileName);
            }
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            //form submitted -> make msg persist
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User updated'.$user->getUserName());
        }
        //$em->refresh($user);
        return $this->render('user/update.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,

        ));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction(Request $request)
    {
        //$category = new MyCategory();
        $product = new MyProduct();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //file-upload: https://symfony.com/doc/current/controller/upload_file.html
            // $file stores the uploaded PDF file
            //allow empty Brochure
            if ($product->getBrochure()) {
                // @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $product->getBrochure();
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('document_directory'),
                    $fileName
                );
                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $product->setBrochure($fileName);
            }
            //Product-Image
            $file = $product->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('img_directory'),
                $fileName
            );
            $product->setImage($fileName);

            //make persistent
            $em = $this->getDoctrine()->getManager();

            $category = $form->getData()->getCategory();
            //check for an existing category
            $dbCategory = $this->getDoctrine()
                ->getRepository(MyCategory::class)
                ->findOneBy(array('name' => $category)); //find by string

            if($dbCategory)
                $product->setCategory($dbCategory);
            else {
                $newCategory = new MyCategory();
                $newCategory->setName($category);
                $product->setCategory($newCategory);
                $em->persist($newCategory);
            }

            $em->persist($product); //we can use the original $msg,
            $em->flush();

            $this->addFlash('success', 'Product added to DB'.$product->getName());
            return $this->redirectToRoute('orm_show_products');
        }

        return $this->render('admin/admin.html.twig', array(
            'user' => $this->getUser(),
            'form' => $form->createView()

        ));
    }

}