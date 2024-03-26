<?php

namespace App\Repository;

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

}
