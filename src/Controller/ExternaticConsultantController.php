<?php

namespace App\Controller;

use App\Entity\RecruitmentProcess;
use App\Entity\User;
use App\Repository\AnnonceRepository;
use App\Repository\AppointementRepository;
use App\Repository\CandidatRepository;
use App\Repository\MessageRepository;
use App\Repository\RecruitmentProcessRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_CONSULTANT')]
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

        $fetchedAnnonces = $annonceRepository->getConsultantAnnonces($user->getConsultant(), false);

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
        $fetchedAnnonces = $annonceRepository->getConsultantAnnonces($user->getConsultant(), true);

        $annonces = $paginator->paginate(
            $fetchedAnnonces,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('externatic_consultant/annoncesArchives.html.twig', [
            'annonces' => $annonces
        ]);
    }

    #[Route('/synthese', name:'synthesis', methods: ['GET'])]
    public function processSynthesis(
        RecruitmentProcessRepository $recruitmentProcess,
    ): Response {
        $synthesis = $recruitmentProcess->findAll();
        return $this->render('externatic_consultant/process-synthesis.html.twig', [
           'synthesis' => $synthesis,
        ]);
    }
}
