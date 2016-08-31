<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Entities\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Section
 * @package Entities\Entity
 * @ORM\Table(name="sections")
 * @ORM\Entity
 */
class Section {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $path;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string" , nullable=true)
     * @var string
     */
    private $bucket;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $position;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $description;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $description1;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $description2;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $description3;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $description4;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    private $status;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $tags;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $text;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $link;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $slug;


    /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="parent",cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     * @var ArrayCollection
     */
    protected $subsections;

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="subsections",cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     * @var Section
     */
    private $parent;

    /**
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Image", mappedBy="section")
     * @ORM\JoinColumn(name="id", referencedColumnName="section_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $images;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Text", mappedBy="section")
     * @ORM\JoinColumn(name="id", referencedColumnName="section_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $texts;

    /**
     * Section constructor.
     */
    public function __construct() {
        $this->subsections = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->texts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDescription1() {
        return $this->description1;
    }

    /**
     * @return string
     */
    public function getDescription2() {
        return $this->description2;
    }

    /**
     * @return string
     */
    public function getDescription3() {
        return $this->description3;
    }

    /**
     * @return string
     */
    public function getDescription4() {
        return $this->description4;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubsections() {
        return $this->subsections->toArray();
    }

    /**
     * @return Section
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @param string $description1
     */
    public function setDescription1($description1) {
        $this->description1 = $description1;
    }

    /**
     * @param string $description2
     */
    public function setDescription2($description2) {
        $this->description2 = $description2;
    }

    /**
     * @param string $description3
     */
    public function setDescription3($description3) {
        $this->description3 = $description3;
    }

    /**
     * @param string $description4
     */
    public function setDescription4($description4) {
        $this->description4 = $description4;
    }

    /**
     * @param int $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * @param ArrayCollection $subsections
     */
    public function setSubsections($subsections) {
        $this->subsections = $subsections;
    }

    /**
     * @param Section $parent
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages() {
        return array_values(array_filter($this->images->toArray(),function($image){ return $image->isEnabled(); }));
        //return $this->images->toArray();
    }

    /**
     * @param ArrayCollection $images
     */
    public function setImages($images) {
        $this->images = $images;
    }

    /**
     * @return ArrayCollection
     */
    public function getTexts() {
        return $this->texts->toArray();
    }

    /**
     * @param ArrayCollection $texts
     */
    public function setTexts($texts) {
        $this->texts = $texts;
    }

    /**
     * @return string
     */
    public function getBucket() {
        return $this->bucket;
    }

    /**
     * @param string $bucket
     */
    public function setBucket($bucket) {
        $this->bucket = $bucket;
    }

    /**
     * @return mixed
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug) {
        $this->slug = $slug;
    }


    /**
     * @return Text|null
     */
    public function getFirstText(){
        $texts = $this->getTexts();
        /** @var Text $text */
        foreach ($texts as $text){
            if ($text->isEnabled()) return $text;
        }
        return null;
    }


    /**
     * @return Image|null
     */
    public function getFirstImage(){
        $images = $this->getImages();
        /** @var Image $image */
        foreach ($images as $image){
            if ($image->isEnabled()) return $image;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isEnabled(){
        if ($this->getStatus() === 0) return false;
        return true;
    }

    /**
     * @return bool
     */
    public function isDisabled(){
        return !$this->isEnabled();
    }

    public function getSubsectionSortedByName(){
        $arrayGetSubsections = $this->getSubsections();
        usort($arrayGetSubsections,function($a,$b){
            return $a->getName()>$b->getName();
        });
        return $arrayGetSubsections;
    }


}