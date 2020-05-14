<?php

namespace App\Controller;

use App\Entity\Handrail;
use App\Entity\Notification;
use App\Entity\NotifUser;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\HandrailRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\UserRepository;
use App\Service\Helper;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HandrailController extends AbstractController
{
    /**
     * @Route("/handrail", options={"expose"=true}, name="handrail")
     */
    public function handrail(HandrailRepository $handrailRepository, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, Helper $helper)
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

        $handrails = $handrailRepository->recent(30, $projectIds);

        return $this->render('user/HAF/handrail.html.twig', [
            'handrails' => $handrails,
            'page'=>'handrail'
        ]);
    }



    /**
     * @Route("/handrail/new", name="handrail_new")
     * @IsGranted("ROLE_USER")
     */
    public function handrailNew(Request $request, ObjectManager $manager, HandrailRepository $handrailRepository, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, Helper $helper, UserRepository $userRepository)
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

        $handrails = $handrailRepository->recent(30, $projectIds);

        $handrail = new Handrail();
        $form = $this->createFormBuilder($handrail)
            ->add('project', EntityType::class, [
                'label' => 'Projet',
                'class' => Project::class,
                'choice_label' => 'name',
                'choices' => $projects,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de l\'évènement',
                'choices' => [
                    'Information' => 0,
                    'Incident' => 1
                ]
            ])
            ->add('urgency', ChoiceType::class, [
                'label' => 'Criticité',
                'choices' => [
                    'Neutre' => 0,
                    'Modérée' => 1,
                    'Elevée' => 2,
                    'Extrême' => 3

                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Donnez un titre...',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => '...'
                ],
                'config' => [
                    'toolbar' => 'standard'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $handrail
                ->setUser($this->getUser())
                ->setProject($data->getProject())
                ->setCreatedAt(new \DateTime())
                ->setStatus(0)
                ->setType($data->getType())
                ->setUrgency($data->getUrgency())
                ->setSubject($data->getSubject())
            ;

            if ($data->getContent() !== null) {
                $handrail->setContent($data->getContent());
            }
            if ($data->getLink() !== null) {

            }

            $manager->persist($handrail);

            $manager->flush();
            foreach($handrail->getProject()->getProjectUsers() as $project_users){

                $user = $project_users->getUser();
                $thisUser = $this->getUser();
                $path = "handrail_article,".$handrail->getId();

                $helper->notif($user,"Ajout au projet", "L'utilisateur ".$thisUser->getFirstName()." ".$thisUser->getLastName()." vient d'ajouter une main courante au projet : ".$handrail->getProject()->getName(),$path,"add_to_project,neutral","chart-bar",null,$thisUser,$userRepository,$manager);


            }


            return $this->redirectToRoute('handrail');
        }

        return $this->render('user/HAF/handrail.html.twig', [
            'handrails' => $handrails,
            'form' => $form->createView(),
            'edit' => false
        ]);
    }



    /**
     * @Route("/handrail/{handrail}", options={"expose"=true}, name="handrail_article")
     */
    public function handrailArticle(Handrail $handrail, HandrailRepository $handrailRepository, ProjectUserRepository $projectUserRepository, ProjectRepository $projectRepository, Helper $helper)
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

        $handrails = $handrailRepository->recent(30, $projectIds);

        return $this->render('user/HAF/handrail.html.twig', [
            'handrails' => $handrails,
            'article' => $handrail
        ]);
    }



    /**
     * @Route("/handrail/{handrail}/edit", name="handrail_article_edit")
     * @IsGranted("ROLE_USER")
     */
    public function handrailArticleEdit(Handrail $handrail, Request $request, ObjectManager $manager,HandrailRepository $handrailRepository, ProjectRepository $projectRepository, ProjectUserRepository $projectUserRepository, Helper $helper)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $projectUsers = $projectRepository->findAll();
            foreach ($projectUsers as $projectUser) {
                $projectIds[] = $projectUser->getId();
            }
            $projects = $projectRepository->findAll();

        } else {

            if ($handrail->getUser() !== $this->getUser()) {
                return $this->redirectToRoute('handrail_article', ['handrail' => $handrail->getId()]);
            }
            $projectUsers = $projectUserRepository->getUserProjects($this->getUser()->getId());
            $projectIds = $helper->projectIds($projectUsers, $projectRepository);
            $projects = $projectRepository->getProjects($projectIds);

        }

        $handrails = $handrailRepository->recent(30, $projectIds);

        $form = $this->createFormBuilder($handrail)
            ->add('project', EntityType::class, [
                'label' => 'Projet',
                'class' => Project::class,
                'choice_label' => 'name',
                'choices' => $projects,
                'data' => $handrail->getProject()
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de l\'évènement',
                'choices' => [
                    'Information' => 0,
                    'Incident' => 1
                ],
                'data' => $handrail->getType()
            ])
            ->add('urgency', ChoiceType::class, [
                'label' => 'Criticité',
                'choices' => [
                    'Neutre' => 0,
                    'Modérée' => 1,
                    'Elevée' => 2,
                    'Extrême' => 3

                ],
                'data' => $handrail->getUrgency()
            ])
            ->add('subject', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => '...',
                    'autocomplete' => 'off'
                ],
                'data' => $handrail->getSubject()
            ])
            ->add('content', CKEditorType::class, [
                'label' => false,
                'required' => false,
                'config' => [
                    'toolbar' => 'standard'
                ],
                'data' => $handrail->getContent()
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $data = $form->getData();

            $handrail
                ->setProject($data->getProject())
                ->setStatus(0)
                ->setType($data->getType())
                ->setUrgency($data->getUrgency())
                ->setSubject($data->getSubject())
            ;

            if ($data->getContent() !== null) {
                $handrail->setContent($data->getContent());
            }
            if ($data->getLink() !== null) {

            }

            $manager->flush();

            return $this->redirectToRoute('handrail_article', ['handrail' => $handrail->getId()]);

        }

        return $this->render('user/HAF/handrail.html.twig', [
            'handrails' => $handrails,
            'article' => $handrail,
            'form' => $form->createView(),
            'edit' => true
        ]);
    }



    /**
     * @Route("/handrail/{handrail}/delete", name="handrail_article_delete", options={"expose"=true})
     * @IsGranted("ROLE_USER")
     */
    public function handrailArticleDelete(Handrail $handrail, ObjectManager $manager,UserRepository $userRepository, Helper $helper)
    {
        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $manager->remove($handrail);
            $manager->flush();
        }else {
            $user = $this->getUser();
            if ($this->isGranted('ROLE_ADMIN')) {
                $canDelete = true;

            } else {
                if ($handrail->getUser() == $this->getUser()) {
                    $canDelete = true;
                } else {
                    $canDelete = false;

                }
            }

            if ($canDelete) {

                $handrail->setStatus(1);
                $users = $userRepository->findAll();
                foreach ($users as $user2) {
                    if ($user2->getRoles() == array("ROLE_SUPER_ADMIN")) {
                        $superAdmin = $user2;
                        break;
                    }
                }
                //$superAdmin = $userRepository->findOneByRoles(["ROLE_SUPER_ADMIN"]);

                $helper->notif($superAdmin, "Demande de suppression", "L'utilisateur " . $user->getFirstName() . " " . $user->getLastName() . " veut supprimer la main courante : " . $handrail->getSubject() . " du projet : " . $handrail->getProject()->getName(), null, "delete_from_project,warning", "chart-bar", "handrail," . $handrail->getId(), $user, $userRepository, $manager);

                $manager->flush();
                return $this->redirectToRoute('handrail_article', ['handrail' => $handrail->getId()]);
            } else {
                return $this->redirectToRoute('handrail');
            }
        }
        return $this->redirectToRoute('handrail');
    }


}
