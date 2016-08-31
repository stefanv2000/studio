<?php
namespace Entities\Mapper;

use Entities\Entity\Section;
use Doctrine\ORM\Query;
class SectionMapper extends AbstractMapper {

    /**
     * @param $parent_id
     * @return array
     */
    public function findSectionWithParent($parent_id){
        $repo = $this->getRepository('section');
        /** @var Section $parent */
        $parent = $repo->find($parent_id);
        return $repo->findBy(array('parent' => $parent),array('position' => 'ASC'));
    }

    /**
     * @param $sectionid
     * @return null|Section
     */
    public function findSectionById($sectionid){
        $repo = $this->getRepository('section');
        /** @var Section $parent */
        $section = $repo->find($sectionid);
        return $section;
    }


    /**
     * @param $parent_id
     * @return array
     */
    public function findSectionWithParentByName($parent_id,$sectionname){
        $repo = $this->getRepository('section');
        /** @var Section $parent */
        $parent = $repo->find($parent_id);
        return $repo->findOneBy(array('parent' => $parent,'name' => $sectionname));
    }

    public function findSectionBySlug($slug){
        $repo = $this->getRepository('section');
        return $repo->findOneBy(array('slug' => $slug));
    }

    public function findSectionBySlugWithParents($slug,$parents,$isNullParent=false,$checkedDisabled=false){

        $repo = $this->getRepository('section');
        /** @var Section $section */
        foreach ($repo->findBy(array('slug' => $slug)) as $section){
            $cparent = $section;$result = true;
            foreach ($parents as $parent){
                if ($parent == null) continue;
                if ($parent == '') continue;
                $cparent = $cparent->getParent();
                if (($cparent == null)||($cparent->getSlug()!==$parent)) {
                    $result=false;
                    break;
                }


            }
            if ($isNullParent){
                if (($cparent!==null)&&($cparent->getParent()!==null)) continue;
            }
            if (($checkedDisabled)&&($section->isDisabled())) continue;
            if ($result) {

                return $section;
            }
        }

        return null;
    }

    /**
     * @param $sectionname string
     * @param $parentid integer
     * @return null|Section
     */
    public function findSectionBySlugWithParentId($sectionname, $parentid){
        $repo = $this->getRepository('section');
        /** @var Section $parent */
        $parent = $repo->find($parentid);
        return $repo->findOneBy(array('parent' => $parent,'slug' => $sectionname));
    }


}

?>