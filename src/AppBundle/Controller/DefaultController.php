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

    /**
     * @Route("/userPanel", name="userPanel")
     */
    public function userPanelAction(Request $request){
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('default/userPanel.html.twig', array(
            'users' => $users
        ));

    }

}
