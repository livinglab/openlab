<p> <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title: (defaults to "Link to OpenLab Gradebook")'); ?></label> <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_html($title); ?>" /></p><p> <label for="<?php echo esc_attr($this->get_field_id('message')); ?>"><?php esc_html_e('Included Message (optional)'); ?></label> <textarea class="widefat" rows="16" cols="20" id="<?php echo esc_attr($this->get_field_id('message')); ?>" name="<?php echo esc_attr($this->get_field_name('message')); ?>"><?php echo esc_html($message); ?></textarea></p>