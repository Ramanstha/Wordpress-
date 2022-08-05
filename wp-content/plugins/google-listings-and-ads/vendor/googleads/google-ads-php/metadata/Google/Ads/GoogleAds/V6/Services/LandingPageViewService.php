<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v6/services/landing_page_view_service.proto

namespace GPBMetadata\Google\Ads\GoogleAds\V6\Services;

class LandingPageViewService
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();
        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Api\Http::initOnce();
        \GPBMetadata\Google\Api\Annotations::initOnce();
        \GPBMetadata\Google\Api\FieldBehavior::initOnce();
        \GPBMetadata\Google\Api\Resource::initOnce();
        \GPBMetadata\Google\Api\Client::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
9google/ads/googleads/v6/resources/landing_page_view.proto!google.ads.googleads.v6.resourcesgoogle/api/resource.protogoogle/api/annotations.proto"�
LandingPageViewG
resource_name (	B0�A�A*
(googleads.googleapis.com/LandingPageView&
unexpanded_final_url (	B�AH �:z�Aw
(googleads.googleapis.com/LandingPageViewKcustomers/{customer_id}/landingPageViews/{unexpanded_final_url_fingerprint}B
_unexpanded_final_urlB�
%com.google.ads.googleads.v6.resourcesBLandingPageViewProtoPZJgoogle.golang.org/genproto/googleapis/ads/googleads/v6/resources;resources�GAA�!Google.Ads.GoogleAds.V6.Resources�!Google\\Ads\\GoogleAds\\V6\\Resources�%Google::Ads::GoogleAds::V6::Resourcesbproto3
�
@google/ads/googleads/v6/services/landing_page_view_service.proto google.ads.googleads.v6.servicesgoogle/api/annotations.protogoogle/api/client.protogoogle/api/field_behavior.protogoogle/api/resource.proto"d
GetLandingPageViewRequestG
resource_name (	B0�A�A*
(googleads.googleapis.com/LandingPageView2�
LandingPageViewService�
GetLandingPageView;.google.ads.googleads.v6.services.GetLandingPageViewRequest2.google.ads.googleads.v6.resources.LandingPageView"J���42/v6/{resource_name=customers/*/landingPageViews/*}�Aresource_nameE�Agoogleads.googleapis.com�A\'https://www.googleapis.com/auth/adwordsB�
$com.google.ads.googleads.v6.servicesBLandingPageViewServiceProtoPZHgoogle.golang.org/genproto/googleapis/ads/googleads/v6/services;services�GAA� Google.Ads.GoogleAds.V6.Services� Google\\Ads\\GoogleAds\\V6\\Services�$Google::Ads::GoogleAds::V6::Servicesbproto3'
        , true);
        static::$is_initialized = true;
    }
}

