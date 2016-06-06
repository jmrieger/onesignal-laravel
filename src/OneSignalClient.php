<?php

namespace jmrieger\OneSignal;

use GuzzleHttp\Client;
use Exception;

class OneSignalClient
{
    /**
     * @const OneSignal API URI
     */
    const API_URL = "https://onesignal.com/api/v1";

    /**
     * @var \GuzzleHttp\Client GuzzleHTTP Client object
     */
    private $client;

    /**
     * @var string OneSignal App ID
     */
    private $appId = '';

    /**
     * @var string OneSignal REST API key
     */
    private $restApiKey = '';

    /**
     * @var string OneSignal User Auth key
     */
    private $userAuthKey = '';

    /**
     * OneSignalClient constructor.
     *
     * @param string $appId       Default OneSignal Application ID.  Required for all endpoints
     * @param string $restApiKey  OneSignal REST API key.  Required for /notifications and /users endpoints
     * @param string $userAuthKey OneSignal User Auth key.  Required to use the /apps endpoints
     */

    public function __construct($appId = '', $restApiKey = '', $userAuthKey = '')
    {
        $this->appId = $appId;
        $this->restApiKey = $restApiKey;
        $this->userAuthKey = $userAuthKey;

        $this->client = new Client();
    }

    /**
     * Build a headers array using the required REST API key
     *
     * @param array $headers An array of existing (or new is created) header files, for use by the GuzzleHTTP client
     * @return array $headers Appropriately modified headers array
     */
    protected function requiresAuth(&$headers = [])
    {
        $headers[ 'headers' ][ 'Authorization' ] = 'Basic ' . $this->restApiKey;

        return $headers;
    }


    /**
     * Build a headers array using the required User Auth key
     *
     * @param array $headers An array of existing (or new is created) header files, for use by the GuzzleHTTP client
     * @return array $headers Appropriately modified headers array
     */
    protected function requiresUserAuth(&$headers = [])
    {
        $headers[ 'headers' ][ 'Authorization' ] = 'Basic ' . $this->userAuthKey;

        return $headers;
    }

    /**
     * Build a headers array indicating a data type of JSON
     *
     * @param array $headers An array of existing (or new is created) header files, for use by the GuzzleHTTP client
     * @return array $headers Appropriately modified headers array
     */
    protected function usesJSON(&$headers = [])
    {
        $headers[ 'headers' ][ 'Content-Type' ] = 'application/json';

        return $headers;
    }


