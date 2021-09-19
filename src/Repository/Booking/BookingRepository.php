<?php

namespace App\Repository\Booking;

use App\Entity\Booking\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use DateTime;

class BookingRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Activation::class);
  }

  public function getBookings(DateTime $start, DateTime $end)
  {
    $qb = $this-> _em->createQueryBuilder('booking');
    $query = $qb
      ->select('booking')
      ->from(Booking::class, 'booking')
      ->where('booking.beginAt BETWEEN :start and :end OR booking.endAt BETWEEN :start and :end')
      ->setParameter('start', $start->format('Y-m-d H:i:s'))
      ->setParameter('end', $end->format('Y-m-d H:i:s'));

    return $query->getQuery()->getResult();
  }
}
