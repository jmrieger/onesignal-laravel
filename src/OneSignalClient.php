<?php

namespace Jmrieger\OneSignal;

use GuzzleHttp\Client;
use Exception;

class OneSignalClient
{
    const API_URL = "https://onesignal.com/api/v1";
    private $client;

    private $appId;
    private $restApiKey;
    private $userAuthKey;

    public function __construct($appId, $restApiKey, $userAuthKey)
    {
        $this->appId = $appId;
        $this->restApiKey = $restApiKey;
        $this->userAuthKey = $userAuthKey;

        $this->client = new Client();
        //$this->headers = ['headers' => []];
    }

    private function requiresAuth( &$headers = [] ) {
        $headers['headers']['Authorization'] = 'Basic '.$this->restApiKey;
        return $headers;
    }

    private function requiresUserAuth( &$headers = [] ) {
        $headers['headers']['Authorization'] = 'Basic '.$this->userAuthKey;
    }

    private function usesJSON( &$headers = [] ) {
        $headers['headers']['Content-Type'] = 'application/json';
        return $headers;
    }


    /*
     * OneSignal Notification related functions
     */
    public function getNotifications( $app_id, $limit = 50, $offset = 0 )
    {
        $headers = $this->headerInit( false, true );
        $data = [ "app_id" => $app_id ];
        if ( $limit ) {
            $data['limit'] = $limit;
        }
        if ( $offset ) {
            $data['offset'] = $offset;
        }

        $headers['query'] = $data;

        return $this->get( "notifications", $headers );
    }

    public function getNotification( $id, $app_id )
    {
        $headers = $this->headersInit( false, true );
        $headers['query'] = [ "id" => $id, "app_id" => $app_id ];
        return $this->get("notifications", $headers );
    }

