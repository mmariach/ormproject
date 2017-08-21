<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * MyCategory
 *
 * @ORM\Table(name="my_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MyCategoryRepository")
 */
class MyCategory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Using plural, since we have an array
     * (While the many-to-one mapping shown earlier was mandatory, this one-to-many mapping is optional.
     * Plus, in the context of this application, it will likely be convenient
     * for each Category object to automatically own a collection of its related Product objects.)
     *
     * //targetEntity=ClassName, //mappedBy=ClassName.variable
     * @ORM\OneToMany(targetEntity="MyProduct", mappedBy="category")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }
/*
    public function removeProduct($product)
    {
        if($this->products->contains($product)) {
            $this->products->remove($product);
            $product->setCategory(null);
        }
    }
*/
    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MyCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}

