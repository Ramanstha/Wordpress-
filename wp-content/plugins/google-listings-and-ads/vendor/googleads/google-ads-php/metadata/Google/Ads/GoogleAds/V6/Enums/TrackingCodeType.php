<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v6/enums/tracking_code_type.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V6\Enums;

class TrackingCodeType
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Api\Http::initOnce();
        \GPBMetadata\Google\Api\Annotations::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
6google/ads/googleads/v6/enums/tracking_code_type.protogoogle.ads.googleads.v6.enums"�
TrackingCodeTypeEnum"w
TrackingCodeType
UNSPECIFIED 
UNKNOWN
WEBPAGE
WEBPAGE_ONCLICK
CLICK_TO_CALL
WEBSITE_CALLB�
!com.google.ads.googleads.v6.enumsBTrackingCodeTypeProtoPZBgoogle.golang.org/genproto/googleapis/ads/googleads/v6/enums;enums�GAA�Google.Ads.GoogleAds.V6.Enums�Google\\Ads\\GoogleAds\\V6\\Enums�!Google::Ads::GoogleAds::V6::Enumsbproto3'
        , true);
        static::$is_initialized = true;
    }
}

