<?php

namespace App\Service;

use App\Entity\Annonce;
use App\Entity\Notif;
use App\Repository\NotifRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use DateTime;

class NewNotif extends AbstractController
{
    private UserRepository $userRepository;
    private NotifRepository $notifRepository;
    private MailerInterface $mailer;
    public function __construct(
        UserRepository $userRepository,
        NotifRepository $notifRepository,
        MailerInterface $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->notifRepository = $notifRepository;
        $this->mailer = $mailer;
    }
    public function newNotifAnnonce(Annonce $annonce): void
    {
        foreach ($this->userRepository->findByRole('ROLE_CANDIDAT') as $user) {
            $notification = new Notif();
            $notification->setContent($annonce->getTitle());
            $notification->setType('newAnnonce');
            $notification->setCreatedAt(new DateTime('now'));
            $notification->setUser($user);
            $notification->setParameter($annonce->getId());
            $notification->setWasRead(false);
            $this->notifRepository->save($notification, true);

            $email = (new Email())
                ->to($user->getEmail())
                ->from($this->getParameter('mailer_from'))
                ->subject('Une nouvelle Annonce correspondant à vos critère a été créer')
                ->html($this->renderView('annonce/newEmail.html.twig', ['annonce' => $annonce]));

            $this->mailer->send($email);
            // sleep pour ne pas dépasser la limite de mailtrap, a enlever en prod
            sleep(3);
        }
    }
}