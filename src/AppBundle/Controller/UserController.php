<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 12.08.17
 * Time: 23:33
 */

namespace AppBundle\Controller;

use AppBundle\Entity\UserFriends;
use AppBundle\Entity\User;
use AppBundle\Entity\Message;
use AppBundle\Entity\MyCategory;
use AppBundle\Entity\MyProduct;
use AppBundle\Form\ProductType;
use AppBundle\Form\UserFriendsType;
use AppBundle\Form\UserUpdateType;
use AppBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserController
 * @package AppBundle\Controller
 */
class UserController extends Controller
{

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

            $category = $product->getCategory();
            //$category = $form->getData()->getCategory();

            //check for an existing category //currently not useful, since a dropdown with all categories is used
            $dbCategory = $this->getDoctrine()
                ->getRepository(MyCategory::class)
                ->findOneBy(array('name' => $category->getName())); //find by string

            if($dbCategory) {
                $product->setCategory($dbCategory);
            } else {
                $newCategory = new MyCategory();
                $newCategory->setName($category);
                $product->setCategory($newCategory);
                $em->persist($newCategory);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product added to DB'.$product->getName());
            return $this->redirectToRoute('orm_show');
        }

        return $this->render('admin/admin.html.twig', array(
            'user' => $this->getUser(),
            'form' => $form->createView()

        ));
    }


    /**
     * @Route("/user", name="user")
     */
    public function userAction($username=1) {
        return $this->redirectToRoute('user_redirect', array('username' => $this->getUser()->getUsername()));
    }

    /**
     *
     * @Route("/user/{username}", name="user_redirect")
     */
    public function userNameAction(Request $request, $username)
    {
        $isOwner = false;
        $isFriend = false;
        $messages = null;
        $friends = null;
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY', null, 'Unable to access this page!');
        $user = $this->getUser();  //only works in Controllers

        //find all msgs for this user
        //if user is the owner
        if ($user->getUsername() === $username) { //user is owner
            // $user = $this->getDoctrine()
            //     ->getRepository(User::class)
            //     ->find($tmpUser->getId());
            $isOwner = true;
        } else { //user is not the owner
            /* */
            $other = $this->getDoctrine()//load other user from db
            ->getRepository(User::class)
                ->findOneBy(array('username' => $username));

            $friends = $this->getDoctrine()//load all friends from other user
            ->getRepository(UserFriends::class)
                ->findOneByIdJoinedToUser($other->getId());

            foreach ($friends as $friend) { //find out, if the user is a friend
                if (($user->getId() === $friend->getFriend()->getId()) && $friend->getIsConfirmed()) {
                    $isFriend = true;
                    break;
                }
            }
            $user = $other; //to display infos about the user
        }

        if ($isFriend || $isOwner) {
            $messages = $user->getMessages();
            //$friends = $user->getFriends();
            $friends = $this->getDoctrine()
                ->getRepository(UserFriends::class)
                ->findOneByIdJoinedToUser($user->getId());

        }

        return $this->render('user/user.html.twig', array(
            //'form' => $form->createView(),
            'user' => $user,
            'is_owner' => $isOwner,
            'is_friend' => $isFriend,
            'messages' => $messages,
            'friends' => $friends
        ));
    }

    /**
     * @Route("/user/{username}/update", name="user_update")
     */
    public function userUpdateAction(Request $request, $username, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        //security check, if user is the owner
        $this->checkUserConditions($user, $username);
        /*
        $userDb = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($user->getId());
        */

        /*setting the UserAvatarFilename to the realpath on the PC by transforing the string(filename) to a File*/
        //TODO solve the defalt value of the FileChooser
        if ($user->getUserAvatarFilename()) {
            $user->setUserAvatarFilename(new File(
                    $this->getParameter('img_directory') . '/' . $user->getUserAvatarFilename())
            );
        }

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
            return $this->redirectToRoute('user');           

        }
       
        //$em->refresh($user);
        return $this->render('user/update.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,

        ));
    }

    /**
     * for now: only in user-section editing or deleting is possible
     *
     * @Route("/user/{username}/editBlog/{blogPost}", name="edit_blog")
     */
    public function editBlogAction(Request $request, $username, Message $blogPost)
    {
        if($blogPost === null) {
            throw $this->createNotFoundException();
        }
        $tmpUser = $this->getUser();  //only works in Controllers
        //security check, if user is the owner //now, this method can only called by the owner
        $this->checkUserConditions($tmpUser, $username);

        $form = $this->createForm(MessageType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush(); //no need to persist($blogPost), flush() is enough
            return $this->redirectToRoute('user');
        }

        return $this->render('user/editBlog.html.twig', [
            'form' => $form->createView(),
            'user' => $tmpUser
        ]);
    }

    /**
     * @Route("/user/{username}/deleteBlog/{blogPost}", name="delete_blog")
     */
    public function deleteBlogAction(Request $request, $username, Message $blogPost) {
        if($blogPost === null) {
            throw $this->createNotFoundException();
        }
        $tmpUser = $this->getUser();  //only works in Controllers
        //security check, if user is the owner //now, this method can only called by the owner
        $this->checkUserConditions($tmpUser, $username);

        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();
        return $this->redirectToRoute('user');
    }

    /**
     * Test Method: currently not in use
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/user/{username}/addFriend/{friend}", name="add_friend")
     */
    public function addFriendAction(Request $request, User $friend)
    {
        $user = $this->getUser(); //actual logged in user
        //$user->getFriends();
        //check for a logged in user
        if (!$user || !$friend) {
            return;
        }

        //test a new way of adding a friend by passing arguments by URL
        $userFriend = new UserFriends($user->getId());
        $userFriend->setFriend($friend);
        $userFriend->setMsg("You sent " . $friend->getUsername() . " a new friend request.");
        $userFriend->setDate(new \DateTime('NOW'));
        $userFriend->setIsConfirmed(true); //set this to true, that the user cannot confirm or reject

        $otherFriend = new UserFriends($friend->getId());
        $otherFriend->setFriend($user);
        $otherFriend->setMsg("You got a friend request from: " . $user->getUsername());
        $otherFriend->setDate(new \DateTime('NOW'));
        $otherFriend->setIsConfirmed(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userFriend);
        $em->persist($otherFriend);
        $em->flush();

        $this->addFlash('success', 'Send a friend request to: ' . $friend->getUsername());


        /*
        $form = $this->createForm(UserFriendsType::class, $userFriend);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userFriend->setDate(new \DateTime('NOW'));
            //$userFriend->setId($userFriend->getId());
            $userFriend->setIsConfirmed(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userFriend);
            $em->flush();
            return $this->redirectToRoute('user');
        }
*/
        return $this->render('user/addFriend.html.twig', array(
            //'form' => $form->createView(),
            'form' => null,
            'user' => $user
        ));

    }

    /**
     * 2nd method to add a friend: click on the link
     * @param string $username //actual logged in username (app.user.username)
     * @param User $friend //in this case: the userId of the actual page(user.getId()) == possible friend
     * @return RedirectResponse
     *
     * @Route("/user/{username}/add2Friend/{friend}", name="add2_friend")
     */
    public function add2FriendAction($username, User $friend)
    {
        $tmpUser = $this->getUser();
        if (!$tmpUser || !$friend) {
            return;
        }

        //(no need to check, if the user is the owner, because it's never the case)
        $userFriend = new UserFriends($tmpUser->getID());
        $userFriend->setFriend($friend);
        $userFriend->setMsg("You sent " . $friend->getUsername() . " a new friend request.");
        $userFriend->setDate(new \DateTime('NOW'));
        $userFriend->setIsConfirmed(true); //allow this from the sender of the request. if the friend doesn't accept the request delete both entries

        $otherFriend = new UserFriends($friend->getId());
        $otherFriend->setFriend($tmpUser);
        $otherFriend->setMsg("You got a friend request from: " . $tmpUser->getUsername());
        $otherFriend->setDate(new \DateTime('NOW'));
        $otherFriend->setIsConfirmed(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userFriend);
        $em->persist($otherFriend);
        $em->flush();

        return $this->redirectToRoute('user');
    }

    /**
     * //currently: if a friend accepts the friend request, simple make him your friend in the other direction
     * same params and return value for the folling methods...
     * @param string $username //actual logged in username (app.user.username)
     * @param User $friend //the userId of the possible friend (friend.getId())
     * @return RedirectResponse
     *     *
     * @Route("/user/{username}/confirmFriend/{friend}", name="confirm_friend")
     */
    public function confirmFriendAction($username, User $friend)
    {
        if($friend === null) {
            throw $this->createNotFoundException();
        }
        $tmpUser = $this->getUser();
        //security check, if user is the owner //now, this method can only called by the owner
        $this->checkUserConditions($tmpUser, $username);

        /*
        $userFriend = new UserFriends($tmpUser->getID());
        $userFriend->setFriend($friend);
        $userFriend->setIsConfirmed(true);
        $userFriend->setMsg("You are now friends with : " . $username);
        $userFriend->setDate(new \DateTime('NOW'));
 */
        $em = $this->getDoctrine()->getManager();
        //get the other friend and set the data
        $otherFriend = $em->find('AppBundle\Entity\UserFriends',
            array('id' => $tmpUser->getId(), 'friend' => $friend->getId()));
        $otherFriend->setIsConfirmed(true);
        $otherFriend->setMsg("You are now friends with : " . $friend->getUsername());
        //get the user friend and set the data
        $userFriend = $em->find('AppBundle\Entity\UserFriends',
            array('id' => $friend->getId(), 'friend' => $tmpUser->getId()));
        $userFriend->setMsg("You are now friends with : " . $tmpUser->getUsername());

        $em->flush();
        $this->addFlash('success', 'Confirmed friend request from: ' . $username);
        return $this->redirectToRoute('user');
    }

    /**
     * currently: if a friend rejects the friend request, simple delete the UserFriends entry in the DB
     * @param string $username //actual logged in username (app.user.username)
     * @param User $friend //the userId of the possible friend (friend.getId())
     * @return RedirectResponse
     *
     * @Route("/user/{username}/rejectFriend/{friend}", name="reject_friend")
     */
    public function rejectFriendAction($username, User $friend)
    {
        if($friend === null) {
            throw $this->createNotFoundException();
        }
        $tmpUser = $this->getUser();
        //security check, if user is the owner //now, this method can only called by the owner
        $this->checkUserConditions($tmpUser, $username);

        $em = $this->getDoctrine()->getManager();
        $otherFriend = $em->find('AppBundle\Entity\UserFriends',
            array('id' => $tmpUser->getId(), 'friend' => $friend->getId()));
        $em->remove($otherFriend);
        $userFriend = $em->find('AppBundle\Entity\UserFriends',
            array('id' => $friend->getId(), 'friend' => $tmpUser->getId()));
        $em->remove($userFriend);

        $em->flush();
        return $this->redirectToRoute('user');

    }

    /**
     * @param User $user
     * @param string $username
     */
    private function checkUserConditions(User $user, string $username)
    {
        if($user->getUsername() !== $username) {
            throw $this->createAccessDeniedException();
        }
    }
}