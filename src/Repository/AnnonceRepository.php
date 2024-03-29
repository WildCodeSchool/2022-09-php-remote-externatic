<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\ExternaticConsultant;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    private const FULL_TIME = 35;
    public const NUMBER_OF_ITEMS = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function save(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function annonceFinder(mixed $searchInformations): Query
    {
        $searchInformations['searchQuery'] ??= '';
        $queryBuilder = $this->createQueryBuilder('a')
            ->distinct()
            ->andWhere('a.title LIKE :searchQuery')
            ->setParameter('searchQuery', '%' . $searchInformations['searchQuery'] . '%')
            ->andWhere('a.publicationStatus = 1');

        //Minimum Salary and remote
        $this->getSalaryAndRemoteQuery($queryBuilder, $searchInformations);

        //Contract types
        if (!empty($searchInformations['contractType'])) {
            $queryBuilder->addCriteria(self::getContractQuery($searchInformations['contractType']));
        }

        //workTime
        if (isset($searchInformations['workTime']) && $searchInformations['workTime'] != "") {
            $worktimeOperator = $searchInformations['workTime'] ? ">=" : "<";
            $queryBuilder->andWhere("a.workTime $worktimeOperator " . self::FULL_TIME);
        }

        //date
        if (!empty($searchInformations['period'])) {
            $searchPeriod = new DateTime();
            $searchPeriod->sub(new DateInterval("P" . $searchInformations['period'] . "D"));
            $queryBuilder->andWhere("a.createdAt > :searchPeriod")
                ->setParameter("searchPeriod", $searchPeriod);
        }

        if (!empty($searchInformations['company'])) {
            $queryBuilder->join("a.company", "c")
                ->andWhere("c.id = :company_id")
                ->setParameter("company_id", $searchInformations['company']);
        }

        if (!empty($searchInformations['techno'])) {
            $queryBuilder->join("a.techno", "t");
            $queryBuilder->addCriteria(self::getTechnoQuery($searchInformations['techno']));
        }

        $queryBuilder->orderBy('a.createdAt', 'ASC');
        return $queryBuilder->getQuery();
    }

    public static function getContractQuery(array $contractTypes): Criteria
    {
        $criteria = Criteria::create();
        foreach ($contractTypes as $key => $contractType) {
            if ($key == 0) {
                $criteria->andWhere(Criteria::expr()->eq('contractType', $contractType));
            } else {
                $criteria->orWhere(Criteria::expr()->eq('contractType', $contractType));
            }
        }
        return $criteria;
    }

    public static function getTechnoQuery(array $technos): Criteria
    {
        $criteria = Criteria::create();
        foreach ($technos as $key => $techno) {
            if ($key == 0) {
                $criteria->andWhere(Criteria::expr()->eq('t.id', $techno));
            } else {
                $criteria->orWhere(Criteria::expr()->eq('t.id', $techno));
            }
        }
        return $criteria;
    }

    private function getSalaryAndRemoteQuery(QueryBuilder $queryBuilder, mixed $searchInformations): void
    {
        if (!empty($searchInformations['salaryMin'])) {
            $queryBuilder->andWhere('a.salaryMax > :salaryMin or a.salaryMax IS null')
                ->setParameter('salaryMin', $searchInformations['salaryMin']);
        }
        if (isset($searchInformations['remote']) && $searchInformations['remote'] != "") {
            $queryBuilder->andWhere('a.remote=:remote')
                ->setParameter('remote', $searchInformations['remote']);
        }
    }

    public function countAnnonce(): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery();

        return $queryBuilder->getResult();
    }

    public function getConsultantAnnonces(ExternaticConsultant $externaticConsultant, int $publicationStatus): Query
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.author = :externaticConsultant')
            ->setParameter('externaticConsultant', $externaticConsultant)
            ->andWhere('a.publicationStatus = :publicationStatus')
            ->setParameter('publicationStatus', $publicationStatus)
            ->join('a.company', 'c')
            ->getQuery();
    }

    public function searchAnnonces(
        ExternaticConsultant $externaticConsultant,
        int $publicationStatus,
        ?string $search = ''
    ): Query {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.author = :externaticConsultant')
            ->setParameter('externaticConsultant', $externaticConsultant)
            ->andWhere("a.publicationStatus = :publicationStatus")
            ->setParameter('publicationStatus', $publicationStatus);
        if (!is_null($search)) {
            $qb->andWhere('a.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $qb->join('a.company', 'c');


        return $qb->getQuery();
    }
}
