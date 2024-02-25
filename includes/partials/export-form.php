<?php
// get all post types
$post_types = get_post_types( array( 'public' => true ), 'objects' );
?>
<div class="wrap">
    <h2>Export Custom Post Type Data</h2>
    <form method="post" action="" id="pl-export-form">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="post_type">Select Post Type:</label>
                    </th>
                    <td>
                        <select name="post_type" id="post_type">
                            <?php
                            foreach ( $post_types as $post_type ) {
                                echo '<option value="' . $post_type->name . '">' . $post_type->labels->singular_name . '</option>';
                            }
                            ?>
                        </select>          
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Fields exported:
                    </th>
                    <td>
                        <ul>
                            <li>Title</li>
                            <li>Content</li>
                            <li>Featured Image</li>
                            <li>Categories</li>
                            <li>ACF Festival Slug</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>
            <input type="submit" name="export_submit" class="button-primary" value="Export">
        </p>
    </form>
</div>