    /*
     * OneSignal Notification related functions
     */
    /**
     * Get all notifications for the given application ID
     *
     * @param string $app_id Application ID to get notifications from
     * @param int    $limit  Limit the total number of notifications returned.
     * @param int    $offset Starting offset for notifications returned
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function getNotifications($app_id = '', $limit = 50, $offset = 0)
    {
        $headers = $this->headerInit(false, true);

        if ($app_id == '') {
            $app_id = $this->appId;
        }
        $data = ["app_id" => $app_id];
        if ($limit) {
            $data[ 'limit' ] = $limit;
        }
        if ($offset) {
            $data[ 'offset' ] = $offset;
        }

        $headers[ 'query' ] = $data;

        return $this->get("notifications", $headers);
    }

    /**
     * Get a single Notification object for the given application ID
     *
     * @param string $id     Notification ID
     * @param string $app_id Application ID
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function getNotification($id, $app_id = '')
    {
        $headers = $this->headerInit(false, true);
        if ($app_id == '') {
            $app_id = $this->appId;
        }
        $headers[ 'query' ] = ["id" => $id, "app_id" => $app_id];

        return $this->get("notifications", $headers);
    }

    /**
     * Add a Notification to an application
     *
     * @param array  $data   Array of notification data to submit a new Notification object for
     * @param string $app_id Application ID
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function postNotification($data = [], $app_id = '')
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

        // Loop on all of the available parameters (we're sanitizing our data)
        foreach ($valid_params AS $param => $required) {
            // If we have a required parameter that's not present in the param data, this request is not valid
            if ($required && !array_key_exists($param, $data)) {
                // Ignore the app_id if it was passed
                if ($param == 'app_id' && $app_id) {
                    $data[ $param ] = $app_id;
                } else {
                    if ($param == 'app_id' && $this->appId) {
                        $data[ $param ] = $this->appId;
                    } else {
                        // Missing a required parameter; throw an exception
                        throw new OneSignalException("Param $param not present");
                    }
                }
            }
            // If the data is present in our passed data, include it in our cleaned data
            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit(false, true);
        $headers[ 'json' ] = $clean_data;

        // Return the Response from the OneSignal API
        return $this->post("/notifications", $headers);
    }

    /**
     * Specify that a Notification has been opened
     *
     * @param string    $id     Notification id
     * @param string    $app_id Application ID
     * @param bool|true $opened Was this notification opened or not
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function putNotificationTrackOpen($id, $app_id = '', $opened = true)
    {
        $headers = $this->headerInit();
        if ($app_id == '') {
            $app_id = $this->appId;
        }
        $headers[ 'app_id' ] = $app_id;
        $headers[ 'opened' ] = $opened;

        return $this->put("notifications/${id}", $headers);
    }

    /**
     * Delete a Notification from an App
     *
     * @param string $id     Notification ID
     * @param string $app_id Application ID
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function deleteNotification($id, $app_id = '')
    {
        $headers = $this->headerInit(false, true);
        if ($app_id == '') {
            $app_id = $this->appId;
        }

        return $this->delete("notifications/${id}?app_id=${app_id}", $headers);
    }

    /*
     * OneSignal Player related functions
     */
    /**
     * Get a set of Players from an App
     *
     * @param string $app_id Application ID
     * @param int    $limit
     * @param int    $offset
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function getPlayers($app_id = '', $limit = 300, $offset = 0)
    {
        $headers = $this->headerInit(false, true);
        if ($app_id == '') {
            $app_id = $this->appId;
        }
        $data = ["app_id" => $app_id];
        if ($limit) {
            $data[ 'limit' ] = $limit;
        }
        if ($offset) {
            $data[ 'offset' ] = $offset;
        }

        $headers[ 'query' ] = $data;

        return $this->get("players", $headers);
    }

    /**
     * Get a Player
     *
     * @param string $id Player ID
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function getPlayer($id)
    {
        $headers = $this->headerInit();

        return $this->get("players/${id}", $headers);
    }

    /**
     * Add a Player to an App
     *
     * @param array  $data   Data for this Player
     * @param string $app_id Application id
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function postPlayer($data = [], $app_id = '')
    {
        $valid_params = [
            "app_id"        => true,
            "device_type"   => true,
            "identifier"    => false,
            "language"      => false,
            "timezone"      => false,
            "game_version"  => false,
            "device_model"  => false,
            "device_os"     => false,
            "ad_id"         => false,
            "sdk"           => false,
            "session_count" => false,
            "tags"          => false,
            "amount_spent"  => false,
            "created_at"    => false,
            "playtime"      => false,
            "badge_count"   => false,
            "last_active"   => false,
            "test_type"     => false,

        ];
        $clean_data = [];

        foreach ($valid_params AS $param => $required) {
            if ($required && !array_key_exists($param, $data)) {
                if ($param == 'app_id' && $app_id) {
                    $data[ $param ] = $app_id;
                } else {
                    if ($param == 'app_id' && $this->appId) {
                        $data[ $param ] = $this->appId;
                    } else {
                        throw new OneSignalException("Param $param not present");
                    }
                }
            }

            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers[ 'data-binary' ] = $clean_data;

        return $this->post("/players", $headers);
    }

    /**
     * Update a Player object for the given App
     *
     * @param array  $data   Player data to update
     * @param string $app_id Application to update with
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function putPlayer($data = [], $app_id = '')
    {
        $valid_params = [
            "id"                 => true,
            "app_id"             => true,
            "identifier"         => false,
            "language"           => false,
            "timezone"           => false,
            "device_model"       => false,
            "device_os"          => false,
            "game_version"       => false,
            "ad_id"              => false,
            "session_count"      => false,
            "tags"               => false,
            "amount_spent"       => false,
            "created_at"         => false,
            "last_active"        => false,
            "badge_count"        => false,
            "playtime"           => false,
            "sdk"                => false,
            "notification_types" => false,
            "test_type"          => false,
            "long"               => false,
            "lat"                => false,
        ];
        $clean_data = [];

        foreach ($valid_params AS $param => $required) {
            if ($required && !array_key_exists($param, $data)) {

                if ($param == 'app_id' && $app_id) {
                    $data[ $param ] = $app_id;
                } else {
                    if ($param == 'app_id' && $this->appId) {
                        $data[ $param ] = $this->appId;
                    } else {
                        throw new OneSignalException("Param $param not present");
                    }
                }
            }

            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers[ 'data-binary' ] = $clean_data;

        return $this->post("/players", $headers);
    }

    /**
     * Get a CSV export of players for this App
     *
     * @param string $app_id Application ID
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function postCSVExport($app_id = '')
    {
        $headers = $this->headerInit(false, true);
        if ($app_id == '') {
            $app_id = $this->appId;
        }
        $headers[ 'app_id' ] = $app_id;

        return $this->post("players/csv_export");
    }

    /**
     * Start a new device session for this player
     *
     * @param array $data Player data
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function postPlayerOnSession($data = [])
    {
        $valid_params = [
            "id"           => true,
            "identifier"   => false,
            "language"     => false,
            "timezone"     => false,
            "game_version" => false,
            "device_os"    => false,
            "ad_id"        => false,
            "sdk"          => false,
            "tags"         => false,
        ];
        $clean_data = [];

        foreach ($valid_params AS $param => $required) {
            if ($required && !array_key_exists($param, $data)) {
                throw new OneSignalException("Param $param not present");
            }

            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers[ 'data-binary' ] = $clean_data;

        return $this->post("/players/" . $clean_data[ 'id' ] . "/on_session", $headers);
    }

    /**
     * Track a new purchase
     *
     * @param array $data Purchase data
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function postPlayerOnPurchase($data = [])
    {
        $valid_params = [
            "id"        => true,
            "purchases" => [
                "sku"    => true,
                "amount" => true,
                "iso"    => true,
            ],
            "existing"  => false,

        ];

        $clean_data = [];

        foreach ($valid_params AS $param => $required) {

            if ($required && !array_key_exists($param, $data)) {
                throw new OneSignalException("Param $param not present");
            }
            if (is_array($required)) {
                foreach ($required AS $param2 => $required2) {
                    if ($required2 && !array_key_exists($param2, $data[ $param ])) {
                        throw new OneSignalException("Param $param2 not present in $param data");
                    }
                }
            }

            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers[ 'data-binary' ] = $clean_data;

        return $this->post("/players/" . $clean_data[ 'id' ] . "/on_purchase", $headers);
    }

    /**
     * Increment the Player's total session length
     *
     * @param array $data Session data
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function postPlayerOnFocus($data = [])
    {
        $valid_params = [
            "id"          => true,
            "state"       => true,
            "active_time" => true,
        ];
        $clean_data = [];

        foreach ($valid_params AS $param => $required) {
            if ($required && !array_key_exists($param, $data)) {
                throw new OneSignalException("Param $param not present");
            }

            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit();
        $headers[ 'data-binary' ] = $clean_data;

        return $this->post("/players/" . $clean_data[ 'id' ] . "/on_focus", $headers);
    }

    /*
     * OneSignal App related functions
     */

