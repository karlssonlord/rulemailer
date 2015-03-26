<?php

class KL_Rulemailer_Model_Api_Customizations extends KL_Rulemailer_Model_Api_Rest
{
    /**
     *  Endpoint
     */
    const CUSTOMIZATIONS_ENDPOINT = 'customizations';

    /**
     * Find all customizations
     *
     * This call is limited to 100 customizations. Fetch all by changing the
     * page parameter. 
     *
     * @param int $page What page to fetch
     *
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function findAll($page = 0)
    {
        $data = array();
        $data['page'] = $page;

        $response = $this->client->get(static::CUSTOMIZATIONS_ENDPOINT, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

}
