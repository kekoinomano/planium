package com.app.planium.model;

import android.os.Parcel;
import android.os.Parcelable;
import android.util.Log;

import com.app.planium.constants.Constants;

import org.json.JSONObject;


public class Plan implements Constants, Parcelable {

    private long id, authorId;

    private int mood, state, sex, year, month, day, verify, category, allowPosts, pro, itemsCount, galleryItemsCount, likesCount, giftsCount, friendsCount, followingsCount, followersCount, allowComments, allowMessages, lastAuthorize, accountType;

    private int allowShowMyInfo, allowShowMyFriends, allowShowMyGallery, allowShowMyGifts;

    private int allowGalleryComments;

    private double distance = 0;
    private double lat, lng;

    private String username, fullname, webPage, lowPhotoUrl, bigPhotoUrl, normalPhotoUrl, normalCoverUrl, location, facebookPage, instagramPage, bio, lastAuthorizeDate, lastAuthorizeTimeAgo;

    private Boolean blocked = false;

    private Boolean inBlackList = false;

    private Boolean follower = false;

    private Boolean follow = false;

    private Boolean friend = false;

    private Boolean online = false;

    public Plan() {


    }

    public Plan(JSONObject jsonData) {

        try {

            if (!jsonData.getBoolean("error")) {

                this.setId(jsonData.getLong("id"));
                this.setAuthorId(jsonData.getLong("accountAuthor"));
                this.setCategory(jsonData.getInt("accountCategory"));
                this.setDistance(jsonData.getDouble("distance"));
                this.setLat(jsonData.getDouble("lat"));
                this.setLng(jsonData.getDouble("lng"));
                this.setState(jsonData.getInt("state"));
                this.setType(jsonData.getInt("accountType"));
                this.setSex(jsonData.getInt("sex"));
                this.setYear(jsonData.getInt("year"));
                this.setMonth(jsonData.getInt("month"));
                this.setDay(jsonData.getInt("day"));
                this.setUsername(jsonData.getString("username"));
                this.setFullname(jsonData.getString("fullname"));
                this.setLocation(jsonData.getString("location"));
                this.setWebPage(jsonData.getString("my_page"));
                this.setFacebookPage(jsonData.getString("fb_page"));
                this.setInstagramPage(jsonData.getString("instagram_page"));
                this.setBio(jsonData.getString("status"));
                this.setVerify(jsonData.getInt("verified"));

                this.setMood(jsonData.getInt("mood"));

                this.setLowPhotoUrl(jsonData.getString("lowPhotoUrl"));
                this.setNormalPhotoUrl(jsonData.getString("normalPhotoUrl"));
                this.setBigPhotoUrl(jsonData.getString("bigPhotoUrl"));

                this.setNormalCoverUrl(jsonData.getString("normalCoverUrl"));

                this.setFollowersCount(jsonData.getInt("followersCount"));
                this.setFollowingsCount(jsonData.getInt("friendsCount"));
                this.setFriendsCount(jsonData.getInt("friendsCount"));
                this.setItemsCount(jsonData.getInt("postsCount"));
                this.setLikesCount(jsonData.getInt("likesCount"));
                this.setGalleryItemsCount(jsonData.getInt("galleryItemsCount"));
                this.setGiftsCount(jsonData.getInt("giftsCount"));

                this.setAllowShowMyInfo(jsonData.getInt("allowShowMyInfo"));
                this.setAllowShowMyFriends(jsonData.getInt("allowShowMyFriends"));
                this.setAllowShowMyGallery(jsonData.getInt("allowShowMyGallery"));
                this.setAllowShowMyGifts(jsonData.getInt("allowShowMyGifts"));

                this.setAllowComments(jsonData.getInt("allowComments"));
                this.setAllowPosts(jsonData.getInt("allowPosts"));
                this.setAllowMessages(jsonData.getInt("allowMessages"));

                if (jsonData.has("inBlackList")) {

                    this.setInBlackList(jsonData.getBoolean("inBlackList"));
                }

                if (jsonData.has("follower")) {

                    this.setFollower(jsonData.getBoolean("follower"));
                }

                if (jsonData.has("follow")) {

                    this.setFollow(jsonData.getBoolean("follow"));
                }

                if (jsonData.has("friend")) {

                    this.setFriend(jsonData.getBoolean("friend"));
                }

                if (jsonData.has("online")) {

                    this.setOnline(jsonData.getBoolean("online"));
                }

                if (jsonData.has("blocked")) {

                    this.setBlocked(jsonData.getBoolean("blocked"));
                }

                this.setLastActive(jsonData.getInt("lastAuthorize"));
                this.setLastActiveDate(jsonData.getString("lastAuthorizeDate"));
                this.setLastActiveTimeAgo(jsonData.getString("lastAuthorizeTimeAgo"));

                if (jsonData.has("allowGalleryComments")) {

                    this.setAllowGalleryComments(jsonData.getInt("allowGalleryComments"));
                }

                if (jsonData.has("distance")) {

                    this.setDistance(jsonData.getDouble("distance"));
                }

                if (jsonData.has("pro")) {

                    this.setProMode(jsonData.getInt("pro"));
                }
            }

        } catch (Throwable t) {

            Log.e("Plan", "Could not parse malformed JSON: \"" + jsonData.toString() + "\"");

        } finally {

            Log.d("Plan", jsonData.toString());
        }
    }

