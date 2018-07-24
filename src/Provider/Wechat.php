<?php

namespace RycCheen\OAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface;

class Wechat extends AbstractProvider
{
    use ArrayAccessorTrait;

    protected $appid;
    protected $secret;
    protected $redirect_uri;

    /**
     * Production Graph API URL.
     *
     * @const string
     */
    const BASE_WECHAT_AUTHORIZATION_URL = 'https://open.weixin.qq.com/connect';

    /**
     * Beta tier URL of the Graph API.
     *
     * @const string
     */
    const BASE_WECHAT_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com';

    /**
     * Returns authorization parameters based on provided options.
     *
     * @param  array $options
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options)
    {
        $options += [
            'appid' => $this->appid
        ];

        if (!isset($options['redirect_uri'])) {
            $options['redirect_uri'] = $this->redirect_uri;
        }

        $options += [
            'response_type' => 'code'
        ];

        if (empty($options['scope'])) {
            $options['scope'] = 'snsapi_login';
        }

        if (empty($options['state'])) {
            // $options['state'] = $this->getRandomState().'#wechat_redirect';
            $options['state'] = $this->getRandomState();
        }

        // Store the state as it may need to be accessed later on.
        $this->state = $options['state'];

        return $options;
    }

    public function getBaseAuthorizationUrl()
    {
        return self::BASE_WECHAT_AUTHORIZATION_URL.'/qrconnect';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return self::BASE_WECHAT_ACCESS_TOKEN_URL.'/sns/oauth2/access_token';
    }

   /**
     * override
     * Appends a query string and '#wechat_redirect' to a URL.
     *
     * @param  string $url The URL to append the query to
     * @param  string $query The HTTP query string
     * @return string The resulting URL
     */
    protected function appendQuery($url, $query)
    {
        $query = trim($query, '?&');

        if ($query) {
            return sprintf("%s?%s#wechat_redirect", $url, $query);
        }

        return $url;
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    public function getDefaultScopes()
    {
        return ['snsapi_userinfo'];
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $access_token = $token->getToken();
        $openid = $token->getValues()['openid'];

        return sprintf("%s/sns/userinfo?access_token=%s&openid=%s", self::BASE_WECHAT_ACCESS_TOKEN_URL, $access_token, $openid);
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessToken
     */
    public function getAccessToken($grant, array $options = [])
    {
        $grant = $this->verifyGrant($grant);

        $params = [
            'appid'  => $this->appid,
            'secret' => $this->secret
        ];

        $params   = $grant->prepareRequestParameters($params, $options);
        $request  = $this->getAccessTokenRequest($params);
        $response = $this->getResponse($request);
        $prepared = $this->prepareAccessTokenResponse($response);
        $token    = $this->createAccessToken($prepared, $grant);

        return $token;
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new WechatUser($response);
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string|\Psr\Http\Message\ResponseInterface $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $errcode = $this->getValueByKey($data, 'errcode');
        $errmsg = $this->getValueByKey($data, 'errmsg');

        if ($errcode || $errmsg) {
            throw new IdentityProviderException($errmsg, $errcode, $data);
        };
    }
}
