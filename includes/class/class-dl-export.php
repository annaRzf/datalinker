<?php

final class DataLinkeRExport
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
            if( $key == 'menus' )
                $icon = 'fa-solid fa-bars';
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

    public function get_post_rules($field_type = '')
    {
        // default rules
        $rules = [
            '' => ['text' => 'Select Rule'],
            'equals' => ['text' => 'equals'],
            'not_equals' => ['text' => "doesn't equal"],
            'greater' => ['text' => 'greater than'],
            'equals_or_greater' => ['text' => 'equal to or greater than'],
            'less' => ['text' => 'less than'],
            'equals_or_less' => ['text' => 'equal to or less than'],
            'contains' => ['text' => 'contains'],
            'not_contains' => ['text' => "doesn't contain"],
            'is_empty' => ['text' => 'is empty'],
            'is_not_empty' => ['text' => 'is not empty'],
            'in' => ['text' => 'in'],
            'not_in' => ['text' => 'not In'],
        ];
        // filter rules depending on the field type (text,id,array,...)
        return $rules;
    }

    public function get_post_filters($post_type = '', $taxonomy = '')
    {
        // TODO: use filters available in WP_Query to build query
        $filters = [];
        // add post type specific filters
        if ($post_type) {
            switch ($post_type) {
                case 'post':
                    $post_filters = [
                        'Standard' => [
                            'p' => ['text' => 'ID'],
                            'post_title' => ['text' => 'Title'],
                            'post_content' => ['text' => 'Content'],
                            'post_excerpt' => ['text' => 'Excerpt'],
                            'post_date' => ['text' => 'Date'],
                            'permalink' => ['text' => 'Permalink']
                        ],
                        'Taxonomies' => [
                            'category' => ['text' => 'Category'],
                            'post_tag' => ['text' => 'Tag']
                        ],
                        'Custom Fields' => [
                            'meta_key' => ['text' => 'Meta Key'],
                            'meta_value' => ['text' => 'Meta Value']
                        ],
                        'Author' => [
                            'author' => ['text' => 'Author ID'],
                            'author_name' => ['text' => 'Author Name'],
                            'author_email' => ['text' => 'Author Email'],
                            'author_username' => ['text' => 'Author Username']
                        ]
                    ];
                    $filters = array_merge($filters, $post_filters);
                    break;
                case 'page':
                    $page_filters = [
                        'Standard' => [
                            'p' => ['text' => 'ID'],
                            'post_title' => ['text' => 'Title'],
                            'post_content' => ['text' => 'Content'],
                            'post_excerpt' => ['text' => 'Excerpt'],
                            'post_date' => ['text' => 'Date'],
                            'permalink' => ['text' => 'Permalink']
                        ],
                        'Custom Fields' => [
                            'meta_key' => ['text' => 'Meta Key'],
                            'meta_value' => ['text' => 'Meta Value']
                        ],
                        'Author' => [
                            'author' => ['text' => 'Author ID'],
                            'author_name' => ['text' => 'Author Name'],
                            'author_email' => ['text' => 'Author Email'],
                            'author_username' => ['text' => 'Author Username']
                        ]
                    ];
                    $filters = array_merge($filters, $page_filters);
                    break;
                case 'users':
                    $user_filters = [
                        'Standard' => [
                            'ID' => ['text' => 'ID'],
                            'user_login' => ['text' => 'Username'],
                            'user_email' => ['text' => 'Email'],
                            'user_url' => ['text' => 'Website'],
                            'user_registered' => ['text' => 'Registered'],
                            'display_name' => ['text' => 'Display Name'],
                            'first_name' => ['text' => 'First Name'],
                            'last_name' => ['text' => 'Last Name'],
                            'role' => ['text' => 'Role']
                        ],
                        'Meta' => [
                            'meta_key' => ['text' => 'Meta Key'],
                            'meta_value' => ['text' => 'Meta Value']
                        ]
                    ];
                    $filters = array_merge($filters, $user_filters);
                    break;
                default:
                    # code...
                    break;
            }
        }
        if( $taxonomy ){
            $taxonomy_filters = [
                'Standard' => [
                    'term_id' => ['text' => 'ID'],
                    'taxonomy' => ['text' => 'Name'],
                    'slug' => ['text' => 'Slug'],
                    'description' => ['text' => 'Description'],
                    'count' => ['text' => 'Count']
                ],
            ];
            $filters = array_merge($filters, $taxonomy_filters);
        }
        // add default option
        $filters = array_merge(['' => ['' => ['text' => 'Select an element']]], $filters);

        return $filters;
    }
}