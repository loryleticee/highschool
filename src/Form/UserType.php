<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mentors = $options["mentors"] ?? [];

        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "Lycéen" => 'ROLE_USER',
                    "Admin" => 'ROLE_ADMIN',
                ],
                // "multiple" => true,
            ])
            ->add('firstname')
            ->add('lastname')
            ->add('mentor', EntityType::class, [
                "class" => User::class,
                'choices' => $mentors,
                "placeholder" => "SELECTIONNEZ UN MENTOR",
                "required" => false
            ]);

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                    // transform the array to a string
                    return implode(', ', $tagsAsArray);
                },
                function ($tagsAsString) {
                    // transform the string back to an array
                    return explode(', ', $tagsAsString);
                }
            ));
        // ->add('lessons')

        if (in_array($options["mode"], ["new"])) {
            $builder->add('password', PasswordType::class);
        }

        if ($options["mode"] === "new") {
            $builder->add("image_url", FileType::class, [
                "required" => true,
                "data_class" => null
            ]);
        } else {
            $builder->add("image_url");
        }
        if ($options["mode"] === "edit") {
            $builder->add("new_image_url", FileType::class, [
                "required" => false,
                "mapped" => false,   
            ]);
        } 
       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'mentors' => [],
            "mode" => "",
            'from_action' => ""
        ]);
    }
}
