<?php

namespace App\EventListener;

use App\Repository\BookingRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
  private $bookingRepository;
  private $router;

  public function __construct(
    BookingRepository $bookingRepository,
    UrlGeneratorInterface $router
  ) {
    $this->bookingRepository = $bookingRepository;
    $this->router = $router;
  }

  public static function getSubscribedEvents()
  {
    return [
      CalendarEvents::SET_DATA => 'onCalendarSetData',
    ];
  }

  public function onCalendarSetData(CalendarEvent $calendar)
  {
    $start = $calendar->getStart();
    $end = $calendar->getEnd();

    $bookings = $this->bookingRepository
      ->createQueryBuilder('booking')
      ->where('booking.beginAt BETWEEN :start and :end OR booking.endAt BETWEEN :start and :end')
      ->setParameter('start', $start->format('Y-m-d H:i:s'))
      ->setParameter('end', $end->format('Y-m-d H:i:s'))
      ->getQuery()
      ->getResult();

    foreach ($bookings as $booking) {
      // this create the events with your data (here booking data) to fill calendar
      $bookingEvent = new Event(
        $booking->getTitle(),
        $booking->getBeginAt(),
        $booking->getEndAt() // If the end date is null or not defined, a all day event is created.
      );

      $bookingEvent->setOptions([
        'backgroundColor' => 'red',
        'borderColor' => 'red',
      ]);
      $bookingEvent->addOption(
        'url',
        $this->router->generate('booking_show', [
          'id' => $booking->getId(),
        ])
      );

      // finally, add the event to the CalendarEvent to fill the calendar
      $calendar->addEvent($bookingEvent);
    }
  }
}
