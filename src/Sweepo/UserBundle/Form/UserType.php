<?php

namespace Sweepo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', ['read_only' => true, 'required' => true]);
        $builder->add('email', 'text', ['required' => true]);
        $builder->add('token', 'hidden');
        $builder->add('token_secret', 'hidden');
        $builder->add('screen_name', 'hidden');
        $builder->add('twitter_id', 'hidden');
        $builder->add('profile_image_url', 'hidden');
        $builder->add('lang', 'hidden');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sweepo\UserBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'user';
    }
}