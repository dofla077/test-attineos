<?php

namespace Attineos\Model\Reservation\Repositories;

use Attineos\Model\Reservation\Entites\Reservation;

/**
 * Class ReservationRepository
 *
 * This class make request to collect data from bdd
 *
 * @package Attineos\Model\Repositories
 */
class ReservationRepository extends \BaseRepository
{

    public function __construct(Reservation $reservation)
    {
        $this->model = $reservation;
    }

    public function getRoomName()
    {
        $room = $this->model->getRoom();

        return $room->getName() . ' ('.$room->getType().')';
    }

    public function getBookedDateFormat($format)
    {
        return $this->getBookedDate()->format($format);
    }

    public function getRoomPriceHotelCurrencyCode()
    {
        $builder = $this->model;
        return $builder->getRoomPrice().' '.$builder->getHotel()->getCurrency()->getCode();
    }

    public function getBookedStartAndEndTimeFormat($start_time, $end_time)
    {
        $builder = $this->model;
        return $builder->getBookedStartTime()->format($start_time).' - '.$builder->getBookedEndTime()->format($end_time);
    }

    public function getCustomerSimplePhoneNumber()
    {
        $builder = $this->model;

        return $builder->getCustomer()->getSimplePhoneNumber();
    }
}