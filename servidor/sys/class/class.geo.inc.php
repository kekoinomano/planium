<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class geo extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    private function getMaxId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM users");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getPeopleNearbyCount($lat, $lng, $distance = 30)
    {

        $tableName = "users";
        $origLat = $lat;
        $origLon = $lng;
        $dist = $distance; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search

        $sql = "SELECT id, 3956 * 2 *
          ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
          +COS($origLat*pi()/180 )*COS(lat*pi()/180)
          *POWER(SIN(($origLon-lng)*pi()/180/2),2)))
          as distance FROM $tableName WHERE
          lng between ($origLon-$dist/cos(radians($origLat))*69)
          and ($origLon+$dist/cos(radians($origLat))*69)
          and lat between ($origLat-($dist/69))
          and ($origLat+($dist/69))
          and (id <> $this->requestFrom)
          and (state = 0)
          and (account_type = 0)
          having distance < $dist";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getPeopleNearby($itemId, $lat, $lng, $distance = 30, $sex = -1)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxId();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $tableName = "users";
        $origLat = $lat;
        $origLon = $lng;
        $dist = $distance; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search

        if ($sex == -1) {

            $sql = "SELECT id, lat, lng, 3956 * 2 *
                    ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                    +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                    *POWER(SIN(($origLon-lng)*pi()/180/2),2)))
                    as distance FROM $tableName WHERE
                    lng between ($origLon-$dist/cos(radians($origLat))*69)
                    and ($origLon+$dist/cos(radians($origLat))*69)
                    and lat between ($origLat-($dist/69))
                    and ($origLat+($dist/69))
                    and (id < $itemId)
                    and (id <> $this->requestFrom)
                    and (state = 0)
                    and (account_type = 0)
                    having distance < $dist ORDER BY id DESC limit 20";

        } else {

            $sql = "SELECT id, lat, lng, 3956 * 2 *
                    ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                    +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                    *POWER(SIN(($origLon-lng)*pi()/180/2),2)))
                    as distance FROM $tableName WHERE
                    lng between ($origLon-$dist/cos(radians($origLat))*69)
                    and ($origLon+$dist/cos(radians($origLat))*69)
                    and lat between ($origLat-($dist/69))
                    and ($origLat+($dist/69))
                    and (id < $itemId)
                    and (id <> $this->requestFrom)
                    and (state = 0)
                    and (account_type = 0)
                    and (sex = $sex)
                    having distance < $dist ORDER BY id DESC limit 20";
        }

        $stmt = $this->db->prepare($sql);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['id']);
                    $profile->setRequestFrom($this->requestFrom);
                    $profileInfo = $profile->get();
                    $profileInfo['distance'] = round($this->getDistance($lat, $lng, $profileInfo['lat'], $profileInfo['lng']), 1);
                    unset($profile);

                    array_push($result['items'], $profileInfo);

                    $result['itemId'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $result;
    }
    public function getPlanes2($itemId, $lat, $lng, $distance = 30, $sex = -1, $distancia = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxId();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $tableName = "users";
        $origLat = $lat;
        $origLon = $lng;
        $dist = $distance; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search
        if($distancia==0){
            $sql = "SELECT id, lat, lng, 3956 * 2 *
                    ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                    +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                    *POWER(SIN(($origLon-lng)*pi()/180/2),2)))
                    as distance FROM $tableName WHERE account_type='1' ORDER BY distance ASC limit 20";
        }else{
            if($distancia=="1"){
               $dist=3; 
            }if($distancia=="2"){
               $dist=300; 
            }if($distancia=="3"){
               $dist=600; 
            }if($distancia=="4"){
               $dist=2000; 
            }
            $sql = "SELECT id, lat, lng, 3956 * 2 *
                    ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                    +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                    *POWER(SIN(($origLon-lng)*pi()/180/2),2)))
                    as distance FROM $tableName WHERE account_type='1' AND
                    lng between ($origLon-$dist/cos(radians($origLat))*69)
                    and ($origLon+$dist/cos(radians($origLat))*69)
                    and lat between ($origLat-($dist/69))
                    and ($origLat+($dist/69))
                    having distance < $dist ORDER BY id DESC limit 20";
        }


        $stmt = $this->db->prepare($sql);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['id']);
                    $profile->setRequestFrom($this->requestFrom);
                    $profileInfo = $profile->get();
                    $profileInfo['distance'] = round($this->getDistance($lat, $lng, $profileInfo['lat'], $profileInfo['lng']), 1);
                    unset($profile);

                    array_push($result['items'], $profileInfo);

                    $result['itemId'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $result;
    }
    public function getPlanes($itemId, $lat, $lng, $distance = 30, $sex = -1)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxId();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $tableName = "planes";
        $origLat = $lat;
        $origLon = $lng;
        

            $sql = "SELECT id, name, city, lat, lng FROM $tableName ORDER BY id DESC limit 20";

        $stmt = $this->db->prepare($sql);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new plan($this->db, $row['id']);
                    $profile->setRequestFrom($this->requestFrom);
                    $profileInfo = $profile->get();
                    $profileInfo['distance'] = round($this->getDistance($lat, $lng, $profileInfo['lat'], $profileInfo['lng']), 1);
                    unset($profile);

                    array_push($result['items'], $profileInfo);

                    $result['itemId'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $result;
    }
    public function getPlanes3($itemId, $lat, $lng, $distance = 30, $sex = -1, $distancia = 0, $categoria = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxId();
            $itemId++;
        }
        if ($categoria !==0){
            $categoria = " AND account_category = '". $categoria. "'";
        }else{
            $categoria = "";
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $tableName = "users";
        $origLat = $lat;
        $origLon = $lng;
        $dist = $distance; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search
        $fin=" AND finalidad LIKE '%%"."Todo"."%%'";
        if($distancia==0){
            $sql = "SELECT id, lat, lng, 3956 * 2 *
                    ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                    +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                    *POWER(SIN(($origLon-lng)*pi()/180/2),2)))
                    as distance FROM $tableName WHERE account_type='1' $categoria $fin ORDER BY distance ASC limit 20";
        }else{
            if($distancia=="1"){
               $dist=5; 
            }if($distancia=="2"){
               $dist=300; 
            }if($distancia=="3"){
               $dist=600; 
            }if($distancia=="4"){
               $dist=2000; 
            }
            $sql = "SELECT id, lat, lng, 3956 * 2 *
                    ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                    +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                    *POWER(SIN(($origLon-lng)*pi()/180/2),2)))
                    as distance FROM $tableName WHERE account_type='1' $categoria $fin AND
                    lng between ($origLon-$dist/cos(radians($origLat))*69)
                    and ($origLon+$dist/cos(radians($origLat))*69)
                    and lat between ($origLat-($dist/69))
                    and ($origLat+($dist/69))
                    having distance < $dist ORDER BY id DESC limit 20";
        }


        $stmt = $this->db->prepare($sql);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['id']);
                    $profile->setRequestFrom($this->requestFrom);
                    $profileInfo = $profile->get();
                    $profileInfo['distance'] = round($this->getDistance($lat, $lng, $profileInfo['lat'], $profileInfo['lng']), 1);
                    unset($profile);

                    array_push($result['items'], $profileInfo);

                    $result['itemId'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $result;
    }

    public function getDistance($fromLat, $fromLng, $toLat, $toLng) {

        $latFrom = deg2rad($fromLat);
        $lonFrom = deg2rad($fromLng);
        $latTo = deg2rad($toLat);
        $lonTo = deg2rad($toLng);

        $delta = $lonTo - $lonFrom;

        $alpha = pow(cos($latTo) * sin($delta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($delta), 2);
        $beta = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($delta);

        $angle = atan2(sqrt($alpha), $beta);

        return ($angle * 6371000) / 1000;
    }

    public function info($ip_addr)
    {
        $info = helper::getContent('http://www.geoplugin.net/json.gp?ip='.$ip_addr);

        return json_decode($info, true);
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }
}

