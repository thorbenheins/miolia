<?php
namespace Amilio\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChannelType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("name", "text")
//             ->add('id', "hidden")
            ->add('description', "textarea")
//             ->add('image', "file", array('required' => false))
            ->add('save', 'submit', array(
            'label' => 'Kanal speichern'            
        ));          
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amilio\CoreBundle\Entity\Channel'
        ));
    }

    public function getName()
    {
        return 'channel';
    }
}