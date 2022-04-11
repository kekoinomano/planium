<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk, https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class plan extends db_connect
{

    private $id = 0;
    private $requestFrom = 0;

    public function __construct($dbo = NULL, $profileId)
    {

        parent::__construct($dbo);

        $this->setId($profileId);
    }

    public function lastIndex()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM planes2");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn() + 1;
    }

    public function get()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM planes WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();


                $current_time = time();

                $time = new language($this->db);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "city" => $row['city'],
                                "name" => $row['name'],
                                "lat" => $row['lat'],
                                "lng" => $row['lng']);
            }
        }

        return $result;
    }
    public function get2()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                // test to blocked

                $online = false;

                $current_time = time();

                if ($row['last_authorize'] != 0 && $row['last_authorize'] > ($current_time - 15 * 60)) {

                    $online = true;
                }

                $time = new language($this->db);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "state" => $row['state'],
                                "accountType" => $row['account_type'],
                                "accountCategory" => $row['account_category'],
                                "accountAuthor" => $row['account_author'],
                                "mood" => $row['mood'],
                                "pro" => $row['pro'],
                                "pro_create_at" => $row['pro_create_at'],
                                "sex" => $row['sex'],
                                "age" => $row['age'],
                                "year" => $row['bYear'],
                                "month" => $row['bMonth'],
                                "day" => $row['bDay'],
                                "username" => $row['login'],
                                "fullname" => htmlspecialchars_decode(stripslashes($row['fullname'])),
                                "location" => stripcslashes($row['country']),
                                "status" => stripcslashes($row['status']),
                                "fb_page" => stripcslashes($row['fb_page']),
                                "instagram_page" => stripcslashes($row['my_page']),
                                "my_page" => stripcslashes($row['my_page']),
                                "verify" => $row['verify'],
                                "verified" => $row['verify'],
                                "lat" => $row['lat'],
                                "lng" => $row['lng'],
                                "lowPhotoUrl" => $row['lowPhotoUrl'],
                                "bigPhotoUrl" => $row['bigPhotoUrl'],
                                "normalPhotoUrl" => $row['normalPhotoUrl'],
                                "normalCoverUrl" => $row['normalCoverUrl'],
                                "originCoverUrl" => $row['originCoverUrl'],
                                "coverPosition" => $row['coverPosition'],
                                "allowComments" => $row['allowComments'],
                                "allowPhotosComments" => $row['allowPhotosComments'],
                                "allowVideoComments" => $row['allowVideoComments'],
                                "allowGalleryComments" => $row['allowGalleryComments'],
                                "allowMessages" => $row['allowMessages'],
                                "referralsCount" => $row['referrals_count'],
                                "postsCount" => $row['posts_count'],
                                "followersCount" => $row['followers_count'],
                                "likesCount" => $row['likes_count'],
                                "photosCount" => $row['photos_count'],
                                "galleryItemsCount" => $row['gallery_items_count'],
                                "videosCount" => $row['videos_count'],
                                "giftsCount" => $row['gifts_count'],
                                "friendsCount" => $row['friends_count'],
                                "allowShowMyInfo" => $row['allowShowMyInfo'],
                                "allowShowMyVideos" => $row['allowShowMyVideos'],
                                "allowShowMyFriends" => $row['allowShowMyFriends'],
                                "allowShowMyPhotos" => $row['allowShowMyPhotos'],
                                "allowShowMyGallery" => $row['allowShowMyGallery'],
                                "allowShowMyGifts" => $row['allowShowMyGifts'],
                                "allowShowMyAgeAndGender" => $row['allowShowMyAgeAndGender'],
                                "follower" => $follower,
                                "inBlackList" => $inBlackList,
                                "follow" => $follow,
                                "friend" => $friend,
                                "blocked" => $blocked,
                                "lastAuthorize" => $row['last_authorize'],
                                "lastAuthorizeDate" => date("Y-m-d H:i:s", $row['last_authorize']),
                                "lastAuthorizeTimeAgo" => $time->timeAgo($row['last_authorize']),
                                "online" => $online,
                                "photoModerateAt" => $row['photoModerateAt'],
                                "photoModerateUrl" => $row['photoModerateUrl'],
                                "photoPostModerateAt" => $row['photoPostModerateAt'],
                                "coverModerateAt" => $row['coverModerateAt'],
                                "coverModerateUrl" => $row['coverModerateUrl'],
                                "coverPostModerateAt" => $row['coverPostModerateAt']);
            }
        }

        return $result;
    }
    public function create($name, $city)
    {

        $result = array("error" => true);


        $currentTime = time();


        $stmt = $this->db->prepare("INSERT INTO planes (name, city) value (:name, :city)");
        $stmt->bindParam(":name", $name, PDO::PARAM_INT);
        $stmt->bindParam(":city", $city, PDO::PARAM_STR);
        

        if ($stmt->execute()) {

            $this->setId($this->db->lastInsertId());

            $this->setLanguage("en");

            $result = array("error" => false,
                            'groupId' => $this->id,
                            'name' => $name,
                            'city' => $city,
                            'error_code' => ERROR_SUCCESS,
                            'error_description' => 'Group Create Success!');

            return $result;
        }

        return $result;
    }
    public function create2($group_name, $group_fullname, $group_category, $group_desc, $group_site, $group_location, $year, $month, $day, $group_allow_posts, $group_allow_comments)
    {

        $result = array("error" => true);

        $helper = new helper($this->db);

        if (!helper::isCorrectLogin($group_name)) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 0,
                            "error_description" => "Incorrect Group Name (Login)");

            return $result;
        }

        if ($helper->isLoginExists($group_name)) {

            $result = array("error" => true,
                            "error_code" => ERROR_LOGIN_TAKEN,
                            "error_type" => 1,
                            "error_description" => "Login (Group Name) already taken");

            return $result;
        }

        if (strlen($group_fullname) == 0) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 3,
                            "error_description" => "Empty group full name");

            return $result;
        }

        $currentTime = time();

        $ip_addr = helper::ip_addr();

        $accountState = ACCOUNT_STATE_ENABLED;
        $accountType = ACCOUNT_TYPE_GROUP;

        $stmt = $this->db->prepare("INSERT INTO users (state, login, fullname, account_author, account_type, account_category, status, country, my_page, allowComments, allowPosts, bYear, bMonth, bDay, regtime, ip_addr) value (:state, :username, :fullname, :account_author, :account_type, :account_category, :status, :country, :my_page, :allowComments, :allowPosts, :bYear, :bMonth, :bDay, :createAt, :ip_addr)");
        $stmt->bindParam(":state", $accountState, PDO::PARAM_INT);
        $stmt->bindParam(":username", $group_name, PDO::PARAM_STR);
        $stmt->bindParam(":fullname", $group_fullname, PDO::PARAM_STR);
        $stmt->bindParam(":account_author", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":account_type", $accountType, PDO::PARAM_INT);
        $stmt->bindParam(":account_category", $group_category, PDO::PARAM_INT);
        $stmt->bindParam(":status", $group_desc, PDO::PARAM_STR);
        $stmt->bindParam(":country", $group_location, PDO::PARAM_STR);
        $stmt->bindParam(":my_page", $group_site, PDO::PARAM_STR);
        $stmt->bindParam(":allowComments", $group_allow_comments, PDO::PARAM_INT);
        $stmt->bindParam(":allowPosts", $group_allow_posts, PDO::PARAM_INT);
        $stmt->bindParam(":bYear", $year, PDO::PARAM_INT);
        $stmt->bindParam(":bMonth", $month, PDO::PARAM_INT);
        $stmt->bindParam(":bDay", $day, PDO::PARAM_INT);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $this->setId($this->db->lastInsertId());

            $this->setLanguage("en");

            $result = array("error" => false,
                            'groupId' => $this->id,
                            'username' => $group_name,
                            'fullname' => $group_fullname,
                            'error_code' => ERROR_SUCCESS,
                            'error_description' => 'Group Create Success!');

            return $result;
        }

        return $result;
    }

    public function getShort()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                // is my profile exists in blacklist
                $inBlackList = false;

                if ($this->requestFrom != 0) {

                    $blacklist = new blacklist($this->db);
                    $blacklist->setRequestFrom($this->getId());

                    if ($blacklist->isExists($this->getRequestFrom())) {

                        $inBlackList = true;
                    }

                    unset($blacklist);
                }

                $online = false;

                $current_time = time();

                if ($row['last_authorize'] != 0 && $row['last_authorize'] > ($current_time - 15 * 60)) {

                    $online = true;
                }

                $time = new language($this->db);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "rating" => $row['rating'],
                                "state" => $row['state'],
                                "mood" => $row['mood'],
                                "pro" => $row['pro'],
                                "pro_create_at" => $row['pro_create_at'],
                                "sex" => $row['sex'],
                                "age" => $row['age'],
                                "year" => $row['bYear'],
                                "month" => $row['bMonth'],
                                "day" => $row['bDay'],
                                "lat" => $row['lat'],
                                "lng" => $row['lng'],
                                "username" => $row['login'],
                                "fullname" => htmlspecialchars_decode(stripslashes($row['fullname'])),
                                "location" => stripcslashes($row['country']),
                                "status" => stripcslashes($row['status']),
                                "fb_page" => stripcslashes($row['fb_page']),
                                "instagram_page" => stripcslashes($row['my_page']),
                                "verify" => $row['verify'],
                                "verified" => $row['verify'],
                                "lowPhotoUrl" => $row['lowPhotoUrl'],
                                "bigPhotoUrl" => $row['bigPhotoUrl'],
                                "normalPhotoUrl" => $row['normalPhotoUrl'],
                                "normalCoverUrl" => $row['normalCoverUrl'],
                                "originCoverUrl" => $row['originCoverUrl'],
                                "coverPosition" => $row['coverPosition'],
                                "allowComments" => $row['allowComments'],
                                "allowPhotosComments" => $row['allowPhotosComments'],
                                "allowVideoComments" => $row['allowVideoComments'],
                                "allowGalleryComments" => $row['allowGalleryComments'],
                                "allowMessages" => $row['allowMessages'],
                                "referralsCount" => $row['referrals_count'],
                                "postsCount" => $row['posts_count'],
                                "followersCount" => $row['followers_count'],
                                "likesCount" => $row['likes_count'],
                                "photosCount" => $row['photos_count'],
                                "galleryItemsCount" => $row['gallery_items_count'],
                                "videosCount" => $row['videos_count'],
                                "giftsCount" => $row['gifts_count'],
                                "friendsCount" => $row['friends_count'],
