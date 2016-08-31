<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Frontend\Infrastructure\Domain;


use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Entities\Util\Serializer;

class FoodDrink {

    /**
     * @var SectionMapper
     */
    private $sectionMapper;
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Intro constructor.
     * @param $sectionMapper
     */
    public function __construct($sectionMapper, $serializer) {
        $this->sectionMapper = $sectionMapper;
        $this->serializer = $serializer;
    }

    public function getPressContentArray($parents) {
        $section = $this->sectionMapper->findSectionBySlugWithParents('press', $parents);
        $arrayPress = array();
        $slug = '';
        foreach ($parents as $part) $slug = '/' . $part . $slug;
        /** @var Section $pressSect */
        foreach ($section->getSubsections() as $pressSect) {
            if ($pressSect->isDisabled()) continue;

            $arrayElem = array(
                'name' => $pressSect->getName(),
                'slug' => $slug . '/' . $section->getSlug() . '/' . $pressSect->getSlug()
            );

            $image = $pressSect->getFirstImage();
            if ($image !== null) {
                $arrayElem['imagepath'] = "http://" . $pressSect->getBucket() . ".s3.amazonaws.com/" . $pressSect->getPath() . '/_images3/' . $image->getOriginalName();
            }

            $arrayPress[] = $arrayElem;
        }


        return $arrayPress;
    }


    public function getContentPressPost($postname, $name, $link) {

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($postname, array("press", $name, $link));

        $arrayResult = [];

        $res = array_filter($section->getSubsections(), function ($section) {
            return true;
            if ($section->getSlug() == 'gallery') return true;
            if ($section->getSlug() == 'text') return true;
            return false;
        });
        if (count($res) > 0) {
            /** @var Section $resSect */
            $resSect = $res[0];
            $presstype = 'gallery';
            if ($resSect->getSlug() == 'text') {
                $arrayResult = $this->getPressContentText($resSect);
            } else {
                $arrayResult = $this->getPressContentGallery($resSect);
            }
        }


        return $arrayResult;

    }

    public function getPressPostLinks($postname, $name, $link){
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlugWithParents($postname, array("press", $name, $link));

        $array = [
            'previousarticle' => '',
            'nextarticle' => '',
        ];


        $isnext = false;
        $nextpost = null;
        $previouspost = null;
        $first=null;
        $last=null;

        $subsections = $section->getParent()->getSubsections();

        /** @var Section $post */
        foreach ($subsections as $post){
            if ($post->isDisabled()) continue;

            if ($first===null) $first=$post;
            if ($isnext){
                $nextpost=$post;
                $isnext=false;
            }

            if ($post->getId() === $section->getId()){
                $previouspost = $last;
                $isnext = true;
            }
            $last=$post;
        }

        if ($nextpost==null) $nextpost=$first;;
        if ($previouspost==null) $previouspost = $last;
        $array['nextarticle'] = '/'.$section->getParent()->getParent()->getParent()->getSlug().'/'.$section->getParent()->getParent()->getSlug().'/'.$section->getParent()->getSlug().'/'.$nextpost->getSlug();
        $array['previousarticle'] = '/' . $section->getParent()->getParent()->getParent()->getSlug() .
            '/' . $section->getParent()->getParent()->getSlug() .
            '/' . $section->getParent()->getSlug() .
            '/' . $previouspost->getSlug();

        return $array;
    }

    public function getPressContentText(Section $section) {

        $text = $section->getFirstText();
        if ($text == null) return [];


        return array(
            'header' => (($text->getHeader() === '') || ($text->getHeader() === null) || ($text->getHeader() === 'null')) ? $section->getParent()->getName() : $text->getHeader(),
            'body' => strip_tags($text->getBody(),"<a><br><br/><br /><b><i><strong><img><video><iframe>")
        );

    }


    public function getPressContentGallery(Section $section) {


        $path = "http://" . $section->getBucket() . ".s3.amazonaws.com/" . $section->getPath();
        $serial = $this->serializer;
        /**
         * @param $image Image
         * @return array
         */
        $filterf = function ($image) use ($serial, $path, $section) {
            $sImage = $serial->serialize($image);
            $sImage['linkpath'] = $path;

            return $sImage;


        };

        $array = array_map($filterf, array_values(array_filter($section->getImages(), function ($image) {
            if ($image->getStatus() === 1) return true;
            return false;
        })));

        return $array;
    }
}