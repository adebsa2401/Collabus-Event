<?php

namespace App\EventSubscriber;

use App\Repository\CompanyRepository;
use App\Repository\JoinCompanyRequestRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $joinCompanyRequestRepository;

    private $companyRepository;

    public function __construct(JoinCompanyRequestRepository $joinCompanyRequestRepository, CompanyRepository $companyRepository)
    {
        $this->joinCompanyRequestRepository = $joinCompanyRequestRepository;
        $this->companyRepository = $companyRepository;
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
        $filters = $calendar->getFilters();

        $company = $this->companyRepository->find($filters['company_id']);

        foreach ($this->joinCompanyRequestRepository->findRelations($company, $start, $end) as $request) {
            $calendar->addEvent(new Event(
                'Réunion entre ' . $request->getRequestedBy()->getName() . ' et ' . $request->getRequestedTo()->getName(),
                $request->getStartedAt(),
                $request->getEndedAt()
            ));
        }
    }
}