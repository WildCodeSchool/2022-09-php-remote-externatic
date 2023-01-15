<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AnnonceRepository;
use App\Repository\AppointementRepository;
use App\Repository\MessageRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/consultant', name: "consultant_")]
class ExternaticConsultantController extends AbstractController
{
    #[Route('/board', name: 'board')]
    public function board(AppointementRepository $appointRepository, MessageRepository $messageRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $weekAppointments = $appointRepository->findAppoitmentList($user->getConsultant()->getId(), "thisWeek");
        $otherAppointments = $appointRepository->findAppoitmentList($user->getConsultant()->getId(), "thisMonth");

        $messages = $messageRepository->findBy(["sendTo" => $user], ["date" => 'DESC'], 10);

        return $this->render('externatic_consultant/board.html.twig', [
            'controller_name' => 'ExternaticConsultantController',
            'weekAppointment' => $weekAppointments,
            'otherAppointment' => $otherAppointments,
            'messages' => $messages
        ]);
    }

    #[Route('/annonces', name: 'annonces')]
    public function viewAnnonces(
        AnnonceRepository $annonceRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        /**
         * @var User $user
         */
        $user = $this->getUser();

/*        $queryBuilder = $annonceRepository->createQueryBuilder('a')
            ->andWhere('a.publicationStatus = 1')
            ->andWhere('a.endingAt <= :today')
            ->setParameter('today', new Datetime())
            ->join("a.author", "c")
            ->andWhere("c.id = " . $user->getConsultant()->getId())
            ->orderBy('a.date', 'ASC');
        $fetchedAnnonces = $queryBuilder->getQuery()->getResult();*/

        $fetchedAnnonces = $annonceRepository->findBy([
            'author' => $user->getConsultant(),
            'publicationStatus' => 1
        ], ["createdAt" => "DESC"]);

        $annonces = $paginator->paginate(
            $fetchedAnnonces,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('externatic_consultant/annonces.html.twig', [
            'annonces' => $annonces
        ]);
    }

    #[Route('/annonces/archives', name: 'annonces_archives')]
    public function viewAnnoncesArchives(
        AnnonceRepository $annonceRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $fetchedAnnonces = $annonceRepository->findBy([
            'author' => $user->getConsultant(),
            'publicationStatus' => 0
        ], ["createdAt" => "DESC"]);

        $annonces = $paginator->paginate(
            $fetchedAnnonces,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('externatic_consultant/annoncesArchives.html.twig', [
            'annonces' => $annonces
        ]);
    }
}
