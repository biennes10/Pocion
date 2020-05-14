<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Project;
use App\Repository\AgendaRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\UserRepository;
use App\Service\Helper;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AgendaController extends AbstractController
{
    /**
     * @Route("/agenda", options={"expose"=true}, name="agenda")
     */
    public function agenda(AgendaRepository $agendaRepository, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, Helper $helper)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $projectUsers = $projectRepository->findAll();
            foreach ($projectUsers as $projectUser) {
                $projectIds[] = $projectUser->getId();
            }
        } else {
            $projectUsers = $projectUserRepository->getUserProjects($this->getUser()->getId());
            $projectIds = $helper->projectIds($projectUsers, $projectRepository);
        }

        $agendas = $agendaRepository->recent(30, $projectIds);
        $calendar = [];
        foreach ($agendas as $agenda) {
            $calendar[] = [
                "title" => $agenda->getSubject(),
                "start" => date_format($agenda->getStartAt(), 'Y-m-d H:i:s'),
                "end" => date_format($agenda->getEndAt(), 'Y-m-d H:i:s'),
                "url" => $this->generateUrl('agenda_article', ['agenda' => $agenda->getId()])
            ];
        }

        return $this->render('user/HAF/agenda.html.twig', [
            'page' => 'agenda',
            'agendas' => $agendas,
            'calendar' => $calendar
        ]);
    }

    /**
     * @Route("/agenda/new", name="agenda_new")
     * @IsGranted("ROLE_USER")
     */
    public function agendaNew(Request $request, ObjectManager $manager, AgendaRepository $agendaRepository, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, Helper $helper, UserRepository $userRepository)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $projectUsers = $projectRepository->findAll();
            $projectIds = [];
            foreach ($projectUsers as $projectUser) {
                $projectIds[] = $projectUser->getId();
            }
            $projects = $projectRepository->findAll();
        } else {
            $projectUsers = $projectUserRepository->getUserProjects($this->getUser()->getId());
            $projectIds = $helper->projectIds($projectUsers, $projectRepository);
            $projects = $projectRepository->getProjects($projectIds);
        }

        $agendas = $agendaRepository->recent(30, $projectIds);

        $agenda = new Agenda();
        $form = $this->createFormBuilder($agenda)
            ->add('subject', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Donnez un titre...',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('project', EntityType::class, [
                'label' => 'Projet',
                'class' => Project::class,
                'choice_label' => 'name',
                'choices' => $projects,
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'Début',
                'data' => (new \DateTime())->setTimezone(new \DateTimeZone("Europe/Paris"))
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'Fin',
                'data' => (new \DateTime())->setTimezone(new \DateTimeZone("Europe/Paris"))
            ])
            ->add('content', CKEditorType::class, [
                'label' => false,
                'config' => [
                    'toolbar' => 'standard'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $agenda
                ->setUser($this->getUser())
                ->setProject($projectRepository->findOneById($data->getProject()))
                ->setCreatedAt(new \DateTime())
                ->setStatus(0)
                ->setSubject($data->getSubject())
            ;

            if ($data->getContent() !== null) {
                $agenda->setContent($data->getContent());
            }
            if ($data->getStartAt() !== null) {
                $agenda->setStartAt($data->getStartAt());
            }
            if ($data->getEndAt() !== null) {
                $agenda->setEndAt($data->getEndAt());
            }

            $manager->persist($agenda);
            $manager->flush();
            foreach($agenda->getProject()->getProjectUsers() as $project_users){

                $user = $project_users->getUser();
                $thisUser = $this->getUser();
                $path = "agenda_article,".$agenda->getId();

                $helper->notif($user,"Ajout au projet", "L'utilisateur ".$thisUser->getFirstName()." ".$thisUser->getLastName()." vient d'ajouter un agenda au projet : ".$agenda->getProject()->getName(),$path,"add_to_project,neutral","calendar",null,$thisUser,$userRepository,$manager);


            }
            return $this->redirectToRoute('agenda');
        }

        return $this->render('user/HAF/agenda.html.twig', [
            'agendas' => $agendas,
            'form' => $form->createView(),
            'edit' => false
        ]);
    }

    /**
     * @Route("/agenda/{agenda}", options={"expose"=true}, name="agenda_article")
     */
    public function agendaArticle(Agenda $agenda, AgendaRepository $agendaRepository, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, Helper $helper)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $projectUsers = $projectRepository->findAll();
            foreach ($projectUsers as $projectUser) {
                $projectIds[] = $projectUser->getId();
            }
        } else {
            $projectUsers = $projectUserRepository->getUserProjects($this->getUser()->getId());
            $projectIds = $helper->projectIds($projectUsers, $projectRepository);
        }

        if (!$agenda) {
            return $this->redirectToRoute('agenda');
        }

        $agendas = $agendaRepository->recent(30, $projectIds);

        return $this->render('user/HAF/agenda.html.twig', [
            'agendas' => $agendas,
            'article' => $agenda
        ]);
    }

    /**
     * @Route("/agenda/{agenda}/edit", name="agenda_article_edit")
     * @IsGranted("ROLE_USER")
     */
    public function agendaArticleEdit(Agenda $agenda, Request $request, ObjectManager $manager,AgendaRepository $agendaRepository, ProjectRepository $projectRepository, ProjectUserRepository $projectUserRepository, Helper $helper)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $projectUsers = $projectRepository->findAll();
            foreach ($projectUsers as $projectUser) {
                $projectIds[] = $projectUser->getId();
            }
            $projects = $projectRepository->findAll();

        } else {

            if ($agenda->getUser() !== $this->getUser()) {
                return $this->redirectToRoute('agenda_article', ['agenda' => $agenda->getId()]);
            }
            $projectUsers = $projectUserRepository->getUserProjects($this->getUser()->getId());
            $projectIds = $helper->projectIds($projectUsers, $projectRepository);
            $projects = $projectRepository->getProjects($projectIds);

        }

        $agendas = $agendaRepository->recent(30, $projectIds);

        $form = $this->createFormBuilder($agenda)
            ->add('subject', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Donnez un titre',
                    'autocomplete' => 'off'
                ],
                'data' => $agenda->getSubject()
            ])
            ->add('project', EntityType::class, [
                'label' => 'Projet',
                'class' => Project::class,
                'choice_label' => 'name',
                'choices' => $projects,
                'data' => $agenda->getProject()
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'Début',
                'data' => $agenda->getStartAt()
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'Fin',
                'data' => $agenda->getEndAt()
            ])
            ->add('content', CKEditorType::class, [
                'label' => false,
                'required' => false,
                'config' => [
                    'toolbar' => 'standard'
                ],
                'data' => $agenda->getContent()
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $data = $form->getData();

            $agenda
                ->setProject($projectRepository->findOneById($data->getProject()))
                ->setStatus(0)
                ->setSubject($data->getSubject())
            ;

            if ($data->getContent() !== null) {
                $agenda->setContent($data->getContent());
            }
            if ($data->getStartAt() !== null) {
                $agenda->setStartAt($data->getStartAt());
            }
            if ($data->getEndAt() !== null) {
                $agenda->setEndAt($data->getEndAt());
            }

            $manager->flush();

            return $this->redirectToRoute('agenda_article', ['agenda' => $agenda->getId()]);

        }

        return $this->render('user/HAF/agenda.html.twig', [
            'agendas' => $agendas,
            'article' => $agenda,
            'form' => $form->createView(),
            'edit' => true
        ]);
    }

    /**
     * @Route("/agenda/{agenda}/delete", name="agenda_article_delete", options={"expose"=true})
     * @IsGranted("ROLE_USER")
     */
    public function handrailArticleDelete(Agenda $agenda, ObjectManager $manager,UserRepository $userRepository, Helper $helper)
    {
        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $manager->remove($agenda);
            $manager->flush();
        }else {
            $user = $this->getUser();
            if ($this->isGranted('ROLE_ADMIN')) {
                $canDelete = true;

            } else {
                if ($agenda->getUser() == $this->getUser()) {
                    $canDelete = true;
                } else {
                    $canDelete = false;

                }
            }

            if ($canDelete) {

                $agenda->setStatus(1);
                $users = $userRepository->findAll();
                foreach ($users as $user2) {
                    if ($user2->getRoles() == array("ROLE_SUPER_ADMIN")) {
                        $superAdmin = $user2;
                        break;
                    }
                }
                //$superAdmin = $userRepository->findOneByRoles(["ROLE_SUPER_ADMIN"]);

                $helper->notif($superAdmin, "Demande de suppression", "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " veut supprimer l'agenda' : " . $agenda->getSubject() . " du projet : " . $agenda->getProject()->getName(), null, "delete_from_project,warning", "calendar", "agenda," . $agenda->getId(), $user, $userRepository, $manager);

                $manager->flush();
                return $this->redirectToRoute('agenda_article', ['agenda' => $agenda->getId()]);
            } else {
                return $this->redirectToRoute('agenda');
            }
        }
        return $this->redirectToRoute('agenda');
    }
}
