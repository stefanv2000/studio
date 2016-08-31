<?php
namespace Frontend\Forms;

use Zend\Form\Element;
use Zend\Form\Element\Select;
use Zend\Form\ElementInterface;
use Zend\Form\Form;

/**
 * Create form for become a model page
 * @author Stefan Valea stefanvalea@gmail.com
 *
 */
class BecomeModelForm extends Form {
    public function __construct() {
        parent::__construct('becomeamodel');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'formbecomeamodel'
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'age',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Age',
            ),
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Phone',
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));

        $this->add(array(
            'name' => 'country',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Country',
            ),
        ));

        $this->add(array(
            'name' => 'wheredidyoufind',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Where did you find about us',
            ),
        ));

        $this->add(array(
            'name' => 'height',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Height',
            ),
        ));

        $this->add(array(
            'name' => 'bust',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Bust',
            ),
        ));

        $this->add(array(
            'name' => 'waist',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Waist',
            ),
        ));

        $this->add(array(
            'name' => 'hips',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Hips',
            ),
        ));

        $this->add(array(
            'name' => 'dress',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Dress',
            ),
        ));

        $this->add(array(
            'name' => 'shoe',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Shoe',
            ),
        ));

        $this->add(array(
            'name' => 'haircolour',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Hair colour',
            ),
        ));

        $this->add(array(
            'name' => 'eyecolour',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Eye colour',
            ),
        ));

        $this->add(array(
            'name' => 'shirt',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Shirt',
            ),
        ));

        $this->add(array(
            'name' => 'suit',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Suit',
            ),
        ));

        $this->add(array(
            'name' => 'inseam',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => '',
                'required' => 'required',
                'class' => 'inputclass',
            ),
            'options' => array(
                'label' => 'Inseam',
            ),
        ));

        $this->add(array(
            'name' => 'filelist',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'id' => 'filelistfield'
            ),
        ));


    }

}