//                                "allowShowMyBirthday" => $row['allowShowMyBirthday'],
//                                "friendsCount" => $row['friends_count'],
                                "allowShowMyInfo" => $row['allowShowMyInfo'],
                                "allowShowMyVideos" => $row['allowShowMyVideos'],
                                "allowShowMyFriends" => $row['allowShowMyFriends'],
                                "allowShowMyPhotos" => $row['allowShowMyPhotos'],
                                "allowShowMyGallery" => $row['allowShowMyGallery'],
                                "allowShowMyGifts" => $row['allowShowMyGifts'],
                                "allowShowMyAgeAndGender" => $row['allowShowMyAgeAndGender'],
                                "inBlackList" => $inBlackList,
                                "createAt" => $row['regtime'],
                                "createDate" => date("Y-m-d", $row['regtime']),
                                "lastAuthorize" => $row['last_authorize'],
                                "lastAuthorizeDate" => date("Y-m-d H:i:s", $row['last_authorize']),
                                "lastAuthorizeTimeAgo" => $time->timeAgo($row['last_authorize']),
                                "online" => $online,
                                "photoModerateAt" => $row['photoModerateAt'],
                                "photoModerateUrl" => $row['photoModerateUrl'],
                                "photoPostModerateAt" => $row['photoPostModerateAt'],
                                "coverModerateAt" => $row['coverModerateAt'],
                                "coverModerateUrl" => $row['coverModerateUrl'],
                                "coverPostModerateAt" => $row['coverPostModerateAt']);
            }
        }

        return $result;
    }

    public function getVeryShort()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $online = false;

                $current_time = time();

                if ($row['last_authorize'] != 0 && $row['last_authorize'] > ($current_time - 15 * 60)) {

                    $online = true;
                }

                $time = new language($this->db);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "rating" => $row['rating'],
                                "state" => $row['state'],
                                "accountType" => $row['account_type'],
                                "mood" => $row['mood'],
                                "pro" => $row['pro'],
                                "pro_create_at" => $row['pro_create_at'],
                                "sex" => $row['sex'],
                                "age" => $row['age'],
                                "year" => $row['bYear'],
                                "month" => $row['bMonth'],
                                "day" => $row['bDay'],
                                "lat" => $row['lat'],
                                "lng" => $row['lng'],
                                "username" => $row['login'],
                                "fullname" => htmlspecialchars_decode(stripslashes($row['fullname'])),
                                "location" => stripcslashes($row['country']),
                                "status" => stripcslashes($row['status']),
                                "verify" => $row['verify'],
                                "verified" => $row['verify'],
                                "fb_page" => stripcslashes($row['fb_page']),
                                "instagram_page" => stripcslashes($row['my_page']),
                                "lowPhotoUrl" => $row['lowPhotoUrl'],
                                "bigPhotoUrl" => $row['bigPhotoUrl'],
                                "normalPhotoUrl" => $row['normalPhotoUrl'],
                                "normalCoverUrl" => $row['normalCoverUrl'],
                                "originCoverUrl" => $row['originCoverUrl'],
                                "allowComments" => $row['allowComments'],
                                "allowPhotosComments" => $row['allowPhotosComments'],
                                "allowVideoComments" => $row['allowVideoComments'],
                                "allowGalleryComments" => $row['allowGalleryComments'],
                                "allowMessages" => $row['allowMessages'],
                                "referralsCount" => $row['referrals_count'],
                                "postsCount" => $row['posts_count'],
                                "followersCount" => $row['followers_count'],
                                "likesCount" => $row['likes_count'],
                                "photosCount" => $row['photos_count'],
                                "galleryItemsCount" => $row['gallery_items_count'],
                                "videosCount" => $row['videos_count'],
                                "giftsCount" => $row['gifts_count'],
                                "friendsCount" => $row['friends_count'],
                                "allowShowMyInfo" => $row['allowShowMyInfo'],
                                "allowShowMyVideos" => $row['allowShowMyVideos'],
                                "allowShowMyFriends" => $row['allowShowMyFriends'],
                                "allowShowMyPhotos" => $row['allowShowMyPhotos'],
                                "allowShowMyGallery" => $row['allowShowMyGallery'],
                                "allowShowMyGifts" => $row['allowShowMyGifts'],
                                "allowShowMyAgeAndGender" => $row['allowShowMyAgeAndGender'],
                                "createAt" => $row['regtime'],
                                "createDate" => date("Y-m-d", $row['regtime']),
                                "lastAuthorize" => $row['last_authorize'],
                                "lastAuthorizeDate" => date("Y-m-d H:i:s", $row['last_authorize']),
                                "lastAuthorizeTimeAgo" => $time->timeAgo($row['last_authorize']),
                                "online" => $online,
                                "photoModerateAt" => $row['photoModerateAt'],
                                "photoModerateUrl" => $row['photoModerateUrl'],
                                "photoPostModerateAt" => $row['photoPostModerateAt'],
                                "coverModerateAt" => $row['coverModerateAt'],
                                "coverModerateUrl" => $row['coverModerateUrl'],
                                "coverPostModerateAt" => $row['coverPostModerateAt']);
            }
        }

        return $result;
    }

    public function addFollower($follower_id, $follow_type = 1)
    {
        if ($this->is_follower_exists($follower_id)) {

            $stmt = $this->db->prepare("DELETE FROM profile_followers WHERE follower = (:follower) AND follow_to = (:follow_to)");
            $stmt->bindParam(":follower", $follower_id, PDO::PARAM_INT);
            $stmt->bindParam(":follow_to", $this->id, PDO::PARAM_INT);

            $stmt->execute();

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS,
                            "follow" => false,
                            "followersCount" => $this->getFollowersCount());

            $notify = new notify($this->db);
            $notify->removeNotify($this->id, $follower_id, NOTIFY_TYPE_FOLLOWER, 0);
            unset($notify);

        } else {

            $create_at = time();

            $stmt = $this->db->prepare("INSERT INTO profile_followers (follower, follow_to, follow_type, create_at) value (:follower, :follow_to, :follow_type, :create_at)");
            $stmt->bindParam(":follower", $follower_id, PDO::PARAM_INT);
            $stmt->bindParam(":follow_to", $this->id, PDO::PARAM_INT);
            $stmt->bindParam(":follow_type", $follow_type, PDO::PARAM_INT);
            $stmt->bindParam(":create_at", $create_at, PDO::PARAM_INT);

            $stmt->execute();

            $blacklist = new blacklist($this->db);
            $blacklist->setRequestFrom($this->id);

            if (!$blacklist->isExists($follower_id) && $follow_type == 0) {

                $notify = new notify($this->db);
                $notify->createNotify($this->id, $follower_id, NOTIFY_TYPE_FOLLOWER, 0);
                unset($notify);
            }

            unset($blacklist);

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS,
                            "follow" => true,
                            "followersCount" => $this->getFollowersCount());
        }

        $this->updateCounters();

        return $result;
    }
    public function updateCounters()
    {
        $postsCount = $this->getPostsCount();
        $followersCount = $this->getFollowersCount();

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE planes2 SET posts_count = (:posts_count), followers_count = (:followers_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":posts_count", $postsCount, PDO::PARAM_INT);
        $stmt->bindParam(":followers_count", $followersCount, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function is_follower_exists($follower_id)
    {

        $stmt = $this->db->prepare("SELECT id FROM profile_followers WHERE follower = (:follower) AND follow_to = (:follow_to) LIMIT 1");
        $stmt->bindParam(":follower", $follower_id, PDO::PARAM_INT);
        $stmt->bindParam(":follow_to", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return true;
        }

        return false;
    }


    public function is_friend_exists($friend_id)
    {

        $stmt = $this->db->prepare("SELECT id FROM friends WHERE friend = (:friend) AND friendTo = (:friendTo) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":friend", $friend_id, PDO::PARAM_INT);
        $stmt->bindParam(":friendTo", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return true;
        }

        return false;
    }

    public function reportAbuse($abuseId)
    {
        $result = array("error" => true);

        $create_at = time();
        $ip_addr = helper::ip_addr();

        $stmt = $this->db->prepare("INSERT INTO profile_abuse_reports (abuseFromUserId, abuseToUserId, abuseId, createAt, ip_addr) value (:abuseFromUserId, :abuseToUserId, :abuseId, :createAt, :ip_addr)");
        $stmt->bindParam(":abuseFromUserId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":abuseToUserId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":abuseId", $abuseId, PDO::PARAM_INT);
        $stmt->bindParam(":createAt", $create_at, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false);
        };

        return $result;
    }
    private function getMaxIdPosts()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM posts");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getPostsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM posts WHERE fromUserId = (:fromUserId) AND groupId = 0 AND removeAt = 0");
        $stmt->bindParam(":fromUserId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getFriendsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM profile_followers WHERE follower = (:followerId) AND follow_type = 0");
        $stmt->bindParam(":followerId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getMyGroupsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM profile_followers WHERE follower = (:followerId) AND follow_type = 1");
        $stmt->bindParam(":followerId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getManagedGroupsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM planes2 WHERE account_author = (:account_author) AND state = 0");
        $stmt->bindParam(":account_author", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getLikesCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM likes WHERE toUserId = (:toUserId) AND removeAt = 0");
        $stmt->bindParam(":toUserId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getFollowingsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM profile_followers WHERE follower = (:followerId) AND follow_type = 0");
        $stmt->bindParam(":followerId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getFollowersCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM profile_followers WHERE follow_to = (:follow_to)");
        $stmt->bindParam(":follow_to", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function setLastNotifyView()
    {
        $time = time();

        $stmt = $this->db->prepare("UPDATE planes2 SET last_notify_view = (:last_notify_view) WHERE id = (:id)");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":last_notify_view", $time, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getLastNotifyView()
    {
        $stmt = $this->db->prepare("SELECT last_notify_view FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['last_notify_view'];
            }
        }

        $time = time();

        return $time;
    }
    public function setBirth($year, $month, $day)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE planes2 SET bYear = (:bYear), bMonth = (:bMonth), bDay = (:bDay) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":bYear", $year, PDO::PARAM_INT);
        $stmt->bindParam(":bMonth", $month, PDO::PARAM_INT);
        $stmt->bindParam(":bDay", $day, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function setWebPage($my_page)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE planes2 SET my_page = (:my_page) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":my_page", $my_page, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getWebPage()
    {
        $stmt = $this->db->prepare("SELECT planes2 FROM planes2 WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['my_page'];
        }

        return '';
    }
    public function setEmail($email)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $helper = new helper($this->db);

        if (!helper::isCorrectEmail($email)) {

            return $result;
        }

        if ($helper->isEmailExists($email)) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE planes2 SET email = (:email) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getEmail()
    {
        $stmt = $this->db->prepare("SELECT email FROM planes2 WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['email'];
        }

        return '';
    }
    public function getPosts($itemId = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxIdPosts();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT id FROM posts WHERE groupId = (:groupId) AND removeAt = 0 AND id < (:itemId) ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':groupId', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':itemId', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $post = new post($this->db);
                $post->setRequestFrom($this->requestFrom);

                $postInfo = $post->infoplan($row['id']);

                array_push($result['items'], $postInfo);

                $result['itemId'] = $row['id'];

                unset($postInfo);
                unset($post);
            }
        }

        return $result;
    }

    public function setId($profileId)
    {
        $this->id = $profileId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }

    public function setVerify($verify)
    {
        $stmt = $this->db->prepare("UPDATE planes2 SET verify = (:verify) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":verify", $verify, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function setFullname($fullname)
    {
        if (strlen($fullname) == 0) {

            return;
        }

        $stmt = $this->db->prepare("UPDATE planes2 SET fullname = (:fullname) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function setAllowMessages($allowMessages)
    {
        $stmt = $this->db->prepare("UPDATE planes2 SET allowMessages = (:allowMessages) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowMessages", $allowMessages, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllowMessages()
    {
        $stmt = $this->db->prepare("SELECT allowMessages FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['allowMessages'];
        }

        return 0;
    }

    public function setAllowComments($allowComments)
    {
        $stmt = $this->db->prepare("UPDATE planes2 SET allowComments = (:allowComments) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowComments", $allowComments, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllowComments()
    {
        $stmt = $this->db->prepare("SELECT allowComments FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['allowComments'];
        }

        return 0;
    }

    public function setAllowPosts($allowPosts)
    {
        $stmt = $this->db->prepare("UPDATE planes2 SET allowPosts = (:allowPosts) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowPosts", $allowPosts, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllowPosts()
    {
        $stmt = $this->db->prepare("SELECT allowPosts FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['allowPosts'];
        }

        return 0;
    }

    public function setCategory($accountCategory)
    {

        $stmt = $this->db->prepare("UPDATE planes2 SET account_category = (:accountCategory) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":accountCategory", $accountCategory, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getCategory()
    {
        $stmt = $this->db->prepare("SELECT account_category FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['account_category'];
        }

        return 0;
    }

    public function setState($accountState)
    {

        $stmt = $this->db->prepare("UPDATE planes2 SET state = (:accountState) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":accountState", $accountState, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getState()
    {
        $stmt = $this->db->prepare("SELECT state FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['state'];
        }

        return 0;
    }


    public function getFullname()
    {
        $stmt = $this->db->prepare("SELECT login, fullname FROM planes2 WHERE id = (:profileId) LIMIT 1");
        $stmt->bindParam(":profileId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        $fullname = stripslashes($row['fullname']);

        if (strlen($fullname) < 1) {

            $fullname = $row['login'];
        }

        return $fullname;
    }

        public function setUsername($username)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $helper = new helper($this->db);

        if (!helper::isCorrectLogin($username)) {

            return $result;
        }

        if ($helper->isLoginExists($username)) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE planes2 SET login = (:login) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":login", $username, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getUsername()
    {
        $stmt = $this->db->prepare("SELECT login FROM planes2 WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['login'];
        }

        return '';
    }
    public function setLocation($location)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE planes2 SET country = (:country) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":country", $location, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getLocation()
    {
        $stmt = $this->db->prepare("SELECT country FROM planes2 WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['country'];
        }

        return '';
    }

    public function setStatus($status)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE planes2 SET status = (:status) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getStatus()
    {
        $stmt = $this->db->prepare("SELECT status FROM planes2 WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['status'];
        }

        return '';
    }
    public function deactivation($password)
    {

        $result = array('error' => true,
                        'error_code' => ERROR_UNKNOWN);

        if (!helper::isCorrectPassword($password)) {

            return $result;
        }

        $stmt = $this->db->prepare("SELECT salt FROM planes2 WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            $passw_hash = md5(md5($password) . $row['salt']);

            $stmt2 = $this->db->prepare("SELECT id FROM planes2 WHERE id = (:accountId) AND passw = (:password) LIMIT 1");
            $stmt2->bindParam(":accountId", $this->id, PDO::PARAM_INT);
            $stmt2->bindParam(":password", $passw_hash, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {

                $this->setState(ACCOUNT_STATE_DISABLED);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS);
            }
        }

        return $result;
    }

    public function setLanguage($language)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE planes2 SET language = (:language) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":language", $language, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getLanguage()
    {
        $stmt = $this->db->prepare("SELECT language FROM planes2 WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['language'];
        }

        return 'en';
    }

    public function getAnonymousQuestions()
    {
        $stmt = $this->db->prepare("SELECT anonymousQuestions FROM planes2 WHERE id = (:profileId) LIMIT 1");
        $stmt->bindParam(":profileId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['anonymousQuestions'];
    }

    private function getMaxIdFollowers()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM profile_followers");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getFollowings($itemId = 0) {

        return $this->getFriends($itemId);
    }

    public function getFriends($id = 0)
    {
        if ($id == 0) {

            $id = $this->getMaxIdFollowers();
            $id++;
        }

        $friends = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "id" => $id,
                        "friends" => array());

        $stmt = $this->db->prepare("SELECT * FROM profile_followers WHERE follower = (:follower) AND id < (:id) AND follow_type = 0 ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':follower', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['follow_to']);
                    $profile->setRequestFrom($this->requestFrom);

                    array_push($friends['friends'], $profile->get());

                    $friends['id'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $friends;
    }

    public function getFollowers($itemId = 0, $limit = 20)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxIdFollowers();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT * FROM profile_followers WHERE follow_to = (:follow_to) AND id < (:id) ORDER BY id DESC LIMIT :limit");
        $stmt->bindParam(':follow_to', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['follower']);
                    $profile->setRequestFrom($this->requestFrom);

                    array_push($result['items'], $profile->get());

                    $result['itemId'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $result;
    }
    public function edit($fullname)
    {
        $result = array("error" => true);

        $stmt = $this->db->prepare("UPDATE planes2 SET fullname = (:fullname) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false);
        }

        return $result;
    }

    public function setPhoto($array_data)
    {
        $stmt = $this->db->prepare("UPDATE planes2 SET originPhotoUrl = (:originPhotoUrl), normalPhotoUrl = (:normalPhotoUrl), bigPhotoUrl = (:bigPhotoUrl), lowPhotoUrl = (:lowPhotoUrl) WHERE id = (:account_id)");
        $stmt->bindParam(":account_id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":originPhotoUrl", $array_data['originPhotoUrl'], PDO::PARAM_STR);
        $stmt->bindParam(":normalPhotoUrl", $array_data['normalPhotoUrl'], PDO::PARAM_STR);
        $stmt->bindParam(":bigPhotoUrl", $array_data['bigPhotoUrl'], PDO::PARAM_STR);
        $stmt->bindParam(":lowPhotoUrl", $array_data['lowPhotoUrl'], PDO::PARAM_STR);

        $stmt->execute();
    }

    public function getAccessLevel($user_id)
    {
        $stmt = $this->db->prepare("SELECT access_level FROM planes2 WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['access_level'];
            }
        }

        return 0;
    }

    public function setAccessLevel($access_level)
    {
        $stmt = $this->db->prepare("UPDATE planes2 SET access_level = (:access_level) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":access_level", $access_level, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function getMyGroups($itemId = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->getMaxIdFollowers();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT * FROM profile_followers WHERE follower = (:follower) AND id < (:id) AND follow_type = 1 ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':follower', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $group = new group($this->db, $row['follow_to']);
                    $group->setRequestFrom($this->requestFrom);

                    array_push($result['items'], $group->get());

                    $result['itemId'] = $row['id'];

                    unset($group);
                }
            }
        }

        return $result;
    }

    public function getManagedGroups($itemId = 0)
    {
        if ($itemId == 0) {

            $itemId = $this->lastIndex();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemId" => $itemId,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT id FROM planes2 WHERE account_author = (:account_author) AND id < (:id) ORDER BY id DESC LIMIT 20");
        $stmt->bindParam(':account_author', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $group = new group($this->db, $row['id']);
                    $group->setRequestFrom($this->requestFrom);

                    array_push($result['items'], $group->get());

                    $result['itemId'] = $row['id'];

                    unset($group);
                }
            }
        }

        return $result;
    }
}

