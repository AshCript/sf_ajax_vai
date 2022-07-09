<?php

namespace App\Form;

use App\Entity\Pannier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PannierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('payStatus')
            ->add('paidAt')
            ->add('validated')
            ->add('validatedAt')
            ->add('delivered')
            ->add('deliveredAt')
            ->add('user')
            ->add('produit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pannier::class,
        ]);
    }
}
