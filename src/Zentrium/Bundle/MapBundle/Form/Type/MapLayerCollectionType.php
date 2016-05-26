<?php

namespace Zentrium\Bundle\MapBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentrium\Bundle\MapBundle\Form\DataTransformer\MapLayerCollectionToArrayTransformer;

class MapLayerCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new MapLayerCollectionToArrayTransformer($options['layer_repository'], $options['collection']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'layer_repository' => null,
            'collection' => null,
        ]);
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
