<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\NotifUser;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index()
    {
        return $this->render('user/profile.html.twig', [
            'page'=>'profile',
        ]);
    }
    /**
     * @Route("/profile/{field}", name="profile_edit")
     */
    public function editProfile($field,Request $request,ObjectManager $objectManager,UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {

        $user = $this->getUser();
        $content = $request->request;
        $form = $this->createFormBuilder($user);

            if($field == "email"){


                    $form->add('email',TextType::class,[
                        'label'=> 'Veuillez modifier votre adresse email',
                        'attr'=>[
                            'placeholder'=>'Rentrez une nouvelle adresse email',
                            'autocomplete'=>'off'
                        ]
                    ]);
                    $form = $form->getForm();
                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {

                        $data = $form->getData();
                        if($this->isGranted('ROLE_SUPER_ADMIN')){
                            $user->setEmail($data->getEmail());
                            $objectManager->persist($user);
                            $this->addFlash("success",'Votre adresse email a bien été modifié.');
                            $objectManager->flush();
                        }else{
                            $superAdmin = $userRepository->findSuperAdmin();

                            $notification = new Notification();
                            $notification->setTitle("Demande de modification");
                            $notification->setContent("L'utilisateur : ".$user->getNom()." ".$user->getPrenom()." souhait modifier son adresse email : ".$user->getEmail()." en : ".$data->getEmail());
                            $notification->setData("email,".$data->getEmail());
                            $notification->setType("change_profile,neutral");
                            $notification->setIcon("user");


                            $user_notif = new NotifUser();
                            $user_notif->setNotification($notification);
                            $user_notif->setUser($superAdmin);
                            $user_notif->setOpened(0);
                            if($superAdmin != null) {
                                $objectManager->persist($notification);
                                $objectManager->persist($user_notif);
                            }

                        }
                        $objectManager->flush();
                        return $this->redirectToRoute("profile");
                    }





            }else if($field == "username"){


                    $form->add('username',TextType::class,[
                        'label'=> "Veuillez modifier votre nom d'utilisateur",
                        'attr'=>[
                            'placeholder'=>"Rentrez un nouveau nom d'utilisateur ",
                            'autocomplete'=>'off'
                        ]
                    ]);
                    $form = $form->getForm();
                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $data = $form->getData();
                        if($this->isGranted('ROLE_SUPER_ADMIN')){
                            $user->setUsername($data->getUsername());
                            $objectManager->persist($user);
                            $this->addFlash("success","Votre nom d'utilisateur a bien été modifié.");

                        }else{
                            $superAdmin = $userRepository->findSuperAdmin();

                            $notification = new Notification();
                            $notification->setTitle("Demande de modification");
                            $notification->setContent("L'utilisateur : ".$user->getNom()." ".$user->getPrenom()." souhait modifier son nom d'utilisateur : ".$user->getUsername()." en : ".$data->getUsername());
                            $notification->setData("username,".$data->getUsername());
                            $notification->setType("change_profile,neutral");
                            $notification->setIcon("user");

                            $user_notif = new NotifUser();
                            $user_notif->setNotification($notification);
                            $user_notif->setUser($superAdmin);
                            $user_notif->setOpened(0);
                            if($superAdmin != null) {
                                $objectManager->persist($notification);
                                $objectManager->persist($user_notif);
                            }
                        }
                        $objectManager->flush();
                        return $this->redirectToRoute("profile");
                    }


            }else if($field == "location"){


                    $form->add('location',TextType::class,[
                        'label'=> 'Veuillez modifier votre localisation',
                        'attr'=>[
                            'placeholder'=>'Rentrez une nouvelle localisation',
                            'autocomplete'=>'off'
                        ]
                    ]);
                $form = $form->getForm();
                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $data = $form->getData();
                        $user->setLocation($data->getLocation());
                        $objectManager->persist($user);
                        $this->addFlash("success",'Votre localisation a bien été modifié.');
                        $objectManager->flush();
                        return $this->redirectToRoute("profile");
                    }



            }else if($field == "password"){

                $form->add('password',PasswordType::class,[
                    'label'=> 'Veuillez rentrer un nouveau mot de passe',
                    'attr'=>[
                        'placeholder'=>'secret',
                        'autocomplete'=>'off',
                        'value'=>''
                    ]
                ]);
                $form = $form->getForm();
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $user->setPassword($encoder->encodePassword($this->getUser(), $data->getPassword()));
                    $objectManager->persist($user);
                    $this->addFlash("success",'Votre mot de passe a bien été modifié.');
                    $objectManager->flush();
                    return $this->redirectToRoute("profile");
                }


            }else if($field == "phone_number"){

                $form->add('phoneNumber',TextType::class,[
                    'label'=> 'Veuillez modifier votre numéro de téléphone',
                    'attr'=>[
                        'placeholder'=>'Rentrez un nouveau numéro de téléphone',
                        'autocomplete'=>'off'
                    ]
                ]);
                $form = $form->getForm();
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $user->setPhoneNumber($data->getPhoneNumber());
                    $objectManager->persist($user);
                    $this->addFlash("success",'Votre numéro de téléphone a bien été modifié.');
                    $objectManager->flush();
                    return $this->redirectToRoute("profile");
                }
            }else if($field == "first_name"){

                $form->add('firstName',TextType::class,[
                    'label'=> 'Veuillez modifier votre prénom',
                    'attr'=>[
                        'placeholder'=>'Rentrez un nouveau prénom',
                        'autocomplete'=>'off'
                    ]
                ]);
                $form = $form->getForm();
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $user->setFirstName($data->getFirstName());
                    $objectManager->persist($user);
                    $this->addFlash("success",'Votre prénom a bien été modifié.');
                    $objectManager->flush();
                    return $this->redirectToRoute("profile");
                }

            }else if($field == "last_name"){
                $form->add('lastName',TextType::class,[
                    'label'=> 'Veuillez modifier votre nom de famille: ',
                    'attr'=>[
                        'placeholder'=>'Rentrez un nouveau nom de famille',
                        'autocomplete'=>'off'
                    ]
                ]);
                $form = $form->getForm();
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $user->setLastName($data->getLastName());
                    $objectManager->persist($user);
                    $this->addFlash("success",'Votre nom de famille a bien été modifié.');
                    $objectManager->flush();
                    return $this->redirectToRoute("profile");
                }
            }


        return $this->render('user/profile.html.twig', [
            'page'=>'profil',
            'field'=>$field,
            'form'=>$form->createView()
        ]);
    }


}
