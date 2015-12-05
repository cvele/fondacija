<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', 'entity', [
                'class' => 'AppBundle:Company',
                'choice_label' => 'Company',
            ])
            ->add('firstname', 'text', ['label' => "First Name", 'attr' => ['autofocus' => '']])
            ->add('lastname', 'text', ['label' => "Last Name"])
            ->add('businessEmail', 'email', ['label' => "Business E-Mail", 'required'=>false])
            ->add('personalEmail', 'email', ['label' => "Personal E-Mail", 'required'=>false])
            ->add('businessPhone', 'text', ['label' => "Business Phone", 'required'=>false])
            ->add('homePhone', 'text', ['label' => "Home Phone", 'required'=>false])
            ->add('mobilePhone', 'text', ['label' => "Mobile Phone", 'required'=>false])
            ->add('skype', 'text', ['label' => "Skype", 'required'=>false])
            ->add('save', 'submit', [
                'attr' => ['class' => 'btn-primary pull-right'],
                'label' => "Create Contact"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Person'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'person';
    }
}
