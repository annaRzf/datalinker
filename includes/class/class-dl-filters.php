<?php

final class DataLinkeRFilters
{
    use DataLinkeRHelpers;

    public function __construct()
    {
        add_filter('dl_post_types', [$this, 'dl_post_types']);
    }

    /**
     * Filters and organizes post types.
     *
     * This method filters out unused post types, ensures 'page' and 'post' are first in the list,
     * adds taxonomies, users, and comments, and then appends the remaining post types.
     *
     * @param array $post_types An array of post type objects.
     * @return array An array of filtered and organized post types with their labels.
     */
    public function dl_post_types($post_types)
    {
        $unused_post_types = [
            'attachment', 'revision', 'nav_menu_item', 'import_users', 'shop_webhook', 
            'acf-field', 'acf-field-group', 'wp_block', 'customize_changeset', 
            'custom_css', 'scheduled_action', 'scheduled-action', 'user_request', 
            'oembed_cache', 'wp_navigation','acf-taxonomy'
        ];
    
        // Filter out unused post types
        $post_types = array_filter($post_types, function($key) use ($unused_post_types) {
            return !in_array($key, $unused_post_types);
        }, ARRAY_FILTER_USE_KEY);
    
        // Convert to array and ensure 'page' and 'post' are first
        $post_types_array = [];
        if (isset($post_types['page'])) {
            $post_types_array['page'] = $post_types['page']->label;
            unset($post_types['page']);
        }
        if (isset($post_types['post'])) {
            $post_types_array['post'] = $post_types['post']->label;
            unset($post_types['post']);
        }
    
        // Add taxonomies, users, and comments
        $post_types_array = array_merge($post_types_array, [
            'taxonomies' => 'Taxonomies',
            'users' => 'Users',
            'comments' => 'Comments'
        ]);
    
        // Add the rest of the post types
        foreach ($post_types as $post_type) {
            $post_types_array[$post_type->name] = $post_type->label;
        }
    
        return $post_types_array;
    }
}