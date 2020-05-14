<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\NotifUser;
use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\Helper;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProjetController extends AbstractController
{
    /**
     * @Route("/projet", name="projet")
     * @IsGranted("ROLE_ADMIN")
     */
    public function projet(ProjectRepository $projectRepository)
    {
        $projets = $projectRepository->findAll();
        return $this->render('user/HAF/projet.html.twig', [
            'projets' => $projets,
        ]);
    }

    /**
     * @Route("/projet/new", name="projet_new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function projetNew(Request $request, ObjectManager $manager, ProjectRepository $projectRepository, Helper $helper, UserRepository $userRepository)
    {
        $projets = $projectRepository->findAll();

        $projet = new Project();
        $form = $this->createFormBuilder($projet)
            ->add('name', TextType::class, [
                'label' => 'Nom du Projet',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'attr' => ['autocomplete' => 'off'],
                'choices'=>[
                    'Commencé/En cours'=>0,
                    'Complété'=>1
                ]
            ])
            ->add('participants',EntityType::class,[
                'class'=>User::class,
                'multiple'=>true,
                'choice_label'=>'username'
            ])

        ;



        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $data = $form->getData();
            $projet
                ->setName($data->getName())
                ->setStatus($data->getStatus())
                ->setCreatedAt(new \DateTime())
                ->setAuthor($this->getUser())
            ;


            foreach ($data->getParticipants() as $participant){
                $projet_user = new ProjectUser();
                $projet_user->setProject($projet);
                $projet_user->setUser($participant);

                $content = "Vous avez été ajouté au projet : ".$projet->getName().", au boulot!";
                $helper->notif($participant, "Bienvenue dans le projet !",$content,null,"added_to_project,neutral","project-diagram",null,$this->getUser(), $userRepository,$manager);
                $manager->persist($projet_user);
            }

            $manager->persist($projet);
            $manager->flush();

            return $this->redirectToRoute('projet');
            //return new Response("ok");
        }

        return $this->render('user/HAF/projet.html.twig', [
            'projets' => $projets,
            'form' => $form->createView(),
            'edit' => false
        ]);
    }

    /**
     * @Route("/projet/{projet}", name="projet_article")
     * @IsGranted("ROLE_ADMIN")
     */
    public function projetArticle(Project $projet, ProjectRepository $projetRepository)
    {
        $projets = $projetRepository->findAll();
        $tz  = new \DateTimeZone('Europe/Paris');

        return $this->render('user/HAF/projet.html.twig', [
            'article' => $projet,
            'projets' => $projets
        ]);
    }
    /**
     * @Route("/projet/{projet}/edit", name="projet_article_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function projetArticleEdit(Project $projet, Request $request, ObjectManager $manager, ProjectRepository $projectRepository)
    {

        $projets = $projectRepository->findAll();

        $status = [0=>"Commencé/En cours",1=>"Complété"];
        $form = $this->createFormBuilder($projet)
            ->add('name', TextType::class, [
                'label' => 'Nom du projet',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
        ;




        $form = $form->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $data = $form->getData();
            $projet
                ->setName($data->getName())
                ->setStatus($data->getStatus())

            ;

            $manager->persist($projet);
            $manager->flush();

            return $this->redirectToRoute('projet_article', ['projet' => $projet->getId()]);

        }

        return $this->render('user/HAF/projet.html.twig', [
            'article' => $projet,
            'projets' => $projets,
            'form' => $form->createView(),
            'edit' => true
        ]);
    }

    /**
     * @Route("/projet/{projet}/delete", options={"expose"=true}, name="project_article_delete")
     * @IsGranted("ROLE_USER")
     */
    public function handrailArticleDelete(Project $projet, ObjectManager $manager)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $projet->setState(1);
            $manager->flush();
        } else {
            if ($handrail->getUser() == $this->getUser()) {
                $handrail->setStatus(1);
                $manager->flush();
            } else {
                return $this->redirectToRoute('projet_article', ['projet' => $projet->getId()]);
            }
        }

        return $this->redirectToRoute('projet');
    }

}
