<?php

namespace RycCheen\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class WechatUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param  array $response
     */
    public function __construct(array $response = array())
    {
        $this->data = $response;
    }

    /**
     * Returns the ID for the user as a string if present.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getField('openid');
    }

    /**
     * Returns the nickname for the user as a string if present.
     *
     * @return string|null
     */
    public function getNickName()
    {
        return $this->getField['nickname'];
    }

    /**
     * Returns the gender for the user as a string if present.
     * 1 - male, 2 - female
     * @return string|null
     */
    public function getSex()
    {
        return $this->getField('sex');
    }

    /**
     * Returns the current province of the user as an array.
     *
     * @return string|null
     */
    public function getProvince()
    {
        return $this->getField('province');
    }

    /**
     * Returns the current city of the user as an array.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->getField('city');
    }

    /**
     * Returns the current country of the user as an array.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->getField('country');
    }

    /**
     * Returns the avatar picture of the user as a string if present.
     *
     * @return string|null
     */
    public function getHeadImgUrl()
    {
        return $this->getField('headimgurl');
    }

    /**
     * Returns the Union ID for the user as a string if present.
     *
     * @return string|null
     */
    public function getUnionId()
    {
        return $this->getField('unionid');
    }

    /**
     * Returns all the data obtained about the user.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns a field from the Graph node data.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    private function getField($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
