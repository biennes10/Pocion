<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     * @IsGranted("ROLE_ADMIN")
     */
    public function user(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $this->render('user/HAF/user.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/new", name="user_new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function userNew(Request $request, ObjectManager $manager, UserRepository $userRepository, ProjectRepository $projectRepository, UserPasswordEncoderInterface $encoder)
    {
        $users = $userRepository->findAll();

        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('dateOfBirth', DateTimeType::class, [
                'label' => 'Date de naissance',
                'years' => range(date('Y') - 70, date('Y')),
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('username', TextType::class, [
                'label' => 'Login',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Homme' => 1,
                    'Femme' => 0,
                    'Autre' => 2
                ]
            ])
            ;

        if ($this->isGranted("ROLE_SUPER_ADMIN")) {
            $form = $form->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    'Utilisateur' => 0,
                    'Administrateur' => 1,
                    'Super Administrateur' => 2
                ]
            ]);
        } elseif ($this->isGranted("ROLE_ADMIN")) {
            $form = $form->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    'Utilisateur' => 0,
                    'Administrateur' => 1
                ]
            ]);
        }

        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $data = $form->getData();
            $user
                ->setFirstName($data->getFirstName())
                ->setLastName($data->getLastName())
                ->setDateOfBirth($data->getDateOfBirth())
                ->setUsername($data->getUsername())
                ->setEmail($data->getEmail())
                ->setPassword($encoder->encodePassword($user, $data->getPassword()))
                ->setGender($data->getGender())
                ->setCreatedAt(new \DateTime())
            ;

            if ($form['role']->getData() == 0) {
                $user->setRoles(["ROLE_USER"]);
            } elseif ($form['role']->getData() == 1) {
                $user->setRoles(["ROLE_ADMIN"]);
            } elseif ($form['role']->getData() == 2) {
                $user->setRoles(["ROLE_SUPER_ADMIN"]);
            }

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('user');

        }

        return $this->render('user/HAF/user.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'edit' => false
        ]);
    }

    /**
     * @Route("/user/{user}", name="user_article")
     * @IsGranted("ROLE_ADMIN")
     */
    public function userArticle(User $user, UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        $tz  = new \DateTimeZone('Europe/Paris');
        $age = \DateTime::createFromFormat('d/m/Y', $user->getDateOfBirth()->format('d/m/Y'), $tz)
            ->diff(new \DateTime('now', $tz))
            ->y;
        return $this->render('user/HAF/user.html.twig', [
            'article' => $user,
            'users' => $users,
            'age' => $age
        ]);
    }

    /**
     * @Route("/user/{user}/edit", name="user_article_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function userArticleEdit(User $user, Request $request, ObjectManager $manager, UserRepository $userRepository, ProjectRepository $projectRepository, UserPasswordEncoderInterface $encoder)
    {
        if ($this->isGranted("ROLE_ADMIN") && in_array($user->getRoles()[0], ["ROLE_ADMIN", "ROLE_SUPER_ADMIN"])) {
            if (!$this->isGranted("ROLE_SUPER_ADMIN")) {
                return $this->redirectToRoute("user_article", ['user' => $user->getId()]);
            }
        }

        $users = $userRepository->findAll();
        $roles = ["ROLE_USER" => 0, "ROLE_ADMIN" => 1, "ROLE_SUPER_ADMIN" => 2];

        $form = $this->createFormBuilder($user)
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('dateOfBirth', DateTimeType::class, [
                'label' => 'Date de naissance',
                'years' => range(date('Y') - 70, date('Y')),
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('username', TextType::class, [
                'label' => 'Login',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'off'],
                'required' => false,
                'empty_data' => ''
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Homme' => 1,
                    'Femme' => 0,
                    'Autre' => 2
                ]
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    'Utilisateur' => 0,
                    'Administrateur' => 1,
                    'Super Administrateur' => 2
                ],
                'data' => $roles[$user->getRoles()[0]]
            ])
            ;

        if ($this->isGranted("ROLE_SUPER_ADMIN")) {
            $form = $form->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    'Utilisateur' => 0,
                    'Administrateur' => 1,
                    'Super Administrateur' => 2
                ]
            ]);
        } elseif ($this->isGranted("ROLE_ADMIN")) {
            $form = $form->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    'Utilisateur' => 0,
                    'Administrateur' => 1
                ]
            ]);
        }

        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $data = $form->getData();
            $user
                ->setFirstName($data->getFirstName())
                ->setLastName($data->getLastName())
                ->setDateOfBirth($data->getDateOfBirth())
                ->setUsername($data->getUsername())
                ->setEmail($data->getEmail())
                ->setGender($data->getGender())
            ;

            if ($form['password']->getData() !== "") {
                $data->setPassword($encoder->encodePassword($user, $form['password']->getData()));
            }

            if ($form['role']->getData() == 0) {
                $user->setRoles(["ROLE_USER"]);
            } elseif ($form['role']->getData() == 1) {
                $user->setRoles(["ROLE_ADMIN"]);
            } elseif ($form['role']->getData() == 2) {
                $user->setRoles(["ROLE_SUPER_ADMIN"]);
            }

            $manager->flush();

            return $this->redirectToRoute('user_article', ['user' => $user->getId()]);

        }

        return $this->render('user/HAF/user.html.twig', [
            'article' => $user,
            'users' => $users,
            'form' => $form->createView(),
            'edit' => true
        ]);
    }

    /**
     * @Route("/user/{user}/delete", options={"expose"=true}, name="user_article_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function userArticleDelete(User $user, ObjectManager $manager, ProjectUserRepository $projectUserRepository)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            if (($this->isGranted("ROLE_SUPER_ADMIN") && $user->getRoles()[0] == "ROLE_ADMIN") || ($this->isGranted("ROLE_ADMIN") && $user->getRoles()[0] == "ROLE_USER")) {
                $manager->remove($user);
                $manager->flush();
                return $this->redirectToRoute("user");
            } else {
                return $this->redirectToRoute("user_article", ['user' => $user->getId()]);
            }
        } else {
            return $this->redirectToRoute("user");
        }

    }
}
