<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

####################################################################################################
/**
 * Trusting the Heroku Router
 *
 * In order to preserve the ability to set additional trusted proxy IP ranges (for instance when using a CDN),
 * while at the same time trusting the Heroku router,
 * you could combine the existing logic that reads the TRUSTED_PROXIES environment variable
 * with a conditional addition of REMOTE_ADDR to the list depending on the application env.
 */
$trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false;
$trustedProxies = $trustedProxies ? explode(',', $trustedProxies) : [];
if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'prod') {
    $trustedProxies[] = $_SERVER['REMOTE_ADDR'];
}
if ($trustedProxies) {
    Request::setTrustedProxies($trustedProxies, Request::HEADER_X_FORWARDED_AWS_ELB);
}
####################################################################################################

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
