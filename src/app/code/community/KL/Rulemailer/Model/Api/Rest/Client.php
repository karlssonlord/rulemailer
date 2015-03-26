<?php

/**
 * Interface KL_Rulemailer_Model_Api_Rest_Client
 */
interface KL_Rulemailer_Model_Api_Rest_Client
{
    /**
     * @param       $path
     * @param array $data
     * @return mixed
     */
    public function get($path, array $data = []);

    /**
     * @param       $path
     * @param array $data
     * @return mixed
     */
    public function post($path, array $data = []);

    /**
     * @param       $path
     * @param array $data
     * @return mixed
     */
    public function put($path, array $data = []);

    /**
     * @param       $path
     * @param array $data
     * @return mixed
     */
    public function delete($path, array $data = []);

}
