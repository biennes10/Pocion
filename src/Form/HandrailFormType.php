<?php

namespace App\Form;

use App\Entity\Handrail;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HandrailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projectRepository = $this->getDoctrine()->getRepository(Product::class);
        $projectUserRepository = $this->getDoctrine()->getRepository(Product::class);
        $builder
            ->add('project', ChoiceType::class, [
                'choices' => Project::class
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Information' => 0,
                    'Incident' => 1
                ]
            ])
            ->add('urgency', ChoiceType::class, [
                'choices' => [
                    'Neutre' => 0,
                    'Modérée' => 1,
                    'Elevée' => 2,
                    'Extrême' => 3

                ]
            ])
            ->add('subject', TextType::class)
            ->add('content', CKEditorType::class, [
                'config' => [
                    'toolbar' => 'standard'
                ]
            ])
        ;
    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => Handrail::class,
//        ]);
//    }
}
