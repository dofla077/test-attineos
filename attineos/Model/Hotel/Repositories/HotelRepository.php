<?php
namespace Attineos\Model\Hotel\Repositories;

use Attineos\Model\Hotel\Entites\Hotel;

class HotelRepository extends \BaseRepository
{

    public function __construct(Hotel $hotel)
    {
        $this->model = $hotel;
    }

    /**
     * @param Hotel $hotel
     * @return Hotel
     */
    public function getMainHotelContact(Hotel $hotel)
    {
        $hotelContact = $this->getServiceManager()->get('service.hotel_contacts')->getMainHotelContact($hotel);

        return $hotelContact;
    }
}