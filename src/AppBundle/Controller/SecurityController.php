<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 04.08.17
 * Time: 16:46
 */

namespace AppBundle\Controller;


use AppBundle\Form\UserLoginType;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{

    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            
            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash('success', 'Welcome '.$user->getUsername());

            /**
             * Send an Email to the User
             * /
            //$this->sendEmail('example@mail.com', $user->getEmail(), $user->getUsername());

            /**
             * Automatically login after registration:
             * The first argument is the $user and the second is $request
             * The third argument is the authenticator whose success behavior we want to mimic.
             * Finally, the last argument is called a "provider key". it' the name of your firewall: see security.yml
             */
            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
            //return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/login", name="login")

    public function loginAction()
    {

        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
        ));
    }
    */

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }

    /**
     * @Route("/test", name="test")
     */
    public function helloAction() {
        //only allow ADMIN access
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        //if you only want to check if a user is logged in (you don't care about roles), then you can use IS_AUTHENTICATED_FULLY:
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        return new Response(
            'Hello User.'
        );
    }

    /**
     * @Route("/login2", name="login2")
     */
    public function login2Action(Request $request)
    {
        //get login errors, if there is one
        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();
        $user->setUsername($lastUsername);

        $form = $this->createForm(UserLoginType::class, $user);

        return $this->render(
            'security/login.html.twig',
            array(
                'form' => $form->createView(),
                'error' => $error,
            )
        );
    }

    private function sendEmail($from, $to, $username) {
        $sendFrom = $from;
        $sendTo = $to;
        $subject = 'Hello ' . $username;
        $body = "Welcome to my Demo-Project! \n Your registration is now complete. \n Have fun";
        $message = (new \Swift_Message($subject))
            ->setFrom($sendFrom)
            ->setTo($sendTo)
            ->setBody($body)
            /*
            ->setBody(
                $this->renderView(
                // app/Resources/views/email/registration.html.twig
                    'email/registration.html.twig',
                    array('name' => $name)
                ),
                'text/html'
            )
            */
            /*
             //If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'Emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;

        $this->get('mailer')->send($message);
    }

}