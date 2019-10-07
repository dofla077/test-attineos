<?php

use Zendesk\API\HttpClient as ZendeskAPI;

/**
 *
 * Class Client
 */
class Client
{

    protected $zendeskAPI;
    protected $serviceManager;
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        /**
         * Init ZendeskAPI in Client class because only Client class use it for connect on API
         */
        $this->zendeskAPI = new ZendeskAPI($serviceManager['subdomain']);

        // Auth ZendeskAPI
        $this->zendeskAPI->setAuth('basic', ['username' => $serviceManager['username'], 'token' => $serviceManager['token']]);

    }

    /**
     * Can be more dynamical
     *
     * @param $email
     * @param $name
     * @param $phone
     * @param $role
     * @param array $user_fields
     * @return mixed
     */
    public function createOrUpdateUsers($email, $name, $phone, $role, $user_fields = [])
    {
        $data = [
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'role' => $role
        ];

        if (!empty($user_fields)) {
            $data['user_fields'] = $user_fields;
        }

        $user = $this->zendeskAPI->users()->createOrUpdate( $data );

        return $user;
    }

    /**
     *
     * @param $requester_id
     * @param $message
     * @param $customFields
     * @return mixed
     */
    public function createTickets($requester_id, $message, $customFields)
    {
        $tickets = $this->zendeskAPI->tickets()->create(
            [
                'requester_id' => $requester_id,
                'subject'      => strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message,
                'comment' =>
                    [
                        'body'  => $message
                    ],
                'priority'      => 'normal',
                'type'          => 'question',
                'status'        => 'new',
                'custom_fields' => $customFields
            ]
        );

        return $tickets;
    }

}