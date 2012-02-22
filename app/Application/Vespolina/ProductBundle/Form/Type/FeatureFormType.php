<?php

namespace Application\Vespolina\ProductBundle\Form\Type;
use Vespolina\ProductBundle\Form\Type\FeatureFormType as BaseFeatureFormType;

class FeatureFormType extends BaseFeatureFormType{
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Application\Vespolina\ProductBundle\Document\Feature',
        );
    }
}