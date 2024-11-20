<?php

class DataLinkeRExport
{
    use DataLinkeRHTMLRender;
    use DataLinkeRHelpers;

    /**
     * Retrieve an array of public post types.
     *
     * This function fetches all public post types and converts them into an associative array
     * where the keys are the post type names and the values are the post type labels.
     *
     * @return array An associative array of public post types with post type names as keys and labels as values.
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
        return $post_types_array;
    }
}