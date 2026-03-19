<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2020-01-30 14:40
 */

namespace common\services;

interface PageServiceInterface extends ServiceInterface
{
    const ServiceName = 'pageService';

    public function getPageSubTitle($subTitle);

    public function getById($aid);

    public function getCountByPeriod($startAt=null, $endAt=null);

    public function getSinglePages();

    public function getFrontendURLManager();

}