<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * MyProduct
 *
 * @ORM\Table(name="my_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MyProductRepository")
 */
class MyProduct
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="decimal", scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * //targetEntity=ClassName, //inversedBy=ClassName.variable
     * @ORM\ManyToOne(targetEntity="MyCategory", inversedBy="products")
     * @ORM\JoinColumn(name="my_category_id", referencedColumnName="id")
     * //name=ORM\TableColumnName, //referencedColumnName="id" (MyProduct.my_category_id -> MyCategory.id)
     */
    private $category;

    /**
     * @Assert\File(
     *     maxSize = "500k",
     *     mimeTypes={ "application/pdf" }
     *     )
     * @ORM\Column(type="string", nullable=true)
     */
    private $brochure;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(
     *     maxSize = "500k",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/gif", "image/png", "image/tiff"}
     * )
     */
    private $image;

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getBrochure()
    {
        return $this->brochure;
    }

    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
     * @return Products
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

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }}

