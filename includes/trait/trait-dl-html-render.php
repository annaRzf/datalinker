<?php

trait DataLinkeRHTMLRender
{
    public function render_dropdown($name, $options = [], $groups = [])
    {
        ?>
        <div class="dl-dropdown">
            <select name="<?php echo esc_attr($name); ?>" class="dl-dropdown-select">
                <?php if (!empty($groups)) : ?>
                    <?php foreach ($groups as $group_label => $group_options) : ?>
                        <optgroup label="<?php echo esc_html($group_label); ?>">
                            <?php foreach ($group_options as $value => $label) : ?>
                                <option  <?php if( isset($label['icon']) )  echo 'data-icon="'.$label['icon'].'"'; ?> value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label['text']); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                <?php else : ?>
                    <?php foreach ($options as $value => $label) : ?>
                        <option  <?php if( isset($label['icon']) )  echo 'data-icon="'.$label['icon'].'"'; ?> value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label['text']); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <?php
    }
}