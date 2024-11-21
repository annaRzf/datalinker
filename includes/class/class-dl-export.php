<?php

class DataLinkeRExport
{
    use DataLinkeRHTMLRender;
    use DataLinkeRHelpers;

    /**
     * Retrieves and formats the available post types.
     *
     * This function fetches built-in, custom post types with UI, and custom post types without UI.
     * It then merges these post types, applies a filter, and assigns icons to each post type.
     * Finally, it adds a default option and returns the formatted array of post types.
     *
     * @return array An associative array of post types with their corresponding icons.
     *               The array structure is:
     *               [
     *                   'post_type_key' => [
     *                       'text' => 'Post Type Object',
     *                       'icon' => 'FontAwesome Icon Class'
     *                   ],
     *                   ...
     *               ]
     *               The array also includes a default option:
     *               [
     *                   ' ' => [
     *                       'text' => 'Select a post type'
     *                   ],
     *                   ...
     *               ]
     */
    public function get_post_types()
    {
        $post_types_builtin = get_post_types(array('_builtin' => true), 'objects');
        $post_types_custom_ui = get_post_types(array('_builtin' => false, 'show_ui' => true), 'objects');
        $post_types_custom_no_ui = get_post_types(array('_builtin' => false, 'show_ui' => false), 'objects');

        $post_types = array_merge($post_types_builtin, $post_types_custom_ui, $post_types_custom_no_ui);

        // convert to array
        $post_types_array = apply_filters('dl_post_types', $post_types);
        // set post types icons
        foreach ($post_types_array as $key => $value) {
            $icon = 'fa-solid fa-database';
            if( $key == 'comments' )
                $icon = 'fa-solid fa-comment';
            if( $key == 'users' )
                $icon = 'fa-solid fa-users';
            if( $key == 'taxonomies' )
                $icon = 'fa-solid fa-tags';
            if( $key == 'page' || $key == 'post' )
                $icon = 'fa-solid fa-file';
            
            $post_types_array[$key] = [
                'text' => $value,
                'icon' => $icon
            ];
        }
        // Add default option
        $post_types_array = array_merge([' ' => ['text' => 'Select a post type']], $post_types_array);

        return $post_types_array;
    }

    /**
     * Retrieves and formats a list of taxonomies.
     *
     * This function fetches all available taxonomies, filters out the ones that are
     * ignored, maps them to a specific format, sorts them alphabetically, and adds
     * a default option at the beginning of the list.
     *
     * @return array An array of taxonomies formatted as ['text' => 'Taxonomy Label'].
     *               The array includes a default option [' ' => ['text' => 'Select a taxonomy']].
     */
    public function get_taxonomies()
    {
       // Get all taxonomies
        $taxonomies = get_taxonomies([], 'objects');
        $ignore = ['nav_menu', 'link_category'];

        // Filter out ignored taxonomies
        $filtered_taxonomies = array_filter($taxonomies, function($taxonomy) use ($ignore) {
            return !in_array($taxonomy->name, $ignore);
        });

        // Map taxonomies to the desired format
        $taxonomies_array = array_map(function($taxonomy) {
            $taxonomy_label = !empty($taxonomy->labels->name) && strpos($taxonomy->labels->name, "_") === false
                ? $taxonomy->labels->name
                : (empty($taxonomy->labels->singular_name) ? $taxonomy->name : $taxonomy->labels->singular_name);
            return ['text' => $taxonomy_label];
        }, $filtered_taxonomies);

        // Sort taxonomies array
        uasort($taxonomies_array, function($a, $b) {
            return strcasecmp($a['text'], $b['text']);
        });

        // Add default option
        $taxonomies_array = array_merge([' ' => ['text' => 'Select a taxonomy']], $taxonomies_array);

        return $taxonomies_array;
    }
}