<?php
namespace CarloNicora\Minimalism\Services\Asana\Models\Asana;

use CarloNicora\Minimalism\Abstracts\AbstractModel;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Services\Asana\Asana;
use JetBrains\PhpStorm\NoReturn;

class Start extends AbstractModel
{
    /**
     * @param Asana $asana
     * @return HttpCode
     */
    #[NoReturn] public function get(
        Asana $asana,
    ): HttpCode
    {
        $state = null;
        $url = $asana->getClient()->dispatcher->authorizationUrl($state);

        $_SESSION['asana_state'] = $state;
        $_SESSION['asana_redirect'] = $_SERVER['HTTP_REFERER'];

        header('Location:' . $url);
        exit;
    }
}