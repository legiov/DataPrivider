<?php

namespace DataProvider;


interface DataProviderInputDataInterface
{
    /**
     * Должен возвращать строку уникальную для набора данных
     * @return string
     */
    public function toCacheString():string;
}