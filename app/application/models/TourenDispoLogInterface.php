<?php
/**
 * Created by PhpStorm.
 * User: f.barthold
 * Date: 28.08.2019
 * Time: 13:53
 */

interface Model_TourenDispoLogInterface
{
    public function getTourLogDetails(int $tour_id);

    public function log($oType, $oId, $action, $tour_id = 0, $uid = null, $sperrzeiten_id = null, array $aDetails = []);

    public function logTour($tour_id, $action, $uid = null, array $aDetails = []);

    public function logTimeline($timeline_id, $action, $uid = null, array $aDetails = []);

    public function logPortlet($portlet_id, $action, $uid = null);

    public function logTourenplan($portlet_id, $action, $uid = null, array $aDetails = []);

    public function logResourceFP($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []);

    public function logResourceMA($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []);

    public function logResourceWZ($resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []);

    public function logResource($resource_type, $resource_id, $action, $tour_id, $uid = null, $sperrzeiten_id = null, array $aDetails = []);

    public function getTourHistorie(int $iTourID, array $aQueryOptions = []);

    public function getHistorie(array $queryOptions = []);
}