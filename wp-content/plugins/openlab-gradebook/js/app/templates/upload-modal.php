<div class="modal-dialog" id="modalDialogUpload">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                aria-label="Close<?php esc_html_e('Close', 'openlab-gradebook')?>"><span
                    aria-hidden="true">&times;</span></button>

            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Upload CSV Instructions', 'openlab-gradebook')?>
            </h4>
        </div>
        <div class="modal-body">
            <p class="bold">
                <?php _e('Follow these steps to add assignments and/or grades to your Gradebook using a pre-formatted CSV file.', 'openlab-gradebook') ?>
            </p>
            <ol>
                <li><?php _e('Add Students to your Gradebook using the Add students button.', 'openlab-gradebook') ?><br /><em><?php _e('NOTE: Students cannont be added or edited via the CSV file.', 'openlab-gradebook') ?></em>
                </li>
                <li><?php _e('After adding students to your Gradebook, download this CSV file.', 'openlab-gradebook') ?><br /><button
                        type="button" id="modal-download-csv"
                        class="btn btn-default"><?php esc_html_e('Download CSV', 'openlab-gradebook')?></button></li>
                <li><?php _e('Add assignments and grades to the downloaded CSV file.', 'openlab-gradebook') ?></li>
                <li><?php _e('Return to this window to upload your ammended CSV file', 'openlab-gradebook') ?></li>
                <li><?php _e('If your CSV file is error-free, your assignments and/or grades will be added to your Gradebook. If there are errors, a new CSV file will be generated and the cells that need to be edited will be highlighted. Correct the errors and re-upload.', 'openlab-gradebook') ?>
                </li>
            </ol>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook') ?></button>
            <button type="button" id="modal-upload-csv" data-dismiss="modal" class="btn btn-primary"><?php esc_html_e('Upload CSV', 'openlab-gradebook') ?></button>
        </div>
    </div>
</div>