<?php

namespace Zentrium\Bundle\MapBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentrium\Bundle\MapBundle\Entity\Map;

class MapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'zentrium_map.map.field.name',
            ])
            ->add('projection', TextType::class, [
                'label' => 'zentrium_map.map.field.projection',
            ])
            ->add('layers', MapLayerCollectionType::class, [
                'layer_repository' => $options['layer_repository'],
                'collection' => $options['map_layers'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'zentrium.form.save',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $map = $event->getForm()->getData();
                foreach ($map->getLayers() as $layer) {
                    $layer->setMap($map);
                }
            }, 10)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Map::class,
            'map_layers' => null,
            'layer_repository' => null,
        ]);
    }

    public function getParent()
    {
        return FormType::class;
    }
}
