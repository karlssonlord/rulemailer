<?php
class KL_Rulemailer_Model_Api_Suppression extends KL_Rulemailer_Model_Api_Rest
{
    const SUPPRESSIONS_PATH = 'suppressions';

    /**
     * Add subscribers
     *
     * Suppress one or multiple subscribers by email and/or ID.
     *
     * @param array $subscribers Subscribers
     *
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function addSubscribers(array $subscribers)
    {
        $data = array();
        $data['subscribers'] = $subscribers;

        $response = $this->client->post(static::SUPPRESSIONS_PATH, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * Find all
     *
     * This call is limited to 1000 suppressions. Fetch all by changing the page
     * parameter.
     *
     * @param int $page What page to fetch
     *
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function findAll($page = 0)
    {
        $data = array();
        $data['page'] = $page;

        $response = $this->client->get(static::SUPPRESSIONS_PATH, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

}
