<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\File;
use App\Entity\Handrail;
use App\Repository\AgendaRepository;
use App\Repository\FileRepository;
use App\Repository\HandrailRepository;
use App\Repository\NotifUserRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Service\Helper;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{
    /**
     * @Route("/", options={"expose"=true}, name="home")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function home(HandrailRepository $handrailRepository, AgendaRepository $agendaRepository, FileRepository $fileRepository, ProjectRepository $projectRepository, ProjectUserRepository $projectUserRepository, Helper $helper)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $projectIds = array();
            $projectUsers = $projectRepository->findAll();
            foreach ($projectUsers as $projectUser) {
                $projectIds[] = $projectUser->getId();
            }
        } else {

            $projectUsers = $projectUserRepository->getUserProjects($this->getUser()->getId());
            $projectIds = $helper->projectIds($projectUsers, $projectRepository);
        }

        $handrails = $handrailRepository->recent(10, $projectIds);
        $agenda = $agendaRepository->recent(10, $projectIds);
        $files = $fileRepository->recent(10, $projectIds);


        return $this->render('user/home.html.twig', [
            'page' => 'home',
            'handrails' => $handrails,
            'agenda' => $agenda,
            'files' => $files
        ]);
    }

    /**
     * @Route("/search/{search}", options={"expose"=true}, name="search")
     */
    public function search($search, HandrailRepository $handrailRepository, AgendaRepository $agendaRepository, FileRepository $fileRepository, ProjectRepository $projectRepository, ProjectUserRepository $projectUserRepository, Helper $helper)
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

        $result = [
            "handrail" => [],
            "agenda" => [],
            "files" => []
        ];

        if ($search !== "") {
            $handrails = $handrailRepository->search($search, $projectIds);
            if (!empty($handrails)) {
                foreach ($handrails as $handrail) {
                    $result["handrail"][] = [
                        "id" => $handrail->getId(),
                        "subject" => $handrail->getSubject()
                    ];
                }
            }
            $agendas = $agendaRepository->search($search, $projectIds);
            if (!empty($agendas)) {
                foreach ($agendas as $agenda) {
                    $result["agenda"][] = [
                        "id" => $agenda->getId(),
                        "subject" => $agenda->getSubject()
                    ];
                }
            }
            $files = $fileRepository->search($search, $projectIds);
            if (!empty($files)) {
                foreach ($files as $file) {
                    $result["files"][] = [
                        "id" => $file->getId(),
                        "subject" => $file->getSubject()
                    ];
                }
            }
        }


        return $this->json($result);
    }

    /**
     * @Route("/updateNotifs", options={"expose"=true}, name="update_notifs")
     */
    public function updateNotifs(ObjectManager $objectManager)
    {
        foreach ($this->getUser()->getNotifUsers() as $notifUser){
            $notifUser->setOpened(1);
            $objectManager->persist($notifUser);

        }
        $objectManager->flush();

        return $this->json([]);
    }

    /**
     * @Route("/delete_confirm_notif/{delete}", options={"expose"=true}, name="delete_confirm")
     */
    public function deleteConfirm($delete,ObjectManager $objectManager,HandrailRepository $handrailRepository, FileRepository $fileRepository, AgendaRepository $agendaRepository, NotifUserRepository $notifUserRepository, Helper $helper,UserRepository $userRepository)
    {

        $notif_user = $notifUserRepository->find($delete);
        $notif = $notif_user->getNotification();

        $data = $notif->getData();
        $data_array = explode(',' ,$data);
        $entity = $data_array[0];
        $id = $data_array[1];
        if($entity == "handrail"){
            $entity = $handrailRepository->find($id);
        }else if($entity == "agenda"){
            $entity = $agendaRepository->find($id);
        }else if($entity == "file"){
            $entity = $fileRepository->find($id);
        }
        $helper->notif($notif_user->getSentBy(),"Demande de suppression","Votre demande de supression a été accepté",null,"trash,success","check-circle",null,$notif_user->getUser(),$userRepository,$objectManager);
        $objectManager->remove($notif_user);
        $objectManager->remove($notif);
        $objectManager->remove($entity);
        $objectManager->flush();
        return $this->json([]);
    }
    /**
     * @Route("/delete_decline_notif/{delete}", options={"expose"=true}, name="delete_decline")
     */
    public function deleteDecline($delete,ObjectManager $objectManager,HandrailRepository $handrailRepository, FileRepository $fileRepository, AgendaRepository $agendaRepository, NotifUserRepository $notifUserRepository, Helper $helper, UserRepository $userRepository)
    {
        $notif_user = $notifUserRepository->find($delete);
        $notif = $notif_user->getNotification();

        $data = $notif->getData();
        $data_array = explode(',' ,$data);
        $entity = $data_array[0];
        $id = $data_array[1];
        if($entity == "handrail"){
            $entity = $handrailRepository->find($id);
        }else if($entity == "agenda"){
            $entity = $agendaRepository->find($id);
        }else if($entity == "file"){
            $entity = $fileRepository->find($id);
        }
        $entity->setStatus(0);

        $helper->notif($notif_user->getSentBy(),"Demande de suppression","Votre demande de supression a été refusé",null,"trash,warning","frown",null,$notif_user->getUser(),$userRepository,$objectManager);
        $objectManager->remove($notif_user);
        $objectManager->remove($notif);
        $objectManager->persist($entity);
        $objectManager->flush();
        return $this->json([]);
    }

}
