<?php
namespace Amilio\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', "text")
            ->add("name", "text")
            ->add('description', "textarea")
            ->add('price', "money")
            ->add('image', "url")
            ->add('imageThumbnail', "url")

            ->add('save', 'submit', array(       'label' => 'Produkt erstellen' ))
        ;          
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amilio\CoreBundle\Entity\Product'
        ));
    }

    public function getName()
    {
        return 'product';
    }
}