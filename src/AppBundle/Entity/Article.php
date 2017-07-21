<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Author;
use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 */
class Article
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
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="path",type="string",length=255,nullable=true)
     */
    private $path;

    /**
     *
     * @var string
     */
    private $summary;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_edit", type="datetime", nullable=true)
     */
    private $dateEdit;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @var int
     *
     * @ORM\Column(name="published_by_id", type="integer")
     */
    private $publishedById;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="articles")
     * @ORM\JoinColumn(name="published_by_id",referencedColumnName="id")
     */
    private $publishedBy;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category",inversedBy="articles")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Author",inversedBy="articles")
     */
    private $writer;

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
     * Set title
     *
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set publishedById
     *
     * @param integer $publishedById
     *
     * @return Article
     */
    public function setPublishedById($publishedById)
    {
        $this->publishedById = $publishedById;

        return $this;
    }

    /**
     * Get publishedById
     *
     * @return integer
     */
    public function getPublishedById()
    {
        return $this->publishedById;
    }

    /**
     * Set publishedBy
     *
     * @param User $publishedBy
     *
     * @return Article
     */
    public function setPublishedBy(User $publishedBy = null)
    {
        $this->publishedBy = $publishedBy;

        return $this;
    }

    /**
     * Get publishedBy
     *
     * @return User
     */
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->dateCreated = new \DateTime('now');
    }

    /**
     * Add category
     *
     * @param Category $category
     *
     * @return Article
     */
    public function addCategory(Category $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Article
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Set writer
     *
     * @param Author $writer
     *
     * @return Article
     */
    public function setWriter(Author $writer = null)
    {
        $this->writer = $writer;

        return $this;
    }

    /**
     * Get writer
     *
     * @return Author
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        if($this->summary===null)
        {
            $this->setSummary();
        }
        return $this->summary;
    }

    /**
     * @param string
     */
    public function setSummary()
    {
        $content = $this->getContent();
        if(strlen($content)>50)
        {
            $this->summary = substr($this->getContent(),0,724)." .....";
        }
        else
        {
            $this->summary=$content." .....";
        }
    }

    /**
     * Set dateCreated
     * @ORM\PrePersist()
     * @param \DateTime $dateCreated
     *
     * @return Article
     */
    public function setDateCreated($dateCreated)
    {
        if (!$this->$dateCreated) {
            $this->dateCreated = new \DateTime();
        }
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateEdit
     *
     * @param \DateTime $dateEdit
     *
     * @return Article
     */
    public function setDateEdit($dateEdit)
    {
        $this->dateEdit = $dateEdit;

        return $this;
    }

    /**
     * Get dateEdit
     *
     * @return \DateTime
     */
    public function getDateEdit()
    {
        return $this->dateEdit;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Article
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Article
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
}
