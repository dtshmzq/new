<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2020-01-30 14:21
 */

namespace common\services;

interface TagsServiceInterface extends ServiceInterface
{
    const ServiceName = 'tagsService';

    public function getCountByPeriod($startAt=null, $endAt=null);
}