    /**
     * Get a list of all Apps
     *
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function getApps()
    {
        return $this->get("apps", $this->headerInit(true));
    }

    /**
     * Get an App
     *
     * @param string $app_id
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    public function getApp($app_id)
    {
        return $this->get("apps/${app_id}", $this->headerInit(true));
    }

    /**
     * Add a new App
     *
     * @param array $data App data
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function postApp($data = [])
    {
        $valid_params = [
            "name"                                 => true,
            "apns_env"                             => false,
            "apns_p12"                             => false,
            "apns_p12_password"                    => false,
            "gcm_key"                              => false,
            "chrome_key"                           => false,
            "safari_apns_p12"                      => false,
            "safari_apns_p12_password"             => false,
            "chrome_web_key"                       => false,
            "site_name"                            => false,
            "safari_site_origin"                   => false,
            "safari_icon_16_16"                    => false,
            "safari_icon_32_32"                    => false,
            "safari_icon_64_64"                    => false,
            "safari_icon_128_128"                  => false,
            "safari_icon_256_256"                  => false,
            "chrome_web_gcm_sender_id"             => false,
            "chrome_web_default_notification_icon" => false,
            "chrome_web_sub_domain"                => false,
        ];

        $clean_data = [];

        foreach ($valid_params AS $param => $required) {
            if ($required && !array_key_exists($param, $data)) {
                throw new OneSignalException("Param $param not present");
            }

            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit(true);
        $headers[ 'data-binary' ] = $clean_data;

        return $this->post("/apps", $headers);
    }

    /**
     * Update an App
     *
     * @param string $app_id App id
     * @param array  $data   App data
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     * @throws OneSignalException
     */
    public function putApp($app_id, $data = [])
    {
        $valid_params = [
            "id"                                   => true,
            "name"                                 => true,
            "apns_env"                             => false,
            "apns_p12"                             => false,
            "apns_p12_password"                    => false,
            "gcm_key"                              => false,
            "chrome_key"                           => false,
            "safari_apns_p12"                      => false,
            "safari_apns_p12_password"             => false,
            "chrome_web_key"                       => false,
            "site_name"                            => false,
            "safari_site_origin"                   => false,
            "safari_icon_16_16"                    => false,
            "safari_icon_32_32"                    => false,
            "safari_icon_64_64"                    => false,
            "safari_icon_128_128"                  => false,
            "safari_icon_256_256"                  => false,
            "chrome_web_gcm_sender_id"             => false,
            "chrome_web_default_notification_icon" => false,
            "chrome_web_sub_domain"                => false,
        ];
        $data[ 'id' ] = $app_id;

        $clean_data = [];

        foreach ($valid_params AS $param => $required) {
            if ($required && !array_key_exists($param, $data)) {
                throw new OneSignalException("Param $param not present");
            }

            if (array_key_exists($param, $data)) {
                $clean_data[ $param ] = $data[ $param ];
            }
        }

        $headers = $this->headerInit(true);
        $headers[ 'data-binary' ] = $clean_data;

        return $this->post("/apps", $headers);
    }


