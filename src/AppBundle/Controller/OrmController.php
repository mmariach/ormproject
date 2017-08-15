<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 02.08.17
 * Time: 16:03
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\MyCategory;
use AppBundle\Entity\MyProduct;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrmController extends Controller
{
    /**
     * @Route("/orm/add/", name="orm_add")
     * @return Response
     **/
    public function createProductAction()
    {
        /** create new category or

        $category = new MyCategory();
        $category->setName('Computer Peripherals');
         */

        /* ... use existing category (either id or name) */
        $category = $this->getDoctrine()
            ->getRepository(MyCategory::class)
            ->find(2); //find by ID
            //->findOneBy(array('name' => 'Computer Peripherals')); //find one by name

        $product = new MyProduct();
        $product->setName('Headset');
        $product->setPrice(29.99);
        $product->setDescription('...with microphone!');


        // relate this product to the category
        $product->setCategory($category); //expected to a object!, not a value: $category->getId()

        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->persist($product);
        $em->flush();

        return new Response(
            'Saved new product with id: '.$product->getId()
            .' and new category with id: '.$category->getId()
        );
    }

    /**
     * @Route("/orm/show/", name="orm_show")
     * @return Response
     *
     */
    public function showAction($id=1)
    {
        //get all Categories from DB
        $categories = $this->getDoctrine()
            ->getRepository(MyCategory::class)
            ->findAll();

        return $this->render('orm/show.html.twig', array(
            'categories' => $categories
        ));

    }


    /**
     * @Route("/orm/show/{id}", name="orm_index")
     * @return Response
     **/
    public function indexAction($id)
    {
        //
        $product = $this->getDoctrine()->getRepository(MyProduct::class)->find($id);

        //Doctrine silently makes a second query to find the Category that's related to this Product. It prepares the $category object and returns it to you.
        $categoryName = $product->getCategory()->getName();

        return $this->render('orm/show.html.twig', array(
            'products' => array($product)
        ));
    }

    /**
     * @Route("/orm/showProducts/{categoryId}", name="orm_show_products")
     * @return Response
     **/
    public function showProductsAction($categoryId)
    {
            $category = $this->getDoctrine()
                ->getRepository(MyCategory::class)
                ->find($categoryId);

            $products = $category->getProducts();
            return $this->render('orm/show.html.twig', array(
                'products' => $products
            ));
        // ...
    }


}