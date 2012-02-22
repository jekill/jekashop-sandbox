<?php
/**
 * (c) 2011 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Application\Vespolina\ProductBundle\Form\Type;

use \Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Application\Vespolina\ProductBundle\Form\Type\FeatureFormType;
use Vespolina\ProductBundle\Form\Type\ProductFormType;

/**
 * @author Richard Shank <develop@zestic.com>
 * @author Luis Cordova <cordoval@gmail.com>
 */
class ProductFormExtendedType extends ProductFormType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('price');
        $builder->add('slug');
        $builder->add('description', 'textarea');
        $builder->add('uploaded_image', 'file', array('required' => false));
        $builder->add('categories', 'document', array(
            'class' => 'Jeka\CategoryBundle\Document\Category',
            'empty_value' => false,
            'multiple' => true,
            'expanded' => false,
        ));

        $builder->add('tags_text','text',array('required'=>false));

        $builder->add('features', 'collection', array(
            'required' => false,
            'type' => new FeatureFormType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype_name' => 'features',
        ));

        $builder->add('options', 'collection', array(
            'type'           => 'vespolina_option_group',
            'allow_add'      => true,
            'allow_delete'   => true,
            'required'       => false,
            'by_reference'   => false,
            'prototype_name' => 'group',
        ));



        //        $builder->add('identifiers');

        //        $builder->add('image',
        //            'sonata_type_model',
        //            array(),
        //            array('edit' => 'list', 'link_parameters' => array('context' => 'default')));

        //        $builder->add('features', 'collection', array(
        //            'required' => false,
        //            'type' => new FeatureFormType(),
        //            'allow_add' => true,
        //            'allow_delete' => true,
        //            'by_reference' => false,
        //            'prototype'=>true,
        //            'prototype_name' => 'features',
        //
        //        ));

        //        $builder->remove('options');
        //        $builder->remove('features');
        //        $builder->remove('cartableProduct');
        //        $builder->remove('primaryIdentifierSet');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Application\Vespolina\ProductBundle\Document\Product',
            'validator_constraints' => new Collection(array(
                'price' => new \Symfony\Component\Validator\Constraints\Min(array('limit' => 0)),
            ))
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'vespolina_product';
    }
}
