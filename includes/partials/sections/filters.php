<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please! In other words GTFO.' );
?>
<label for="export_filters">Add rules filters to the data to export</label>
<div class="rule-group-container">
    <div class="rule-group">
        <div class="rule-rows">
            <div class="group-fields rule-row">
                <?php $dl_export->render_dropdown('export_filters',[],$filters) ?>
                <input type="text" name="" id="" placeholder="Value">
                <button class="dl-action-button outlined add-rule-row">and</button>
                <button class="dl-action-button rounded outlined remove-rule-row invisible"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        <h4 class="or-statement">or</h4>
    </div>
</div>
<button class="dl-action-button outlined add-rule-group">Add rule group</button>