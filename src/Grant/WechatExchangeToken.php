<?php

namespace RycCheen\OAuth2\Client\Grant;

use League\OAuth2\Client\Grant\AbstractGrant;

class WechatExchangeToken extends AbstractGrant
{
    public function __toString()
    {
        return 'wechatexchange_token';
    }

    protected function getRequiredRequestParameters()
    {
        return [
            'wechat_exchange_token',
        ];
    }

    protected function getName()
    {
        return 'wechat_exchange_token';
    }
}