    public void setDistance(double distance) {

        this.distance = distance;
    }

    public double getDistance() {

        return this.distance;
    }
    public void setAuthorId(long authorId) {

        this.authorId = authorId;
    }

    public long getAuthorId() {

        return this.authorId;
    }
    public void setCategory(int category) {

        this.category = category;
    }

    public int getCategory() {

        return this.category;
    }
    public void setLat(double lat) {

        this.lat = lat;
    }

    public double getLat() {

        return this.lat;
    }
    public void setLng(double lng) {

        this.lng = lng;
    }

    public double getLng() {

        return this.lng;
    }

    public void setId(long profile_id) {

        this.id = profile_id;
    }

    public long getId() {

        return this.id;
    }

    public void setState(int profileState) {

        this.state = profileState;
    }

    public int getState() {

        return this.state;
    }

    public void setType(int accountType) {

        this.accountType = accountType;
    }

    public int getType() {

        return this.accountType;
    }

    public void setSex(int sex) {

        this.sex = sex;
    }

    public int getSex() {

        return this.sex;
    }

    public void setYear(int year) {

        this.year = year;
    }

    public int getYear() {

        return this.year;
    }

    public void setMonth(int month) {

        this.month = month;
    }

    public int getMonth() {

        return this.month;
    }

    public void setDay(int day) {

        this.day = day;
    }

    public int getDay() {

        return this.day;
    }

    public void setProMode(int proMode) {

        this.pro = proMode;
    }

    public int getProMode() {

        return this.pro;
    }

    public Boolean isProMode() {

        if (this.pro > 0) {

            return true;
        }

        return false;
    }

    public void setVerify(int profileVerify) {

        this.verify = profileVerify;
    }

    public int getVerify() {

        return this.verify;
    }

    public Boolean isVerify() {

        if (this.verify > 0) {

            return true;
        }

        return false;
    }

    public void setUsername(String profile_username) {

        this.username = profile_username;
    }

    public String getUsername() {

        return this.username;
    }

    public void setFullname(String profile_fullname) {

        this.fullname = profile_fullname;
    }

    public String getFullname() {

        if (fullname == null) {

            fullname = "";
        }

        return this.fullname;
    }

    public void setLocation(String location) {

        this.location = location;
    }

    public String getLocation() {

        if (this.location == null) {

            this.location = "";
        }

        return this.location;
    }
    public void setWebPage(String webPage) {

        this.webPage = webPage;
    }

    public String getWebPage() {

        if (this.webPage == null) {

            this.webPage = "";
        }

        return this.webPage;
    }

    public void setFacebookPage(String facebookPage) {

        this.facebookPage = facebookPage;
    }

    public String getFacebookPage() {

        return this.facebookPage;
    }

    public void setInstagramPage(String instagramPage) {

        this.instagramPage = instagramPage;
    }

    public String getInstagramPage() {

        return this.instagramPage;
    }

    public void setBio(String bio) {

        this.bio = bio;
    }

    public String getBio() {

        if (this.bio == null) {

            this.bio = "";
        }

        return this.bio;
    }

    public void setLowPhotoUrl(String lowPhotoUrl) {

        this.lowPhotoUrl = lowPhotoUrl;
    }

    public String getLowPhotoUrl() {

        if (this.lowPhotoUrl == null) {

            this.lowPhotoUrl = "";
        }

        return this.lowPhotoUrl;
    }

    public void setBigPhotoUrl(String bigPhotoUrl) {

        this.bigPhotoUrl = bigPhotoUrl;
    }

    public String getBigPhotoUrl() {

        return this.bigPhotoUrl;
    }

    public void setNormalPhotoUrl(String normalPhotoUrl) {

        this.normalPhotoUrl = normalPhotoUrl;
    }

    public String getNormalPhotoUrl() {

        if (this.normalPhotoUrl == null) {

            this.normalPhotoUrl = "";
        }

        return this.normalPhotoUrl;
    }

    public void setNormalCoverUrl(String normalCoverUrl) {

        this.normalCoverUrl = normalCoverUrl;
    }

