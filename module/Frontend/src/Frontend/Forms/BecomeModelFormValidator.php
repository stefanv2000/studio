<?php
namespace Frontend\Forms;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Digits;
use Zend\Validator\Hostname;

/**
 * Validates the become a model form
 * @author Stefan Valea stefanvalea@gmail.com
 *
 */
class BecomeModelFormValidator implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter)
        {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $fillinstring = " - please fill in this field";

            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'NAME '.$fillinstring ,
                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'age',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'age '.$fillinstring ,
                            ),
                        ),
                    ),
                ),
            )));




            $inputEmail = $factory->createInput(array(
                'name' => 'email',
                'required' => true,
                'filters' => array (
                    array (
                        'name' => 'StripTags'
                    ),
                    array (
                        'name' => 'StringTrim'
                    )
                ),

                'validators' => array (
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'EMAIL '.$fillinstring,
                            ),
                        ),
                    ),
                    /*array (
                        'name' => 'EmailAddress',
                        'options' => array (
                            'allow' => Hostname::ALLOW_ALL,
                            'encoding' => 'UTF-8',
                            'min' => 5,
                            'max' => 255,
                            'messages' => array (
                                \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Email address format is invalid'
                            )
                        ),
                    ),*/

                ),
            ));
            $inputFilter->add($inputEmail);

            $inputFilter->add($factory->createInput(array(
                'name' => 'phone',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'phone '.$fillinstring ,
                            ),
                        ),
                    ),
                ),
            )));


            $inputFilter->add($factory->createInput(array(
                'name' => 'country',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'country '.$fillinstring ,
                            ),
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'height',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'height '.$fillinstring ,
                            ),
                        ),
                    ),
                ),
            )));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}