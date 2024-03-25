<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 *
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }


    public function getCityNames(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.city_name')
            ->getQuery()
            ->getArrayResult();
    }

    public function getCityNamesAndIds(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.city_name', 'l.id')
            ->getQuery()
            ->getArrayResult();
    }

    public function getCitiesAndCoordinates(): array
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l.city_name', 'l.latitude', 'l.longitude')
            ->getQuery();

        $results=  $qb->getResult();

        $citiesAndCoordinates = [];
        foreach($results as $result){
            $cityName = $result['city_name'];
            $latitude = $result['latitude'];
            $longitude = $result['longitude'];

            $citiesAndCoordinates[$cityName] = ['latitude' => $latitude, 'longitude' => $longitude];
        }

        return $citiesAndCoordinates;





    }

    //    /**
    //     * @return Location[] Returns an array of Location objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Location
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
