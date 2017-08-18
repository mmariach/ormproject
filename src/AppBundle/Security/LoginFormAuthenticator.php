<?php
namespace AppBundle\Security;

/**
 * Created by PhpStorm.
 * User: mad
 * Date: 10.08.17
 * Time: 22:52
 */
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Form\UserLoginType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Using a Guard: the first step is always to create an authenticator class.
 * extend AbstractGuardAuthenticator, or AbstractFormLoginAuthenticator for Forms
 * then we must implement methods:
 * getCredentials(), checkCredentials()getUser()
 *
 *1 See if the user is submitting the login form, or if this is just some random request for some random page.
 *2 Read the username and password from the request.
 *3 Load the User object from the database.
 *
 * @package AppBundle\Security
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    //So how can we create a form in the authenticator? Use dependency injection to inject the form.factory service.
    private $formFactory;

    //The User is stored in the database, we'll query for them via the entity manager
    private $em;

    //Needed to redirect to login, if validation fails
    private $router;

    //Symfonys UserPasswordEncoder
    private $passwordEncoder;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $em, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array). If you return null, authentication
     * will be skipped.
     *
     * Whatever value you return here will be passed to getUser() and checkCredentials()
     *
     * For example, for a form login, you might:
     *
     *      if ($request->request->has('_username')) {
     *          return array(
     *              'username' => $request->request->get('_username'),
     *              'password' => $request->request->get('_password'),
     *          );
     *      } else {
     *          return;
     *      }
     *
     * @param Request $request
     *
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        // TODO: Implement getCredentials() method.

        //check, if the user has send a post-request from the correct path=/login
        // So if the URL is /login and the HTTP method is POST, our authenticator should spring into action
        $isLoginSubmit = $request->getPathInfo() == '/login2' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            // skip authentication
            return;
        }

        //create the Form with the FormFactory, and handle requests
        $form = $this->formFactory->create(UserLoginType::class);
        $form->handleRequest($request);

        $data = $form->getData();
        //set the last-typed Username in the form
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data->getUsername()
        );

        return $data;
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @throws AuthenticationException
     *
     * @return UserInterface|null
     */
        public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // TODO: Implement getUser() method.
        //$username = $credentials['_username'];
        $username = $credentials->getUsername();

        //The User is stored in the database, we'll query for them via the entity manager
        // If this returns null, guard authentication will fail and the user will see an error.
        return $this->em->getRepository('AppBundle:User')->findOneBy(['username' => $username]);
    }

    /**
     * Returns true if the credentials are valid.
     *
     * If any value other than true is returned, authentication will
     * fail. You may also throw an AuthenticationException if you wish
     * to cause authentication to fail.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // TODO: Implement checkCredentials() method.
        //$password = $credentials['_password'];
        $password = $credentials->getPassword();

        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }

        return false;
    }

    /**
     * Return the URL to the login page.
     * When authentication fails, we need to redirect the user back to the login form.
     * @return string
     */
    protected function getLoginUrl()
    {
        // TODO: Implement getLoginUrl() method.
        return $this->router->generate('login2');
    }

    /**
     * So what happens when authentication is successful? Send them to the page you need
     * @return string
     */
    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('user');
    }
}