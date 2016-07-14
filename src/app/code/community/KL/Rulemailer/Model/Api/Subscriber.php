<?php

/**
 * Class KL_Rulemailer_Model_Api_Subscriber
 * @package KL_Rulemailer
 * @author Andreas Karlsson <andreas@karlssonlord.com>
 * @author David Wickström <david@karlssonlord.com>
 */
class KL_Rulemailer_Model_Api_Subscriber extends KL_Rulemailer_Model_Api_Rest
{
    /**
     *  Endpoint
     */
    const SUBSCRIBERS_PATH = 'subscribers';

    /**
     * Set api custom api key for client
     *
     * @param string $apiKey Api key for Rule
     */
    public function setApiKey($apiKey)
    {
        $this->client->setApiKey($apiKey);
    }

    /**
     * Create subscriber
     *
     * @param string $email             Email address
     * @param array  $tags              Tag subscriber with these tags, use
     *                                  numerical ID or a string name
     * @param array  $fields            Customization fields, optional
     * @param bool   $updateOnDuplicate Update the subscriber if it already
     *                                  exists, defaults to false
     * @param bool   $autoCreateTags    Create tags if they don't exist,
     *                                  defaults to false
     * @param bool   $autoCreateFields  Create group and or field if they don't
     *                                  exist, defaults to false
     *
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function create($email, array $tags = array(), array $fields = array(), $updateOnDuplicate = true, $autoCreateTags = true, $autoCreateFields = true)
    {
        // Validate if identifier is used
        $this->validateEmailAddress($email, 'email');

        $subscriber = [
            'email' => $email,
            'fields' => $fields
        ];

        $data = array();
        $data['tags'] = $tags;
        $data['update_on_duplicate'] = $updateOnDuplicate;
        $data['auto_create_tags']    = $autoCreateTags;
        $data['auto_create_fields']  = $autoCreateFields;
        $data['subscribers'] = [$subscriber];

        $response = $this->client->post(static::SUBSCRIBERS_PATH, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * Find all subscribers
     *
     * This call is limited to 1000 subscribers. Fetch all by changing the page
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

        $response = $this->client->get(static::SUBSCRIBERS_PATH, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * Find one subscriber
     *
     * Get a subscriber via email or ID.
     *
     * @param int|string $identifier   The subscriber identifier
     * @param string     $identifiedBy Specify your identifier, id or email;
     *                                 defaults to email
     *
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function findOne($identifier, $identifiedBy = 'email')
    {
        // Validate if identifier is used
        $this->validateEmailAddress($identifier, $identifiedBy);

        // Compose request path
        $path = sprintf('%s/%s', static::SUBSCRIBERS_PATH, $identifier);

        $data = array();
        $data['identified_by'] = $identifiedBy;

        $response = $this->client->get($path, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * Add tag
     *
     * Add tag/tags to a subscriber.
     *
     * @param array      $tags         Tag subscriber with these tags, use
     *                                 numerical ID or a string name
     * @param int|string $identifier   The subscriber identifier
     * @param string     $identifiedBy Specify your identifier, id or email;
     *                                 defaults to email
     *
     * @throws InvalidArgumentException
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function addTag($tags, $identifier, $identifiedBy = 'email')
    {
        // Filter invalid email addresses
        $this->validateEmailAddress($identifier, $identifiedBy);

        // Filter invalid tags
        $tags = $this->filterTags($tags);

        // Build request path
        $path = sprintf('%s/%s/tags', static::SUBSCRIBERS_PATH, $identifier);

        // Compose request parameters array
        $data = array();
        $data['identifier'] = $identifier;
        $data['identified_by'] = $identifiedBy;
        $data['tags']          = $tags;

        // Make request
        $response = $this->client->post($path, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * List tags
     *
     * @param int|string $identifier   The subscriber identifier
     * @param string     $identifiedBy Specify your identifier, id or email;
     *                                 defaults to email
     *
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function listTags($identifier, $identifiedBy = 'email')
    {
        $this->validateEmailAddress($identifier, $identifiedBy);

        $path = sprintf('%s/%s/tags', static::SUBSCRIBERS_PATH, $identifier);

        $data = array();
        $data['identified_by'] = $identifiedBy;

        $response = $this->client->get($path, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     * Remove tag
     *
     * Delete a tag from a subscriber.
     *
     * @param int|string $tagIdentifier The tag identifier, can be the tag name
     *                                  or the tag id
     * @param int|string $identifier    The subscriber identifier
     * @param string     $identifiedBy  Specify your identifier, id or email;
     *                                  defaults to email
     *
     * @return KL_Rulemailer_Model_Api_Rest_Response
     */
    public function removeTag($tagIdentifier, $identifier, $identifiedBy = 'email')
    {
        $path = sprintf('%s/%s/tags/%s', static::SUBSCRIBERS_PATH, $identifier, $tagIdentifier);

        $data = array();
        $data['identified_by'] = $identifiedBy;

        $response = $this->client->delete($path, $data);
        return new KL_Rulemailer_Model_Api_Rest_Response($response);
    }

    /**
     *  Check for incorrect types and make sure we return an array
     *
     * @param $tags
     * @return array
     * @throws InvalidArgumentException
     */
    private function filterTags($tags)
    {
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                if (!is_scalar($tag)) {
                    throw new InvalidArgumentException('Tags must have scalar type.');
                }
            }
            return $tags;
        } else {
            if (!is_scalar($tags)) {
                throw new InvalidArgumentException('Tags must have scalar type.');
            }
        }
        return array($tags);
    }

    /**
     *  Validate email identifiers
     *
     * @param $identifier
     * @param $identifiedBy
     * @throws InvalidArgumentException
     */
    private function validateEmailAddress($identifier, $identifiedBy)
    {
        if ($identifiedBy == 'email') {
            if (!filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("The supplied customer email was not valid: {$identifier}");
            }
        }
    }

}