    public String getNormalCoverUrl() {

        return this.normalCoverUrl;
    }

    public void setFollowersCount(int followersCount) {

        this.followersCount = followersCount;
    }

    public int getFollowersCount() {

        return this.followersCount;
    }

    public void setItemsCount(int itemsCount) {

        this.itemsCount = itemsCount;
    }

    public int getItemsCount() {

        return this.itemsCount;
    }

    public void setGalleryItemsCount(int galleryItemsCount) {

        this.galleryItemsCount = galleryItemsCount;
    }

    public int getGalleryItemsCount() {

        return this.galleryItemsCount;
    }

    public void setLikesCount(int likesCount) {

        this.likesCount = likesCount;
    }

    public int getLikesCount() {

        return this.likesCount;
    }

    public void setGiftsCount(int giftsCount) {

        this.giftsCount = giftsCount;
    }

    public int getGiftsCount() {

        return this.giftsCount;
    }

    public void setFollowingsCount(int followingsCount) {

        this.followingsCount = followingsCount;
    }

    public int getFollowingsCount() {

        return this.followingsCount;
    }

    public void setFriendsCount(int friendsCount) {

        this.friendsCount = friendsCount;
    }

    public int getFriendsCount() {

        return this.friendsCount;
    }

    public void setAllowComments(int allowComments) {

        this.allowComments = allowComments;
    }

    public int getAllowComments() {

        return this.allowComments;
    }
    public void setAllowPosts(int allowPosts) {

        this.allowPosts = allowPosts;
    }

    public int getAllowPosts() {

        return this.allowPosts;
    }

    public void setAllowMessages(int allowMessages) {

        this.allowMessages = allowMessages;
    }

    public int getAllowMessages() {

        return this.allowMessages;
    }

    public void setMood(int mood) {

        this.mood = mood;
    }

    public int getMood() {

        return this.mood;
    }

    public void setLastActive(int lastAuthorize) {

        this.lastAuthorize = lastAuthorize;
    }

    public int getLastActive() {

        return this.lastAuthorize;
    }

    public void setLastActiveDate(String lastAuthorizeDate) {

        this.lastAuthorizeDate = lastAuthorizeDate;
    }

    public String getLastActiveDate() {

        return this.lastAuthorizeDate;
    }

    public void setLastActiveTimeAgo(String lastAuthorizeTimeAgo) {

        this.lastAuthorizeTimeAgo = lastAuthorizeTimeAgo;
    }

    public String getLastActiveTimeAgo() {

        return this.lastAuthorizeTimeAgo;
    }

    public void setBlocked(Boolean blocked) {

        this.blocked = blocked;
    }

    public Boolean isBlocked() {

        return this.blocked;
    }

    public void setFollower(Boolean follower) {

        this.follower = follower;
    }

    public Boolean isFollower() {

        return this.follower;
    }

    public void setFollow(Boolean follow) {

        this.follow = follow;
    }

    public Boolean isFollow() {

        return this.follow;
    }

    public void setFriend(Boolean friend) {

        this.friend = friend;
    }

    public Boolean isFriend() {

        return this.friend;
    }

    public void setOnline(Boolean online) {

        this.online = online;
    }

    public Boolean isOnline() {

        return this.online;
    }

    public void setInBlackList(Boolean inBlackList) {

        this.inBlackList = inBlackList;
    }

    public Boolean isInBlackList() {

        return this.inBlackList;
    }

    // Privacy

    public void setAllowShowMyInfo(int allowShowMyInfo) {

        this.allowShowMyInfo = allowShowMyInfo;
    }

    public int getAllowShowMyInfo() {

        return this.allowShowMyInfo;
    }

    public void setAllowShowMyFriends(int allowShowMyFriends) {

        this.allowShowMyFriends = allowShowMyFriends;
    }

    public int getAllowShowMyFriends() {

        return this.allowShowMyFriends;
    }

    public void setAllowShowMyGallery(int allowShowMyGallery) {

        this.allowShowMyGallery = allowShowMyGallery;
    }

    public int getAllowShowMyGallery() {

        return this.allowShowMyGallery;
    }

    public void setAllowShowMyGifts(int allowShowMyGifts) {

        this.allowShowMyGifts = allowShowMyGifts;
    }

    public int getAllowShowMyGifts() {

        return this.allowShowMyGifts;
    }

    public int getAllowGalleryComments() {

        return this.allowGalleryComments;
    }

    public void setAllowGalleryComments(int allowGalleryComments) {

        this.allowGalleryComments = allowGalleryComments;
    }

    @Override
    public int describeContents() {
        return 0;
    }

