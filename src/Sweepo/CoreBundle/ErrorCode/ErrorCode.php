<?php

namespace Sweepo\CoreBundle\ErrorCode;

class ErrorCode
{
    // API
    const TOKEN_MISSING          = 'token_missing';
    const INVALID_PARAMETER      = 'invalid_parameter';

    // User
    const USER_NOT_FOUND         = 'user_not_found';

    // Subscription
    const SUBSCRIPTION_NOT_FOUND = 'subscription_not_found';

    // Tweets
    const TWEETS_NOT_FOUND       = 'tweets_not_found';
}