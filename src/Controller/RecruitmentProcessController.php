<?php

namespace App\Controller;

use App\Entity\RecruitmentProcess;
use App\Repository\RecruitmentProcessRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_CANDIDAT') or is_granted('ROLE_CONSULTANT')")]
#[route('/recruitment_process', name: "recruitmentProcess_")]
class RecruitmentProcessController extends AbstractController
{
    #[Route('/{id<\d+>}/change-rate/{rate<[1-5]>}', name: 'changeRate', methods: ['GET'])]
    public function changeRate(
        RecruitmentProcess $recruitmentProcess,
        RecruitmentProcessRepository $recruitmentProcRepo,
        int $rate
    ): Response {
        $newRate = $recruitmentProcRepo->changeRate($recruitmentProcess, $rate);
        return $this->json([
            'rate' => $newRate
        ]);
    }

    #[Route('/{id<\d+>}/change-read', name: 'changeRead', methods: ['GET'])]
    public function changeRead(
        RecruitmentProcess $recruitmentProcess,
        RecruitmentProcessRepository $recruitmentProcRepo
    ): Response {
        return $this->json([
            'isRead' => $recruitmentProcRepo->changeStatus($recruitmentProcess)
        ]);
    }

    #[Route('/{id<\d+>}/change-archive', name: 'changeArchive', methods: ['GET'])]
    public function changeArchive(
        RecruitmentProcess $recruitmentProcess,
        RecruitmentProcessRepository $recruitmentProcRepo
    ): Response {
        return $this->json([
            'isArchived' => $recruitmentProcRepo->changeArchived($recruitmentProcess)
        ]);
    }
}
