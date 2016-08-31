<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Entities\Entity;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @package Entities\Entity
 * @Table(name="images")
 * @ORM\Entity
 */
class Image {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $originalName;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $position;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageWidth;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageHeight;


    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageWidth1;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageHeight1;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageWidth2;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageHeight2;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageWidth3;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageHeight3;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageWidth4;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageHeight4;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageWidth5;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageHeight5;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageWidth6;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $imageHeight6;


    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbWidth;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbHeight;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbWidth1;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbHeight1;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbWidth2;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbHeight2;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbWidth3;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbHeight3;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbWidth4;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbHeight4;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbWidth5;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbHeight5;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbWidth6;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $thumbHeight6;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $status;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $tags;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $text;


    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $caption1;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $caption2;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $caption3;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $caption4;
    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     */
    protected $link;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     */
    protected $contentType;


    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     */
    protected $thumbname;

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="images")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id",onDelete="CASCADE")
     *
     * @var Section
     */
    protected $section;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getOriginalName() {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     */
    public function setOriginalName($originalName) {
        $this->originalName = $originalName;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getImageWidth() {
        return $this->imageWidth;
    }

    /**
     * @param int $imageWidth
     */
    public function setImageWidth($imageWidth) {
        $this->imageWidth = $imageWidth;
    }

    /**
     * @return int
     */
    public function getImageHeight() {
        return $this->imageHeight;
    }

    /**
     * @param int $imageHeight
     */
    public function setImageHeight($imageHeight) {
        $this->imageHeight = $imageHeight;
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getThumbWidth() {
        return $this->thumbWidth;
    }

    /**
     * @param int $thumbWidth
     */
    public function setThumbWidth($thumbWidth) {
        $this->thumbWidth = $thumbWidth;
    }

    /**
     * @return int
     */
    public function getThumbHeight() {
        return $this->thumbHeight;
    }

    /**
     * @param int $thumbHeight
     */
    public function setThumbHeight($thumbHeight) {
        $this->thumbHeight = $thumbHeight;
    }

    /**
     * @return string
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * @return Section
     */
    public function getSection() {
        return $this->section;
    }

    /**
     * @param Section $section
     */
    public function setSection($section) {
        $this->section = $section;
    }

    /**
     * @return int
     */
    public function getImageWidth1() {
        return $this->imageWidth1;
    }

    /**
     * @param int $imageWidth1
     */
    public function setImageWidth1($imageWidth1) {
        $this->imageWidth1 = $imageWidth1;
    }

    /**
     * @return int
     */
    public function getImageHeight1() {
        return $this->imageHeight1;
    }

    /**
     * @param int $imageHeight1
     */
    public function setImageHeight1($imageHeight1) {
        $this->imageHeight1 = $imageHeight1;
    }

    /**
     * @return int
     */
    public function getImageWidth2() {
        return $this->imageWidth2;
    }

    /**
     * @param int $imageWidth2
     */
    public function setImageWidth2($imageWidth2) {
        $this->imageWidth2 = $imageWidth2;
    }

    /**
     * @return int
     */
    public function getImageHeight2() {
        return $this->imageHeight2;
    }

    /**
     * @param int $imageHeight2
     */
    public function setImageHeight2($imageHeight2) {
        $this->imageHeight2 = $imageHeight2;
    }

    /**
     * @return int
     */
    public function getImageWidth3() {
        return $this->imageWidth3;
    }

    /**
     * @param int $imageWidth3
     */
    public function setImageWidth3($imageWidth3) {
        $this->imageWidth3 = $imageWidth3;
    }

    /**
     * @return int
     */
    public function getImageHeight3() {
        return $this->imageHeight3;
    }

    /**
     * @param int $imageHeight3
     */
    public function setImageHeight3($imageHeight3) {
        $this->imageHeight3 = $imageHeight3;
    }

    /**
     * @return mixed
     */
    public function getImageWidth4() {
        return $this->imageWidth4;
    }

    /**
     * @param mixed $imageWidth4
     */
    public function setImageWidth4($imageWidth4) {
        $this->imageWidth4 = $imageWidth4;
    }

    /**
     * @return int
     */
    public function getImageHeight4() {
        return $this->imageHeight4;
    }

    /**
     * @param int $imageHeight4
     */
    public function setImageHeight4($imageHeight4) {
        $this->imageHeight4 = $imageHeight4;
    }

    /**
     * @return mixed
     */
    public function getImageWidth5() {
        return $this->imageWidth5;
    }

    /**
     * @param mixed $imageWidth5
     */
    public function setImageWidth5($imageWidth5) {
        $this->imageWidth5 = $imageWidth5;
    }

    /**
     * @return int
     */
    public function getImageHeight5() {
        return $this->imageHeight5;
    }

    /**
     * @param int $imageHeight5
     */
    public function setImageHeight5($imageHeight5) {
        $this->imageHeight5 = $imageHeight5;
    }

    /**
     * @return mixed
     */
    public function getImageWidth6() {
        return $this->imageWidth6;
    }

    /**
     * @param mixed $imageWidth6
     */
    public function setImageWidth6($imageWidth6) {
        $this->imageWidth6 = $imageWidth6;
    }

    /**
     * @return int
     */
    public function getImageHeight6() {
        return $this->imageHeight6;
    }

    /**
     * @param int $imageHeight6
     */
    public function setImageHeight6($imageHeight6) {
        $this->imageHeight6 = $imageHeight6;
    }

    /**
     * @return mixed
     */
    public function getThumbWidth1() {
        return $this->thumbWidth1;
    }

    /**
     * @param mixed $thumbWidth1
     */
    public function setThumbWidth1($thumbWidth1) {
        $this->thumbWidth1 = $thumbWidth1;
    }

    /**
     * @return int
     */
    public function getThumbHeight1() {
        return $this->thumbHeight1;
    }

    /**
     * @param int $thumbHeight1
     */
    public function setThumbHeight1($thumbHeight1) {
        $this->thumbHeight1 = $thumbHeight1;
    }

    /**
     * @return mixed
     */
    public function getThumbWidth2() {
        return $this->thumbWidth2;
    }

    /**
     * @param mixed $thumbWidth2
     */
    public function setThumbWidth2($thumbWidth2) {
        $this->thumbWidth2 = $thumbWidth2;
    }

    /**
     * @return int
     */
    public function getThumbHeight2() {
        return $this->thumbHeight2;
    }

    /**
     * @param int $thumbHeight2
     */
    public function setThumbHeight2($thumbHeight2) {
        $this->thumbHeight2 = $thumbHeight2;
    }

    /**
     * @return mixed
     */
    public function getThumbWidth3() {
        return $this->thumbWidth3;
    }

    /**
     * @param mixed $thumbWidth3
     */
    public function setThumbWidth3($thumbWidth3) {
        $this->thumbWidth3 = $thumbWidth3;
    }

    /**
     * @return int
     */
    public function getThumbHeight3() {
        return $this->thumbHeight3;
    }

    /**
     * @param int $thumbHeight3
     */
    public function setThumbHeight3($thumbHeight3) {
        $this->thumbHeight3 = $thumbHeight3;
    }

    /**
     * @return mixed
     */
    public function getThumbWidth4() {
        return $this->thumbWidth4;
    }

    /**
     * @param mixed $thumbWidth4
     */
    public function setThumbWidth4($thumbWidth4) {
        $this->thumbWidth4 = $thumbWidth4;
    }

    /**
     * @return int
     */
    public function getThumbHeight4() {
        return $this->thumbHeight4;
    }

    /**
     * @param int $thumbHeight4
     */
    public function setThumbHeight4($thumbHeight4) {
        $this->thumbHeight4 = $thumbHeight4;
    }

    /**
     * @return mixed
     */
    public function getThumbWidth5() {
        return $this->thumbWidth5;
    }

    /**
     * @param mixed $thumbWidth5
     */
    public function setThumbWidth5($thumbWidth5) {
        $this->thumbWidth5 = $thumbWidth5;
    }

    /**
     * @return int
     */
    public function getThumbHeight5() {
        return $this->thumbHeight5;
    }

    /**
     * @param int $thumbHeight5
     */
    public function setThumbHeight5($thumbHeight5) {
        $this->thumbHeight5 = $thumbHeight5;
    }

    /**
     * @return mixed
     */
    public function getThumbWidth6() {
        return $this->thumbWidth6;
    }

    /**
     * @param mixed $thumbWidth6
     */
    public function setThumbWidth6($thumbWidth6) {
        $this->thumbWidth6 = $thumbWidth6;
    }

    /**
     * @return int
     */
    public function getThumbHeight6() {
        return $this->thumbHeight6;
    }

    /**
     * @param int $thumbHeight6
     */
    public function setThumbHeight6($thumbHeight6) {
        $this->thumbHeight6 = $thumbHeight6;
    }

    /**
     * @return string
     */
    public function getCaption1() {
        return $this->caption1;
    }

    /**
     * @param string $caption1
     */
    public function setCaption1($caption1) {
        $this->caption1 = $caption1;
    }

    /**
     * @return mixed
     */
    public function getCaption2() {
        return $this->caption2;
    }

    /**
     * @param mixed $caption2
     */
    public function setCaption2($caption2) {
        $this->caption2 = $caption2;
    }

    /**
     * @return mixed
     */
    public function getCaption3() {
        return $this->caption3;
    }

    /**
     * @param mixed $caption3
     */
    public function setCaption3($caption3) {
        $this->caption3 = $caption3;
    }

    /**
     * @return mixed
     */
    public function getCaption4() {
        return $this->caption4;
    }

    /**
     * @param mixed $caption4
     */
    public function setCaption4($caption4) {
        $this->caption4 = $caption4;
    }

    /**
     * @return mixed
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * @param mixed $contentType
     */
    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    /**
     * @return mixed
     */
    public function getThumbname() {
        return $this->thumbname;
    }

    /**
     * @param mixed $thumbname
     */
    public function setThumbname($thumbname) {
        $this->thumbname = $thumbname;
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



}