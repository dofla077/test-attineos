<?php

namespace Mailjet\Service;

use Attineos\Model\Hotel\Repositories\HotelRepository;
use Attineos\Model\Reservation\Repositories\ReservationRepository;
use CustomFields;

/**
 * One Class want to have one logic to have no confusion in an others class
 * And the logic for this class is to creat Tickets
 *
 * We can see all methods have the same attributes, so we put these on the constructor
 * to have all information when we initialize the Class
 *
 * CustomFields is a Class to init and just add fields
 *
 * I create a model folder to separate logic for different things who models can do : so make an insert in bdd is not the same
 * when we want to collect data or make search etc...
 *
 * I create model parent (Base) to have all things who is common on all models
 *
 * Class ZendeskService
 * @package Mailjet\Service
 */
class ZendeskService extends AbstractService
{

    const PRODUCTION_SECRET_TOKEN = '7a960781b588403ca32116048238d01c';

    protected $gender;
    protected $firstName;
    protected $lastname;
    protected $phoneNumber;
    protected $email;
    protected $message;
    protected $reservationNumber;
    protected $hotel;
    protected $language;
    protected $domainConfig;
    protected $city;
    protected $hotelName;
    protected $name;
    protected $website;
    protected $media;

    /**
     * ZendeskService constructor.
     * @param $gender
     * @param $firstName
     * @param $lastname
     * @param $phoneNumber
     * @param $email
     * @param $message
     * @param $reservationNumber
     * @param Hotel $hotel
     * @param Language $language
     * @param DomainConfig $domainConfig
     * @param $city
     * @param $hotelName
     * @param $website
     * @param $media
     */
    public function __construct($gender = null, $firstName = null , $lastname = null, $phoneNumber = null, $email = null,
                                $message = null, $reservationNumber = null, Hotel $hotel = null, Language $language = null,
                                DomainConfig $domainConfig = null, $city = null, $hotelName = null, $website = null, $media = null)
    {
        $this->gender = $gender;
        $this->firstName = $firstName;
        $this->lastname = $lastname;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->message = $message;
        $this->reservationNumber = $reservationNumber;
        $this->hotel = $hotel;
        $this->language = $language;
        $this->domainConfig = $domainConfig;
        $this->city = $city;
        $this->hotelName = $hotelName;
        $this->website = $website;
        $this->media = $media;
        $this->name = $this->firstName.' '.strtoupper($this->lastname);
    }

    public function createCustomerTicket()
    {
        $reservation = (new ReservationRepository('Reservation'))->getOneByRef($this->reservationNumber);
        $customFields  = new CustomFields(['80924888' => 'customer', '80531327' => $this->reservationNumber]);

        if ($reservation !== null && $this->hotel === null) {
            $this->hotel = $reservation->getHotel();
        }

        if ($this->hotel !== null) {
            $hotelContact = new HotelRepository($this->hotel);
            $customFields->addFields([
                '80531267' => $hotelContact !== null ? $hotelContact->getEmail() : null,
                '80918668' => $this->hotel->getName(),
                '80918648' => $this->hotel->getAddress()
            ]);
        }

        if ($reservation !== null) {
            $customFields->addFields([
                '80531287' => $reservation->getRoomName(), '80531307' => $reservation->getBookedDateFormat('Y-m-d'),
                '80924568' => $reservation->getRoomPriceHotelCurrencyCode(),
                '80918728' => $reservation->getBookedStartAndEndTimeFormat('H:i', 'H:i')

            ]);
        }
        $customFields->addFields(['80918708' => $this->language]);

        $client = new \Client($this->getServiceManager()->get('Config')['zendesk']);
        $response = $client->createOrUpdateUsers($this->email, $this->name, $reservation->getCustomerSimplePhoneNumber(), 'end-user');
        $client->createTickets($response->user->id, $this->message, $customFields);

        return true;
    }

    public function createHotelTicket()
    {
        $customFields = new CustomFields([
            '80924888' => 'hotel', '80918668' => $this->hotelName, '80918648' => $this->city, '80918708' => $this->language->getName()
        ]);

        $client = new \Client($this->getServiceManager()->get('Config')['zendesk']);
        $response = $client->createOrUpdateUsers($this->email, $this->name, $this->phoneNumber, 'end-user', [ 'website' => $this->website ]);
        $client->createTickets($response->user->id, $this->message, $customFields);

        return true;
    }

    public function createPressTicket()
    {
        $customFields = new CustomFields(['80924888' => 'press', '80918648' => $this->city, '80918708' => $this->language->getName()]);

        $client = new \Client($this->getServiceManager()->get('Config')['zendesk']);
        $response = $client->createOrUpdateUsers($this->email, $this->name, $this->phoneNumber, 'end-user', [ 'press_media' => $this->media ]);
        $client->createTickets($response->user->id, $this->message, $customFields);

        try {
            $client->createTickets($response->user->id, $this->message, $customFields);
        } catch (\Exception $e) {
            $this->getLogger()->addError(var_export($response->user->id, true));
        }

        return true;
    }

    public function createPartnersTicket()
    {
        $customFields = new CustomFields(['80924888' => 'partner', '80918708' => $this->language->getName()]);

        $client = new \Client($this->getServiceManager()->get('Config')['zendesk']);
        $response = $client->createOrUpdateUsers($this->email, $this->name, $this->phoneNumber, 'end-user');
        $client->createTickets($response->user->id, $this->message, $customFields);

        return true;
    }
}