    /**
     * Generate an appropriate associative array of headers to send to the OneSignal API
     *
     * @param bool|false $userAuthKey Require the OneSignal User Auth Key
     * @param bool|false $appAuthKey  Require the OneSignal Application Auth Key. Ignored if $userAuthKey is true.
     * @param bool|true  $json        Set the Content-type of the request to JSON
     * @return array OneSignal friendly associative array of headers
     */
    protected function headerInit($userAuthKey = false, $appAuthKey = false, $json = true)
    {
        $headers = [];
        if ($userAuthKey) {
            $this->requiresUserAuth($headers);
        } else {
            if ($appAuthKey) {
                $this->requiresAuth($headers);
            }
        }
        if ($json) {
            $this->usesJSON($headers);
        }

        return $headers;
    }

    /**
     * POST a request to the OneSignal API
     *
     * @param string $endPoint API endpoint to POST to
     * @param array  $headers  Data and headers to send
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    protected function post($endPoint, $headers = [])
    {
        $response = $this->client->post(self::API_URL . "/" . $endPoint, $headers);

        return $response;
    }

    /**
     * GET a request from the OneSignal API
     *
     * @param string $endPoint API endpoint to GET from
     * @param array  $headers  Data and headers to send
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    protected function get($endPoint, $headers = [])
    {
        $response = $this->client->get(self::API_URL . "/" . $endPoint, $headers);

        return $response;
    }

    /**
     * PUT a request to the OneSignal API
     *
     * @param string $endPoint API endpoint to PUT to
     * @param array  $headers  Data and headers to send
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    protected function put($endPoint, $headers = [])
    {
        $response = $this->client->put(self::API_URL . "/" . $endPoint, $headers);

        return $response;
    }

    /**
     * DELETE a request from the OneSignal API
     *
     * @param string $endPoint API endpoint to DELETE from
     * @param array  $headers  Data and headers to send
     * @return \Psr\Http\Message\ResponseInterface OneSignal API response
     */
    protected function delete($endPoint, $headers = [])
    {
        $response = $this->client->delete(self::API_URL . "/" . $endPoint, $headers);

        return $response;
    }
}

class OneSignalException extends Exception
{

}