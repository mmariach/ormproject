<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
    public function indexAction(Request $request)
    {

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'users' => null,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
     */

    /**
     * @Route("/", name="homepage")
     */
    public function baseAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /** from Controller
     *
     * @Route("/changeLanguage/{_locale}", name="change_language")
     *
     */
    public function changeLanguageAction(Request $request, $_locale)
    {
        //var_dump($request->get());
        //die;
        $request->setLocale($_locale);        
        //$request->getSession()->set('_locale', 'en');
        //$request->setDefaultLocale('en');
        //$request->setLocale($request->getSession()->get('_locale', 'en'));
        return $this->redirect($request->server->get('HTTP_REFERER'));
        //return $this->redirect($this->generateUrl('homepage'));       
    }

    /**
     * @Route("/userPanel", name="userPanel")
     */
    public function userPanelAction(Request $request){

        //$users = $this->getDoctrine()->getRepository(User::class)->findAll();
        //findAll users ASC
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array(), array('username' => 'ASC'));

        return $this->render('default/userPanel.html.twig', array(
            'users' => $users
        ));

    }

    /**
     * @Route("/testSmarty", name="testSmarty")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testSmartyAction()
    {
        $user = $this->getUser();
        $arry = array(
            'name' => 'Hans',
            'value' => 15,
            'user' => $user->__toString()

        );
        $logger = $this->get('logger');

        return $this->render('default/index.html.smarty', array(
            'arry' => $arry
        ));
    }

}
