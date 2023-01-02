<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Techno;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

class SearchBarController extends AbstractController
{
    public function searchBar(AnnonceRepository $annonceRepository): Response
    {
        //fetching contact types from Bdd
        $contractTypeQuery = $annonceRepository->createQueryBuilder("a")
            ->select("distinct (a.contractType)")
            ->getQuery()
            ->getResult();

        $contractTypeFromDb = [];
        foreach ($contractTypeQuery as $contractType) {
            $contractTypeFromDb[ucfirst($contractType[1])] = $contractType[1];
        }
        ksort($contractTypeFromDb);


        //Creating search engine
        $form = $this->createFormBuilder()
            ->add('searchQuery', TextType::class, [
                'label' => "Recherche",
                'attr' => [
                    'placeholder' => 'votre recherche'],
                'required' => false,
                'row_attr' => ['class' => 'form-floating'],
            ])
            ->add('salaryMin', MoneyType::class, [
                'label' => "Salaire Min",
                'row_attr' => ['class' => 'text-editor'],
                'grouping' => true,
                "required" => false,
            ])

            ->add('contractType', ChoiceType::class, [
                'label' => "Type de contrat",
                'choices' => $contractTypeFromDb,
                "required" => false,
                'expanded' => true,
                'multiple' => true,
            ])

            ->add('remote', ChoiceType::class, [
                'label' => "Remote",
                'choices'  => [
                    'Total/partiel' => true,
                    'Présentiel' => false,
                ],
                "required" => false,
            ])

            ->add('workTime', ChoiceType::class, [
                'label' => "Temps de travail",
                'choices'  => [
                    'Plein temps' => true,
                    'Temps partiel' => false,
                ],
                "required" => false,
            ])

            ->add('period', ChoiceType::class, [
                    'label' => "Ancienneté des annonces",
                    'choices'  => [
                        '1 jour' => 1,
                        '5 jours' => 5,
                        '2 semaines' => 15,
                    ],
                    "required" => false,
                ])

            ->add('company', EntityType::class, [
                'label' => 'Entreprise',
                'class' => Company::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'attr' => [
                    'placeholder' => 'Entreprise'],
                'choice_label' => 'name',
                "required" => false
            ])
            ->add('techno', EntityType::class, [
                'class' => Techno::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
            ])
            ->setMethod('GET')

            ->setAction($this->generateUrl('search_results'))

            ->getForm();

        return $this->renderForm('_include/_searchBar.html.twig', [
            'form' => $form
        ]);
    }
}