<?php

/**
 * This class have common request for all Models|Entities => getOne(), getMany based on id...
 *
 * Class BaseRepository
 */
class BaseRepository
{

    protected $model;

    /**
     * @param $number
     * @return model
     */
    public function getOneByRef($number)
    {
        $builder = $this->getEntityRepository($this->model)->getByRef($number);

        return $builder;
    }
}