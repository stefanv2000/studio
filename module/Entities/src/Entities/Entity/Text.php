<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Entities\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Text
 * @package Entities\Entity
 * @ORM\Table(name="texts")
 * @ORM\Entity()
 */
class Text {
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
    protected $header;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $body;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $tags;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $status;
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $position;
    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="texts")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id",onDelete="CASCADE")
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
    public function getHeader() {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header) {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body) {
        $this->body = $body;
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