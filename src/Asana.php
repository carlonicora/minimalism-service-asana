<?php
namespace CarloNicora\Minimalism\Services\Asana;

use Asana\Client;
use CarloNicora\Minimalism\Abstracts\AbstractService;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaTaskCommand;
use CarloNicora\Minimalism\Services\Asana\Commands\AsanaUserCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaUser;
use Exception;
use RuntimeException;

class Asana extends AbstractService
{
    /** @var Client|null  */
    private ?Client $client=null;

    /** @var bool  */
    private bool $isAuthorised;

    /** @var string|null */
    private ?string $token=null;

    /** @var string|null */
    private ?string $refreshToken=null;

    /** @var int|null  */
    private ?int $expiration=null;

    /** @var AsanaUser|null  */
    private ?AsanaUser $user=null;

    /**
     * @param string|null $MINIMALISM_SERVICE_ASANA_TOKEN
     * @param string|null $MINIMALISM_SERVICE_ASANA_CLIENT_ID
     * @param string|null $MINIMALISM_SERVICE_ASANA_CLIENT_SECRET
     * @param string|null $MINIMALISM_SERVICE_ASANA_REDIRECT
     */
    public function __construct(
        private readonly ?string $MINIMALISM_SERVICE_ASANA_TOKEN=null,
        private readonly ?string $MINIMALISM_SERVICE_ASANA_CLIENT_ID=null,
        private readonly ?string $MINIMALISM_SERVICE_ASANA_CLIENT_SECRET=null,
        private readonly ?string $MINIMALISM_SERVICE_ASANA_REDIRECT=null,
    )
    {
        if (
            $this->MINIMALISM_SERVICE_ASANA_TOKEN === null
            && $this->MINIMALISM_SERVICE_ASANA_CLIENT_ID === null
            && $this->MINIMALISM_SERVICE_ASANA_CLIENT_SECRET === null
            && $this->MINIMALISM_SERVICE_ASANA_REDIRECT === null
        ){
            throw new RuntimeException('Minimalism service asana requires either a personal access token or a client in its configurations', 500);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function initialise(
    ): void
    {
        parent::initialise();

        if (array_key_exists('asana_token', $_SESSION) && $_SESSION['asana_token'] !== null) {
            $this->token = $_SESSION['asana_token'];
        } elseif (array_key_exists('asana_token', $_COOKIE) && $_COOKIE['asana_token'] !== null) {
            $this->token = $_COOKIE['asana_token'];
        }

        if (array_key_exists('asana_refresh_token', $_SESSION) && $_SESSION['asana_refresh_token'] !== null) {
            $this->refreshToken = $_SESSION['asana_refresh_token'];
        } elseif (array_key_exists('asana_refresh_token', $_COOKIE) && $_COOKIE['asana_refresh_token'] !== null) {
            $this->refreshToken = $_COOKIE['asana_refresh_token'];
        }

        if (array_key_exists('asana_expiration', $_SESSION) && $_SESSION['asana_expiration'] !== null) {
            $this->expiration = $_SESSION['asana_expiration'];
        } elseif (array_key_exists('asana_expiration', $_COOKIE) && $_COOKIE['asana_expiration'] !== null) {
            $this->expiration = $_COOKIE['asana_expiration'];
        }

        $this->authorise();
    }

    /**
     * @return void
     */
    public function destroy(
    ): void
    {
        if ($this->token !== null) {
            $_SESSION['asana_token'] = $this->token;
            $_SESSION['asana_refresh_token'] = $this->refreshToken;
            if (isset($this->client->dispatcher->expiresIn)) {
                $_SESSION['asana_expiration'] = $this->client->dispatcher->expiresIn + time();
            } else {
                $_SESSION['asana_expiration'] = $this->expiration;
            }
            setcookie('asana_token', $this->token, time() + (60 * 60 * 24 * 365), "/", ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
            setcookie('asana_refresh_token', $this->refreshToken, time() + (60 * 60 * 24 * 365), "/", ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
            setcookie('asana_expiration', $this->expiration, time() + (60 * 60 * 24 * 365), "/", ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));

            $this->user->destroy();
            $_SESSION['asana_user'] = serialize($this->user);
        }

        parent::destroy();
        $this->user = null;
        $this->client = null;
        $this->token = null;
        $this->refreshToken = null;
        $this->expiration = null;
    }

    /**
     * @return Client
     */
    public function getClient(
    ): Client
    {
        return $this->client;
    }

    public function isAuthorised(
    ): bool {
        return $this->isAuthorised;
    }

    /**
     * @return void
     */
    public function authorise(
    ): void
    {
        if ($this->MINIMALISM_SERVICE_ASANA_TOKEN !== null){
            if ($this->client === null) {
                $this->client = Client::accessToken($this->MINIMALISM_SERVICE_ASANA_TOKEN);
                $this->isAuthorised = true;
            }
        } else {
            if ($this->token !== null){
                $this->client = Client::oauth([
                    'client_id' => $this->MINIMALISM_SERVICE_ASANA_CLIENT_ID,
                    'client_secret' => $this->MINIMALISM_SERVICE_ASANA_CLIENT_SECRET,
                    'token' => $this->token,
                    'refresh_token' => $this->refreshToken,
                    'redirect_uri' => $this->MINIMALISM_SERVICE_ASANA_REDIRECT,
                ]);

                if (!$this->client->dispatcher->authorized){
                    $this->token = $this->client->dispatcher->refreshAccessToken();
                }
            } else {
                $this->client = Client::oauth([
                    'client_id' => $this->MINIMALISM_SERVICE_ASANA_CLIENT_ID,
                    'client_secret' => $this->MINIMALISM_SERVICE_ASANA_CLIENT_SECRET,
                    'redirect_uri' => $this->MINIMALISM_SERVICE_ASANA_REDIRECT,
                ]);
            }

            if ($this->expiration !== null && ($this->expiration - time()) > 60){
                $this->client->dispatcher->setExpiresInSeconds($this->expiration - time());
            } else {
                $this->client->dispatcher->setExpiresInSeconds(0);
            }

            $this->isAuthorised = $this->client->dispatcher->authorized;
        }

        if ($this->isAuthorised && array_key_exists('asana_user', $_SESSION)){
            $this->user = unserialize($_SESSION['asana_user'], [AsanaUser::class]);
            $this->user->initialise($this->objectFactory);
        }
    }

    /**
     * @param string $token
     */
    public function setToken(
        string $token
    ): void
    {
        $this->token = $token;
    }

    public function setRefreshToken(
        string $refreshToken,
    ): void {
        $this->refreshToken = $refreshToken;
    }

    public function setExpiration(
        int $expiration
    ): void {
        $this->expiration = time() + $expiration;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function refreshUser(
    ): void
    {
        $this->user = $this->objectFactory->create(AsanaUserCommand::class)->getUser();
    }

    /**
     * @return AsanaUser|null
     */
    public function getUser(): ?AsanaUser
    {
        if ($this->user === null && $this->isAuthorised){
            try {
                $this->user = $this->objectFactory->create(AsanaUserCommand::class)->getUser();
            } catch (Exception) {
                $this->user = null;
            }
        }

        return $this->user;
    }

    /**
     * @return AsanaTaskCommand
     * @throws Exception
     */
    public function tasks(
    ): AsanaTaskCommand
    {
        return $this->objectFactory->create(AsanaTaskCommand::class);
    }

    /**
     * @return AsanaUserCommand
     * @throws Exception
     */
    public function users(
    ): AsanaUserCommand
    {
        return $this->objectFactory->create(AsanaUserCommand::class);
    }
}