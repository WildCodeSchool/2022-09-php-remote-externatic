<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CandidatType;
use App\Form\UserUpdateType;
use App\Repository\CandidatRepository;
use App\Repository\CertificationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/profile')]
class CandidatController extends AbstractController
{
    #[Route('', name: 'app_candidat_profile', methods: ['GET'])]
    public function profile(
        ExperienceRepository $experienceRepository,
        CertificationRepository $certificationRepo
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $curriculum = $user->getCandidat()->getCurriculum();

        $experiences = $experienceRepository->findBy(
            ['curriculum' => $curriculum],
            ['beginning' => 'ASC'],
            10
        );

        $certifications = $certificationRepo->findBy(
            ['curriculum' => $curriculum],
            ['year' => 'ASC'],
            10
        );

        $hardSkills = $curriculum->getSkills()->getHardSkill();
        $softSkills = $curriculum->getSkills()->getSoftSkill();
        $languages = $curriculum->getSkills()->getLanguages();
        $hobbies = $curriculum->getHobbie();

        return $this->render('candidat/profile.html.twig', [
            'user' => $user,
            'experiences' => $experiences,
            'certifications' => $certifications,
            'hardSkills' => $hardSkills,
            'softSkills' => $softSkills,
            'languages' => $languages,
            'hobbies' => $hobbies
        ]);
    }

    #[Route('/account', name: 'app_candidat_account', methods: ['GET', 'POST'])]
    public function account(
        Request $request,
        UserInterface $user,
        UserRepository $userRepository,
        CandidatRepository $candidatRepository
    ): Response {

        $candidat = $candidatRepository->findOneBy(
            ['user' => $user]
        );
        $user = $candidat->getUser();

        //Account Data Form
        $userForm = $this->createForm(UserUpdateType::class, $user);
        $userForm->handleRequest($request);
        //Validation Form User Account Data
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_candidat_profile', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('user/update.html.twig', [
            'userForm' => $userForm,
        ]);
    }

    #[Route('/candidat', name: 'app_candidat_data', methods: ['GET', 'POST'])]
    public function candidat(
        Request $request,
        UserInterface $user,
        UserRepository $userRepository,
        CandidatRepository $candidatRepository
    ): Response {
        //Get Candidat Entity form User
        $candidat = $candidatRepository->findOneBy(
            ['user' => $user]
        );
        //Candidat Data Form
        $candidatForm = $this->createForm(CandidatType::class, $candidat);
        $candidatForm->handleRequest($request);
        //Validation Form Candidat Data
        if ($candidatForm->isSubmitted() && $candidatForm->isValid()) {
            $candidatRepository->save($candidat, true);

            return $this->redirectToRoute('app_candidat_profile', ['candidat' => $candidat,], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('candidat/update.html.twig', [
            'candidatForm' => $candidatForm,
        ]);
    }

    #[Route('/complete', name: 'app_candidat_complete', methods: ['GET', 'POST'])]
    public function complete(): Response
    {
        return $this->renderForm('candidat/complete.html.twig');
    }
}
