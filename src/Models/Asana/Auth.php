<?php
namespace CarloNicora\Minimalism\Services\Asana\Models\Asana;

use CarloNicora\Minimalism\Abstracts\AbstractModel;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Services\Asana\Asana;
use JetBrains\PhpStorm\NoReturn;
use RuntimeException;

class Auth extends AbstractModel
{
    /**
     * @param Asana $asana
     * @param string $code
     * @param string $state
     * @return void
     */
    #[NoReturn] public function get(
        Asana $asana,
        string $code,
        string $state,
    ): void
    {
        if ((string)$_SESSION['asana_state'] !== $state) {
            throw new RuntimeException('OAuth State does not match', HttpCode::InternalServerError->value);
        }
        unset($_SESSION['asana_state']);

        $dispatcher = $asana->getClient()->dispatcher;

        $asana->setToken($dispatcher->fetchToken($code));
        $asana->setRefreshToken($dispatcher->refreshToken);
        $dispatcher->setExpiresInSeconds($dispatcher->expiresIn);
        $asana->setExpiration($dispatcher->getExpiresInSeconds() ?? 0);

        $redirect = $_SESSION['asana_redirect'];
        unset($_SESSION['asana_redirect']);

        header('Location:' . $redirect);
        exit;
    }
}