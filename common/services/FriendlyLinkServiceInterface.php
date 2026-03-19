<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2020-01-23 09:38
 */

namespace common\services;


interface FriendlyLinkServiceInterface extends ServiceInterface {
    const ServiceName = "friendlyLinkService";

    public function getFriendlyLinks();

    public function getFriendlyLinkCountByPeriod($startAt=null, $endAt=null);
}