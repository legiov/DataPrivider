<?php

namespace DataProvider;


class ConcreteDataProvider extends AbstractDataProviderWithCache
{

    private $host;
    private $user;
    private $password;

    /**
     * @param $host
     * @param $user
     * @param $password
     */
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @throws DataProviderException
     * @param DataProviderInputDataInterface $input
     * @return mixed
     */
    protected function get(DataProviderInputDataInterface $input)
    {
        // TODO: Implement get() method.
        // returns a response from external service
    }
}