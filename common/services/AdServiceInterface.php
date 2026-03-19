<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2020-01-29 14:42
 */

namespace common\services;


interface AdServiceInterface extends ServiceInterface
{
    const ServiceName = "adService";

    public function getAdByName($name);
}