    public function postNotification( $data = [] )
    {
        $valid_params = [
            "app_id"                     => true,
            "contents"                   => true,
            "headings"                   => false,
            "isIos"                      => false,
            "isAndroid"                  => false,
            "isWP"                       => false,
            "isWP_WNS"                   => false,
            "isAdm"                      => false,
            "isChrome"                   => false,
            "isChromeWeb"                => false,
            "isSafari"                   => false,
            "isAnyWeb"                   => false,
            "included_segments"          => false,
            "excluded_segments"          => false,
            "include_player_ids"         => false,
            "include_ios_tokens"         => false,
            "include_android_reg_ids"    => false,
            "include_wp_uris"            => false,
            "include_wp_wns_uris"        => false,
            "include_amazon_reg_ids"     => false,
            "include_chrome_reg_ids"     => false,
            "include_chrome_web_reg_ids" => false,
            "app_ids"                    => false,
            "tags"                       => false,
            "ios_badgeType"              => false,
            "ios_badgeCount"             => false,
            "ios_sound"                  => false,
            "android_sound"              => false,
            "adm_sound"                  => false,
            "wp_sound"                   => false,
            "wp_wns_sound"               => false,
            "data"                       => false,
            "buttons"                    => false,
            "small_icon"                 => false,
            "large_icon"                 => false,
            "big_picture"                => false,
            "adm_small_icon"             => false,
            "adm_large_icon"             => false,
            "adm_big_picture"            => false,
            "chrome_icon"                => false,
            "chrome_big_picture"         => false,
            "chrome_web_icon"            => false,
            "firefox_icon"               => false,
            "url"                        => false,
            "send_after"                 => false,
            "delayed_option"             => false,
            "delivery_time_of_day"       => false,
            "android_led_color"          => false,
            "android_accent_color"       => false,
            "android_visibility"         => false,
            "content_available"          => false,
            "android_background_data"    => false,
            "amazon_background_data"     => false,
            "template_id"                => false,
            "android_group"              => false,
            "android_group_message"      => false,
            "adm_group"                  => false,
            "adm_group_message"          => false,
            "ttl"                        => false,
            "priority"                   => false,

        ];
        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {
            if ( $required && !array_key_exists($param, $data) ) {
                if ( $param == 'app_id' && $this->appId ) {
                    $data[ $param ] = $this->appId;
                } else {
                    throw new OneSignalException("Param $param not present");
                }
            }
            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit( false, true );
        $headers['json'] = $clean_data;

        return $this->post( "/notifications", $headers );
    }

    public function putNotificationTrackOpen( $id, $app_id, $opened = true ) {
        $headers = $this->headerInit();
        $headers['app_id'] = $app_id;
        $headers['opened'] = $opened;

        return $this->put("notifications/${id}", $headers );
    }

    public function deleteNotification( $id, $app_id )
    {
        $headers = $this->headerInit( false, true );
        return $this->delete( "notifications/${id}?app_id=${app_id}", $headers );
    }

    /*
     * OneSignal Player related functions
     */
    public function getPlayers( $app_id, $limit = 300, $offset = 0 )
    {
        $headers = $this->headerInit(false, true);
        $data = [ "app_id" => $app_id ];
        if ( $limit ) {
            $data['limit'] = $limit;
        }
        if ( $offset ) {
            $data['offset'] = $offset;
        }

        $headers['query'] = $data;

        return $this->get( "players", $headers );
    }

    public function getPlayer( $id ) {
        $headers = $this->headerInit();
        return $this->get( "players/${id}", $headers );
    }

    public function postPlayer( $data = [] )
    {
        $valid_params = [
            "app_id"                    => true,
            "device_type"               => true,
            "identifier"                => false,
            "language"                  => false,
            "timezone"                  => false,
            "game_version"              => false,
            "device_model"              => false,
            "device_os"                 => false,
            "ad_id"                     => false,
            "sdk"                       => false,
            "session_count"             => false,
            "tags"                      => false,
            "amount_spent"              => false,
            "created_at"                => false,
            "playtime"                  => false,
            "badge_count"               => false,
            "last_active"               => false,
            "test_type"                 => false,

        ];
        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {
            if ( $required && !array_key_exists($param, $data) ) {
                if ( $param == 'app_id' && $this->appId ) {
                    $data[ $param ] = $this->appId;
                } else {
                    throw new OneSignalException("Param $param not present");
                }
            }

            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers['data-binary'] = $clean_data;

        return $this->post( "/players", $headers );
    }

    public function putPlayer ( $data = [] )
    {
        $valid_params = [
            "id"                        => true,
            "app_id"                    => true,
            "identifier"                => false,
            "language"                  => false,
            "timezone"                  => false,
            "device_model"              => false,
            "device_os"                 => false,
            "game_version"              => false,
            "ad_id"                     => false,
            "session_count"             => false,
            "tags"                      => false,
            "amount_spent"              => false,
            "created_at"                => false,
            "last_active"               => false,
            "badge_count"               => false,
            "playtime"                  => false,
            "sdk"                       => false,
            "notification_types"        => false,
            "test_type"                 => false,
            "long"                      => false,
            "lat"                       => false,
        ];
        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {
            if ( $required && !array_key_exists($param, $data) ) {
                if ( $param == 'app_id' && $this->appId ) {
                    $data[ $param ] = $this->appId;
                } else {
                    throw new OneSignalException("Param $param not present");
                }
            }

            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers['data-binary'] = $clean_data;

        return $this->post( "/players", $headers );
    }

    public function postCSVExport ( $app_id )
    {
        $headers = $this->headerInit( false, true );
        $headers['app_id'] = $app_id;
        return $this->post( "players/csv_export");
    }

    public function postPlayerOnSession( $data = [] )
    {
        $valid_params = [
            "id"            => true,
            "identifier"    => false,
            "language"      => false,
            "timezone"      => false,
            "game_version"  => false,
            "device_os"     => false,
            "ad_id"         => false,
            "sdk"           => false,
            "tags"          => false,
        ];
        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {
            if ( $required && !array_key_exists($param, $data) ) {
                throw new OneSignalException("Param $param not present");
            }

            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers['data-binary'] = $clean_data;

        return $this->post( "/players/" . $clean_data['id'] . "/on_session", $headers );
    }

    public function postPlayerOnPurchase( $data = [] )
    {
        $valid_params = [
            "id"            =>  true,
            "purchases"     =>  [
                "sku"       =>  true,
                "amount"    =>  true,
                "iso"       =>  true,
            ],
            "existing"      =>  false,

        ];

        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {

            if ($required && !array_key_exists($param, $data) ){
                throw new OneSignalException("Param $param not present");
            }
            if ( is_array( $required ) ) {
                foreach ($required AS $param2 => $required2) {
                    if ($required2 && !array_key_exists($param2, $data[$param] ) ) {
                        throw new OneSignalException("Param $param2 not present in $param data");
                    }
                }
            }

            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers['data-binary'] = $clean_data;

        return $this->post( "/players/" . $clean_data['id'] . "/on_purchase", $headers );
    }

    public function postPlayerOnFocus ( $data = [] )
    {
        $valid_params = [
            "id"                => true,
            "state"             => true,
            "active_time"       => true,
        ];
        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {
            if ( $required && !array_key_exists($param, $data) ) {
                throw new OneSignalException("Param $param not present");
            }

            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers['data-binary'] = $clean_data;

        return $this->post( "/players/" . $clean_data['id'] . "/on_focus", $headers );
    }

    /*
     * OneSignal App related functions
     */

    public function getApps() {
        return $this->get( "apps", $this->headerInit( true ) );
    }

    public function getApp( $id ) {
        return $this->get( "apps/${id}", $this->headerInit( true ) );
    }

    public function postApp( $data = [] )
    {
        $valid_params = [
            "name"                                    =>  true,
            "apns_env"                                =>  false,
            "apns_p12"                                =>  false,
            "apns_p12_password"                       =>  false,
            "gcm_key"                                 =>  false,
            "chrome_key"                              =>  false,
            "safari_apns_p12"                         =>  false,
            "safari_apns_p12_password"                =>  false,
            "chrome_web_key"                          =>  false,
            "site_name"                               =>  false,
            "safari_site_origin"                      =>  false,
            "safari_icon_16_16"                       =>  false,
            "safari_icon_32_32"                       =>  false,
            "safari_icon_64_64"                       =>  false,
            "safari_icon_128_128"                     =>  false,
            "safari_icon_256_256"                     =>  false,
            "chrome_web_gcm_sender_id"                =>  false,
            "chrome_web_default_notification_icon"    =>  false,
            "chrome_web_sub_domain"                   =>  false,
        ];

        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {
            if ( $required && !array_key_exists($param, $data) ) {
                throw new OneSignalException("Param $param not present");
            }

            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit(true);
        $headers['data-binary'] = $clean_data;

        return $this->post( "/apps", $headers );
    }

    public function putApp( $id, $data = []  )
    {
        $valid_params = [
            "id"                                      =>  true,
            "name"                                    =>  true,
            "apns_env"                                =>  false,
            "apns_p12"                                =>  false,
            "apns_p12_password"                       =>  false,
            "gcm_key"                                 =>  false,
            "chrome_key"                              =>  false,
            "safari_apns_p12"                         =>  false,
            "safari_apns_p12_password"                =>  false,
            "chrome_web_key"                          =>  false,
            "site_name"                               =>  false,
            "safari_site_origin"                      =>  false,
            "safari_icon_16_16"                       =>  false,
            "safari_icon_32_32"                       =>  false,
            "safari_icon_64_64"                       =>  false,
            "safari_icon_128_128"                     =>  false,
            "safari_icon_256_256"                     =>  false,
            "chrome_web_gcm_sender_id"                =>  false,
            "chrome_web_default_notification_icon"    =>  false,
            "chrome_web_sub_domain"                   =>  false,
        ];
        $data['id'] = $id;

        $clean_data = [];

        foreach ( $valid_params AS $param => $required )
        {
            if ( $required && !array_key_exists($param, $data) ) {
                throw new OneSignalException("Param $param not present");
            }

            if ( array_key_exists($param, $data ) ) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit(true);
        $headers['data-binary'] = $clean_data;

        return $this->post( "/apps", $headers );
    }


    /**
     * Generate an appropriate associative array of headers to send to the OneSignal API
     * @param bool|false $userAuthKey Require the OneSignal User Auth Key
     * @param bool|false $appAuthKey Require the OneSignal Application Auth Key. Ignored if $userAuthKey is true.
     * @param bool|true $json Set the Content-type of the request to JSON
     * @return array OneSignal friendly associative array of headers
     */
    public function headerInit( $userAuthKey = false, $appAuthKey = false, $json = true ) {
        $headers = [];
        if ( $userAuthKey ) {
            $this->requiresUserAuth( $headers );
        } else if ( $appAuthKey ) {
            $this->requiresAuth( $headers );
        }
        if ( $json ) {
            $this->usesJSON($headers);
        }

        return $headers;
    }

    public function post($endPoint, $headers = [] ) {
        $response =  $this->client->post(self::API_URL."/".$endPoint, $headers);
        return $response;
    }

    public function get($endPoint, $headers = []) {
        $response =  $this->client->get(self::API_URL."/".$endPoint, $headers);
        return $response;
    }

    public function put($endPoint, $headers = []) {
        $response = $this->client->put(self::API_URL."/".$endPoint, $headers);
        return $response;
    }

    public function delete($endPoint, $headers = []) {
        $response =  $this->client->delete(self::API_URL."/".$endPoint, $headers);
        return $response;
    }
}

class OneSignalException extends Exception
{

}