<?php
class KL_Rulemailer_Model_Api_Tag extends KL_Rulemailer_Model_Api_Rest
{
    /**
     *  Endpoint
     */
    const PATH = 'tags';

    /**
     * Find all
     *
     * This call is limited to 1000 tags. Fetch all by changing the page
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

        $response = $this->client->get(static::PATH, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

}
