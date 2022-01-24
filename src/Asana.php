<?php
namespace CarloNicora\Minimalism\Services\Asana;

use Asana\Client;
use CarloNicora\Minimalism\Abstracts\AbstractService;
use RuntimeException;

class Asana extends AbstractService
{
    /** @var Client|null  */
    private ?Client $client=null;

    /** @var bool  */
    private bool $isAuthorised;

    /** @var string|null */
    private ?string $token=null;

    /**
     * @param string|null $MINIMALISM_SERVICE_ASANA_TOKEN
     * @param string|null $MINIMALISM_SERVICE_ASANA_CLIENT_ID
     * @param string|null $MINIMALISM_SERVICE_ASANA_CLIENT_SECRET
     * @param string|null $MINIMALISM_SERVICE_ASANA_REDIRECT
     */
    public function __construct(
        private ?string $MINIMALISM_SERVICE_ASANA_TOKEN=null,
        private ?string $MINIMALISM_SERVICE_ASANA_CLIENT_ID=null,
        private ?string $MINIMALISM_SERVICE_ASANA_CLIENT_SECRET=null,
        private ?string $MINIMALISM_SERVICE_ASANA_REDIRECT=null,
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
     */
    public function initialise(
    ): void
    {
        parent::initialise();

        if(array_key_exists('asana_token', $_SESSION)){
            $this->token = $_SESSION['asana_token'];
        }

        if ($this->MINIMALISM_SERVICE_ASANA_TOKEN !== null){
            $this->client = Client::accessToken($this->MINIMALISM_SERVICE_ASANA_TOKEN);
            $this->isAuthorised = true;
        } else {
            if ($this->token !== null){
                $this->client = Client::oauth([
                    'client_id' => $this->MINIMALISM_SERVICE_ASANA_CLIENT_ID,
                    'token' => $this->token,
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
    }

    /**
     * @return void
     */
    public function destroy(
    ): void
    {
        if ($this->token !== null) {
            $_SESSION['asana_token'] = $this->token;
        }

        parent::destroy();
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
}