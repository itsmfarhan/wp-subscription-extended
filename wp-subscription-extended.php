<?php
/**
 * Plugin Name: WP Subscription Extended
 * Description: Extended subscription search functionality for admin
 * Author: Muhammad Farhan Mahmood
 * Version: 1.1
 */

if (!class_exists('WPS_SEARCH')) {

    class WPS_SEARCH
    {
        public function __construct()
        {

            if (is_admin()) {

                add_action('wp_ajax_wc_memberships_edit_membership_subscription_link', [$this, 'wps_get_subscriptions'], 10, 2);

            }
        }

        public function wps_get_subscriptions()
        {

            // security check
            check_ajax_referer('edit-membership-subscription-link', 'security');

            // get the search term
            $serach_term = isset($_REQUEST['term']) ? urldecode(stripslashes(strip_tags($_REQUEST['term']))) : '';

            // abort if void
            if (empty($serach_term)) {
                die;
            }

            // query for subscription id
            if (is_numeric($serach_term)) {

                $args = ['post_in' => [(int) $serach_term]];

            }

            $integration = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();

            $results = $integration->get_subscriptions_ids($args);

            $subscriptions = [];

            if (!empty($results)) {

                foreach ($results as $subscription_id) {

                    if ($subscription = wcs_get_subscription($subscription_id)) {

                        $subscriptions[$subscription->get_id()] = $integration->get_formatted_subscription_id_holder_name($subscription);
                    }
                }
            }

            wp_send_json($subscriptions);
        }

    }
}
new WPS_SEARCH();
