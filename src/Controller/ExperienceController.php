<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Form\FormationType;
use App\Repository\CandidatRepository;
use App\Repository\CurriculumRepository;
use App\Repository\ExperienceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/experience')]
class ExperienceController extends AbstractController
{
    #[Route('/', name: 'app_experience_index', methods: ['GET'])]
    public function index(
        CandidatRepository $candidatRepository,
        CurriculumRepository $curriculumRepository,
        ExperienceRepository $experienceRepository
    ): Response {
        $user = $this->getUser();
        $candidat = $candidatRepository->findOneBy(
            ['user' => $user]
        );

        $curriculum = $curriculumRepository->findOneBy(
            ['candidat' => $candidat]
        );

        $experiences = $experienceRepository->findBy(
            ['curriculum' => $curriculum]
        );

        return $this->render('experience/index.html.twig', [
            'user' => $user,
            'curriculum' => $curriculum,
            'candidat' => $candidat,
            'experiences' => $experiences,
        ]);
    }

    #[Route('/new/{type}', name: 'app_experience_new', methods: ['GET', 'POST'])]
    public function newExp(
        string $type,
        Request $request,
        ExperienceRepository $experienceRepository,
        CandidatRepository $candidatRepository,
        CurriculumRepository $curriculumRepository
    ): Response {

        $user = $this->getUser();
        $candidat = $candidatRepository->findOneBy(
            ['user' => $user]
        );
        $curriculum = $curriculumRepository->findOneBy(
            ['candidat' => $candidat]
        );

        if ('experience' === $type) {
            //Experience Entity & Form
            $experience = new Experience();
            $experienceForm = $this->createForm(ExperienceType::class, $experience);
            $experienceForm->handleRequest($request);
            //Validation Form Experience Data
            if ($experienceForm->isSubmitted() && $experienceForm->isValid()) {
                $experience->setIsFormation(false);
                $experience->setCurriculum($curriculum);
                $experienceRepository->save($experience, true);

                return $this->redirectToRoute(
                    'app_candidat_profile',
                    ['candidat' => $candidat,],
                    Response::HTTP_SEE_OTHER
                );
            }
            return $this->renderForm('experience/newExperience.html.twig', [
                'experienceForm' => $experienceForm,
            ]);
        } elseif ('formation' === $type) {
            //Experience Entity & Formation Form
            $formation = new Experience();
            $formationForm = $this->createForm(FormationType::class, $formation);
            $formationForm->handleRequest($request);
            //Validation Form Formation Data
            if ($formationForm->isSubmitted() && $formationForm->isValid()) {
                $formation->setIsFormation(true);
                $formation->setCurriculum($curriculum);
                $experienceRepository->save($formation, true);

                return $this->redirectToRoute(
                    'app_candidat_profile',
                    ['candidat' => $candidat,],
                    Response::HTTP_SEE_OTHER
                );
            }
            return $this->renderForm('experience/newFormation.html.twig', [
                'formationForm' => $formationForm,
            ]);
        } else {
            throw $this->createNotFoundException('The page doesn\'t exist');
        }
    }

    #[Route('/{id}', name: 'app_experience_show', methods: ['GET'])]
    public function show(Experience $experience): Response
    {
        if ($experience->getCurriculum()->getCandidat()->getUser() !== $this->getUser()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can consult !');
        }
        return $this->render('experience/show.html.twig', [
            'experience' => $experience,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_experience_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Experience $experience,
        ExperienceRepository $experienceRepository
    ): Response {
        if ($experience->getCurriculum()->getCandidat()->getUser() !== $this->getUser()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can edit !');
        }

        if ($experience->isIsFormation()) {
            $formationForm = $this->createForm(FormationType::class, $experience);
            $formationForm->handleRequest($request);

            if ($formationForm->isSubmitted() && $formationForm->isValid()) {
                $experienceRepository->save($experience, true);

                return $this->redirectToRoute('app_experience_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('experience/editFormation.html.twig', [
                'formationForm' => $formationForm,
                'experience' => $experience,
            ]);
        } else {
            $experienceForm = $this->createForm(ExperienceType::class, $experience);
            $experienceForm->handleRequest($request);

            if ($experienceForm->isSubmitted() && $experienceForm->isValid()) {
                $experienceRepository->save($experience, true);

                return $this->redirectToRoute('app_experience_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('experience/editExperience.html.twig', [
                'experienceForm' => $experienceForm,
                'experience' => $experience,
            ]);
        }
    }

    #[Route('/{id}', name: 'app_experience_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Experience $experience,
        ExperienceRepository $experienceRepository
    ): Response {
        if ($experience->getCurriculum()->getCandidat()->getUser() !== $this->getUser()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can delete !');
        }
        if ($this->isCsrfTokenValid('delete' . $experience->getId(), $request->request->get('_token'))) {
            $experienceRepository->remove($experience, true);
        }

        return $this->redirectToRoute('app_experience_index', [], Response::HTTP_SEE_OTHER);
    }
}
