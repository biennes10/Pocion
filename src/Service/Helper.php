<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\NotifUser;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;


class Helper
{
    public function projectSelectForm($projectUsers, $projectRepository)
    {
        $projects['Aucun (visible par tout le monde)'] = '';
        foreach ($projectUsers as $projectUser) {
            $project = $projectRepository->findOneById($projectUser->getProject()->getId());
            $projects[$project->getName()] = $project->getId();
        }
        return $projects;
    }

    public function projectIds($projectUsers, $projectRepository)
    {
        $projects = [];
        $projects[] = 1;
        foreach ($projectUsers as $projectUser) {
            $project = $projectRepository->findOneById($projectUser->getProject()->getId());
            $projects[] = $project->getId();
        }
        return $projects;
    }

    public function notif(User $user, $title, $content, $path, $type, $icon, $data,User $sentBy,UserRepository $userRepository, ObjectManager $objectManager)
    {
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setContent($content);
        $notification->setPath($path);
        $notification->setType($type);
        $notification->setIcon($icon);
        $notification->setData($data);

        $user_notif = new NotifUser();
        $user_notif->setNotification($notification);
        $user_notif->setUser($user);
        $user_notif->setOpened(0);
        $user_notif->setCreatedAt( new \DateTime());
        $user_notif->setSentBy($sentBy);

        $objectManager->persist($notification);
        $objectManager->persist($user_notif);

        $objectManager->flush();

    }
}