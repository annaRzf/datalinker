<?php
// get all post types
$post_types = get_post_types( array( 'public' => true ), 'objects' );
?>
<div class="dl-main-content">
    <div class="dl-header">
        <div class="dl-header-inner-wrapper">
            <h2>New Exportation</h2>
        </div>
    </div>
    <div class="dl-main-wrapper">
        <div class="dl-section-container">
            <form action="" class="dl-form">
                <div class="step-info">
                    <ul class="progress-step">
                        <li class="active" data-step="1"><strong>Data information</strong></li>
                        <li data-step="2"><strong>Export information</strong></li>
                        <li data-step="3"><strong>Elements to export</strong></li>
                    </ul>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 33.33333%;"></div>
                    </div>
                </div>
                <fieldset class="form-step">
                    <div class="step-title">
                        <div class="title">
                            <h2>Data information</h2>
                        </div>
                        <div class="preview-section">
                            <p>No post matches your criteria yet</p>
                            <button class="dl-action-button">
                                <i class="fa-solid fa-eye"></i>
                                Preview posts <br> (3 records)
                            </button>
                        </div>
                    </div>
                    <div class="inner-section">
                        <div class="form-section">
                            <div class="form-group">
                                <label for="post_type">Choose what data to export</label>
                                <select name="post_type" id="post_type" class="form-control">
                                    <option value="">Select post type</option>
                                    <?php foreach ( $post_types as $post_type ) : ?>
                                        <option value="<?php echo $post_type->name; ?>"><?php echo $post_type->label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="post_type">Add rules filters to the data to export</label>
                                <div class="group-fields">
                                    <select name="post_type" id="post_type" class="form-control">
                                        <option value="">Select Element</option>
                                    </select>
                                    <select name="post_type" id="post_type" class="form-control">
                                        <option value="">Select Rule</option>
                                    </select>
                                    <input type="text" name="" id="" placeholder="Value">
                                    <button class="dl-action-button outlined">and</button>
                                    <button class="dl-action-button rounded outlined"><i class="fa-solid fa-trash"></i></button>
                                </div>
                                <h4>or</h4>
                                <button class="dl-action-button outlined">Add rule group</button>
                            </div>
                        </div>
                        <!-- <div class="form-section post-info">
                            <p>No post matches your criteria yet</p>
                        </div> -->
                    </div>
                    <input type="button" name="next" class="next dl-action-button" value="Next" data-bitwarden-clicked="1">
                </fieldset>
            </form>
        </div>
    </div>
</div>