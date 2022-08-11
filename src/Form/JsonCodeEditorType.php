<?php

namespace App\Form;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CodeEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JsonCodeEditorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->addModelTransformer(new CallbackTransformer(
                fn($object) => json_encode($object),
                fn($json) => json_decode($json)
            ));
    }
    public function getParent(): string
    {
        return CodeEditorType::class;
    }

}
