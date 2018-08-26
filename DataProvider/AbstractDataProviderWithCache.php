<?php

namespace DataProvider;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Cache\CacheException;

/**
 * WithCache в названии класса спорно, и может зависеть от принятых в компании стандартов,
 * но в данной ситуации показалось что оно будет уместно
 * Class AbstractDataProviderWithCache
 * @package Decorator
 */
abstract class AbstractDataProviderWithCache implements DataProviderInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;

    /**
     * Строка модификации объекта \DateTime от текущего времени
     *
     * @var string
     */
    private $cacheExpired = '+1 day';

    /**
     * Используем кеш
     * @var bool
     */
    private $needCache = true;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function setCachePool(CacheItemPoolInterface $cacheItemPool): void
    {
        $this->cachePool = $cacheItemPool;
    }

    /**
     * Отключение кеша
     */
    public function setNoCache(): void
    {
        $this->needCache = false;
    }

    /**
     * Передаем валидную строку для изменения \DateTime::modify($expired)
     * @param $expired
     */
    public function setCacheExpired(string $expired):void
    {
        $this->cacheExpired = $expired;
    }

    /**
     * @param DataProviderInputDataInterface $input
     * @return array|mixed
     * @throws DataProviderException
     */
    public function getResponse(DataProviderInputDataInterface $input)
    {

            $useCache = $this->needCache && null !== $this->cachePool;
            $cacheItem = null;

            if ($useCache) {
                try {
                    $cacheKey = $input->toCacheString();

                    $cacheItem = $this->cachePool->getItem($cacheKey);
                    if ($cacheItem->isHit()) {
                        return $cacheItem->get();
                    }
                } catch (CacheException $e) {
                    $this->logError('Cache Exception', $e);
                }
            }
            try {
                $result = $this->get($input);
            } catch (DataProviderException $e) {
                $this->logError('Error', $e);

                throw $e;
            }

            if ($useCache && $cacheItem) {
                $cacheItem
                    ->set($result)
                    ->expiresAt(
                        (new \DateTime())->modify($this->cacheExpired)
                    );

                $this->cachePool->save($cacheItem);
            }

            return $result;

    }

    /**
     * предпочтительно чтобы логгер умел писать стандартный лог для ошибок, $this->logger->logException($e), но это
     * выходит за рамки текущей задачи.
     * Здесь для примера реализован вывод ошибки более информативный чем был ранее)
     *
     * @param $message
     * @param \Exception $e
     */
    private function logError($message, \Exception $e):void
    {
        if (null !== $this->logger) {
            $this->logger->critical(
                printf(
                    '%s in file %s in line %d with message %s',
                    $message, $e->getFile(), $e->getLine(), $e->getMessage()
                )
            );

            $this->logger->critical($e->getTraceAsString());
        }
    }

    /**
     * Метод получения данных из сервиса
     *
     * @param DataProviderInputDataInterface $input
     * @return mixed
     * @throws DataProviderException
     */
    abstract protected function get(DataProviderInputDataInterface $input);

}