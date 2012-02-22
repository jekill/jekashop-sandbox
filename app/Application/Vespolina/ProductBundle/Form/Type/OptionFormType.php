<?php

namespace Application\Vespolina\ProductBundle\Form\Type;

use \Symfony\Component\Form\FormBuilder;
use Vespolina\ProductBundle\Form\Type\OptionFormType as BaseOptionFormType;;

class OptionFormType extends BaseOptionFormType{


    public function buildForm(FormBuilder $builder, array $options){
        parent::buildForm($builder,$options);
        $builder->remove('display');
    }
}

