<?php
namespace CarloNicora\Minimalism\Services\Asana;

use Asana\Client;
use CarloNicora\Minimalism\Abstracts\AbstractService;
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

        if ($this->MINIMALISM_SERVICE_ASANA_TOKEN !== null){
            $this->client = Client::accessToken($this->MINIMALISM_SERVICE_ASANA_TOKEN);
            $this->isAuthorised = true;
        } else {
            if ($this->token !== null){
                $this->client = Client::oauth([
                    'client_id' => $this->MINIMALISM_SERVICE_ASANA_CLIENT_ID,
                    'client_secret' => $this->MINIMALISM_SERVICE_ASANA_CLIENT_SECRET,
                    'token' => $this->token,
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

            $this->isAuthorised = $this->client->dispatcher->authorized;
        }

        if ($this->isAuthorised && array_key_exists('asana_user', $_SESSION)){
            $this->user = unserialize($_SESSION['asana_user'], [AsanaUser::class]);
            $this->user->initialise($this->objectFactory);
        }
    }

    /**
     * @return void
     */
    public function destroy(
    ): void
    {
        if ($this->token !== null) {
            $_SESSION['asana_token'] = $this->token;
            setcookie('asana_token', $this->token, time() + (60 * 60 * 24 * 365), "/", ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));

            $this->user->destroy();
            $_SESSION['asana_user'] = serialize($this->user);
        }

        parent::destroy();
        $this->user = null;
        $this->client = null;
        $this->token = null;
    }

    /**
     * @return Client
     */
    public function getClient(
    ): Client
    {
        return $this->client;
    }

    /**
     * @return bool
     */
    public function isAuthorised(
    ): bool
    {
        return $this->isAuthorised;
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
}