    @Override
    public void writeToParcel(Parcel dest, int flags) {
        dest.writeLong(this.id);
        dest.writeLong(this.authorId);
        dest.writeInt(this.mood);
        dest.writeInt(this.state);
        dest.writeInt(this.sex);
        dest.writeInt(this.year);
        dest.writeInt(this.month);
        dest.writeInt(this.day);
        dest.writeInt(this.verify);
        dest.writeInt(this.pro);
        dest.writeInt(this.itemsCount);
        dest.writeInt(this.galleryItemsCount);
        dest.writeInt(this.likesCount);
        dest.writeInt(this.giftsCount);
        dest.writeInt(this.friendsCount);
        dest.writeInt(this.followingsCount);
        dest.writeInt(this.followersCount);
        dest.writeInt(this.allowComments);
        dest.writeInt(this.allowPosts);
        dest.writeInt(this.category);
        dest.writeInt(this.allowMessages);
        dest.writeInt(this.lastAuthorize);
        dest.writeInt(this.accountType);
        dest.writeInt(this.allowShowMyInfo);
        dest.writeInt(this.allowShowMyFriends);
        dest.writeInt(this.allowShowMyGallery);
        dest.writeInt(this.allowShowMyGifts);
        dest.writeDouble(this.distance);
        dest.writeDouble(this.lat);
        dest.writeDouble(this.lng);
        dest.writeString(this.username);
        dest.writeString(this.fullname);
        dest.writeString(this.lowPhotoUrl);
        dest.writeString(this.bigPhotoUrl);
        dest.writeString(this.normalPhotoUrl);
        dest.writeString(this.normalCoverUrl);
        dest.writeString(this.location);
        dest.writeString(this.webPage);
        dest.writeString(this.facebookPage);
        dest.writeString(this.instagramPage);
        dest.writeString(this.bio);
        dest.writeString(this.lastAuthorizeDate);
        dest.writeString(this.lastAuthorizeTimeAgo);
        dest.writeValue(this.blocked);
        dest.writeValue(this.inBlackList);
        dest.writeValue(this.follower);
        dest.writeValue(this.follow);
        dest.writeValue(this.friend);
        dest.writeValue(this.online);
        dest.writeValue(this.allowGalleryComments);
    }

    protected Plan(Parcel in) {
        this.id = in.readLong();
        this.authorId = in.readLong();
        this.mood = in.readInt();
        this.state = in.readInt();
        this.sex = in.readInt();
        this.year = in.readInt();
        this.month = in.readInt();
        this.day = in.readInt();
        this.verify = in.readInt();
        this.pro = in.readInt();
        this.itemsCount = in.readInt();
        this.galleryItemsCount = in.readInt();
        this.likesCount = in.readInt();
        this.giftsCount = in.readInt();
        this.friendsCount = in.readInt();
        this.followingsCount = in.readInt();
        this.followersCount = in.readInt();
        this.allowComments = in.readInt();
        this.allowPosts = in.readInt();
        this.category = in.readInt();
        this.allowMessages = in.readInt();
        this.lastAuthorize = in.readInt();
        this.accountType = in.readInt();
        this.allowShowMyInfo = in.readInt();
        this.allowShowMyFriends = in.readInt();
        this.allowShowMyGallery = in.readInt();
        this.allowShowMyGifts = in.readInt();
        this.distance = in.readDouble();
        this.lat = in.readDouble();
        this.lng = in.readDouble();
        this.username = in.readString();
        this.fullname = in.readString();
        this.lowPhotoUrl = in.readString();
        this.bigPhotoUrl = in.readString();
        this.normalPhotoUrl = in.readString();
        this.normalCoverUrl = in.readString();
        this.location = in.readString();
        this.webPage = in.readString();
        this.facebookPage = in.readString();
        this.instagramPage = in.readString();
        this.bio = in.readString();
        this.lastAuthorizeDate = in.readString();
        this.lastAuthorizeTimeAgo = in.readString();
        this.blocked = (Boolean) in.readValue(Boolean.class.getClassLoader());
        this.inBlackList = (Boolean) in.readValue(Boolean.class.getClassLoader());
        this.follower = (Boolean) in.readValue(Boolean.class.getClassLoader());
        this.follow = (Boolean) in.readValue(Boolean.class.getClassLoader());
        this.friend = (Boolean) in.readValue(Boolean.class.getClassLoader());
        this.online = (Boolean) in.readValue(Boolean.class.getClassLoader());
        this.allowGalleryComments = in.readInt();
    }

    public static final Creator<Plan> CREATOR = new Creator<Plan>() {
        @Override
        public Plan createFromParcel(Parcel source) {
            return new Plan(source);
        }

        @Override
        public Plan[] newArray(int size) {
            return new Plan[size];
        }
    };
}
