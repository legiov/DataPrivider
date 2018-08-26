<?php

namespace DataProvider;


/**
 * Interface DataProviderInterface
 * @package DataProvider
 */
interface DataProviderInterface
{
    /**
     * Отключение кеша
     */
    public function setNoCache(): void;

    /**
     * Передаем валидную строку для изменения \DateTime::modify($expired)
     * @param $expired
     */
    public function setCacheExpired(string $expired): void;

    /**
     * @param DataProviderInputDataInterface $input
     * @return array|mixed
     * @throws DataProviderException
     */
    public function getResponse(DataProviderInputDataInterface $input);
}