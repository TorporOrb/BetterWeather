<?php

namespace App\Repository;

use DateTime;
use App\Entity\Forecast;
use App\Entity\Location;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Forecast>
 *
 * @method Forecast|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forecast|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forecast[]    findAll()
 * @method Forecast[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForecastRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forecast::class);
    }

    /**
     * @return Forecast[]
     */
    public function findForecastForLocation(Location $location): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb
            ->where('f.location =  :location')
            ->setParameter('location', $location);

        $query = $qb->getQuery();
        $forecasts = $query->getResult();

        return $forecasts;
    }

    /**
     * @return Forecast[]
     */
    public function findNoonForecastsForLocation(Location $location): array
    {
    $qb = $this->createQueryBuilder('f');
    $qb
        ->where('f.location = :location')
        ->andWhere($qb->expr()->orX(
            $qb->expr()->like('f.date', ':time1'),
            $qb->expr()->like('f.date', ':time2')
            )
        )
        ->setParameter('location', $location)
        ->setParameter('time1', '% 12:00:%')
        ->setParameter('time2', '% 18:00:%');

    $query = $qb->getQuery();
    $forecasts = $query->getResult();

    return $forecasts;

    }

    /**
     * @return Forecast[]
     */
    public function findFirstSixForecastsForLocation(Location $location): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb
            ->where('f.location = :location')
            ->setParameter('location', $location)
            ->setMaxResults(6); 

        $query = $qb->getQuery();
        $forecasts = $query->getResult();

        return $forecasts;
    }

    /**
     * @return Forecast[]
     */
    public function findForecastsForLocationAndDate(Location $location, DateTime $date): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb
            ->where('f.location = :location')
            ->andWhere($qb->expr()->like('f.date', ':date'))
            ->setParameter('location', $location)
            ->setParameter('date', $date->format('Y-m-d') . '%');
            $query = $qb->getQuery();
        $forecasts = $query->getResult();

        return $forecasts;

    }
}
