<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 28.07.17
 * Time: 15:46
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class MessageController extends Controller
{
    /**
     * @Route("/blog/", name="blog")
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAction($id = 1, Request $request) {
        $msg = new Message();
        //check for a logged in user
        if($this->getUser()) {
            $msg->setAuthor($this->getUser()->getUsername());
            $msg->setUser($this->getUser());
        } else {
            $msg->setAuthor("Anonymous");
            $msg->setUser(null);
        }

        $form = $this->createForm(MessageType::class, $msg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $msg->setDate(new \DateTime('NOW'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($msg); //we can use the original $msg,
            $em->flush();
        }

        $msgs = $this->getDoctrine()->getRepository(Message::class)->findAll();

        return $this->render('blog/show.html.twig', array(
            'form' => $form->createView(),
            'msg' => $msg,
            'msgs' => $msgs
        ));

    }

    /**
     * @Route("/blog/{id}", name="blog_id")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);
        $msg = $repository->find($id);
        if ($msg === null) {
            throw $this->createNotFoundException();
        }
        return $this->render('blog/show.html.twig',array(
            'form' => null,
            'msg' => $msg,
            'msgs' => null
        ));
    }

}
