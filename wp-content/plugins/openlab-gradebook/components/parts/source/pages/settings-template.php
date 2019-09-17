<div id="an-gradebook-settings" class="wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap">

                    <h1><span><?php esc_html_e('About OpenLab GradeBook', 'openlab-gradebook')?></span></h1>
                    <div class="content-wrapper">

                        <p><?php esc_html_e('OpenLab GradeBook provides faculty with a simple, secure way to keep track of assignments and share grades with their students. When logged into the OpenLab, faculty may enter and view all student grades via their course site dashboard, and each student may log in and view their own grades.', 'openlab-gradebook');?>
                        </p>

                        <h2 class="h5 bold"><?php esc_html_e('Help', 'openlab-gradebook')?></h2>

                        <p><?php esc_html_e("For more information and help using OpenLab GradeBook, please visit OpenLab Help&nbsp;>&nbsp;", 'openlab-gradebook');?><a
                                class="bold"
                                href="<?php echo get_site_url()?><?php esc_html_e("/blog/help/openlab-gradebook/", 'openlab-gradebook')?>"><?php esc_html_e("OpenLab GradeBook", 'openlab-gradebook');?></a>.
                            <?php esc_html_e("There you will find documentation about creating a gradebook, adding assignments, adding students to your gradebook, entering and managing grades, adding a Gradebook widget, and student access.", 'openlab-gradebook');?>
                        </p>

                        <p><?php esc_html_e('If you experience any problems while using OpenLab GradeBook, please contact the OpenLab team for assistance.');?>
                        </p>

                        <h2 class="h5 bold"><?php esc_html_e('Assignment Grade Types', 'openlab-gradebook')?></h2>
                        <p><?php esc_html_e('Grade type can be set per assignment by selecting "Edit" from the dropdown menu.', 'openlab-gradebook');?>
                        </p>

                        <div class="grades-table-wrapper">
                            <table id="gradesTable" class="table table-bordered table-striped">
                                <tr>
                                    <th><?php esc_html_e('Checkmark:', 'openlab-gradebook')?></th>
                                    <th><?php esc_html_e('Letter:', 'openlab-gradebook')?>
                                    </th>
                                    <th><?php esc_html_e('Numeric:', 'openlab-gradebook')?></th>
                                </tr>
                                <tr>
                                    <td rowspan="12"><i
                                            class="oplb-grdbk-icon oplb-grdbk-icon-left oplb-grdbk-check-mark-2"></i><?php esc_html_e('checked box', 'openlab-gradebook')?>
                                    </td>
                                    <td>A+</td>
                                    <td>&gt;100</td>
                                </tr>
                                <tr>
                                    <td>A<br></td>
                                    <td>93-99.9</td>
                                </tr>
                                <tr>
                                    <td>A-</td>
                                    <td>90-92.9</td>
                                </tr>
                                <tr>
                                    <td>B+</td>
                                    <td>87 - 89.9 </td>
                                </tr>
                                <tr>
                                    <td>B</td>
                                    <td>83-86.9</td>
                                </tr>
                                <tr>
                                    <td>B-</td>
                                    <td>80-82.9</td>
                                </tr>
                                <tr>
                                    <td>C+</td>
                                    <td>77-79.9</td>
                                </tr>
                                <tr>
                                    <td>C</td>
                                    <td>73 - 76.9</td>
                                </tr>
                                <tr>
                                    <td>C-</td>
                                    <td>70 - 72.9</td>
                                </tr>
                                <tr>
                                    <td>D+</td>
                                    <td>67- 69.9</td>
                                </tr>
                                <tr>
                                    <td>D</td>
                                    <td>63 - 66.9</td>
                                </tr>
                                <tr>
                                    <td>D-</td>
                                    <td>60-62.9</td>
                                </tr>
                                <tr>
                                    <td><i
                                            class="oplb-grdbk-icon oplb-grdbk-icon-left oplb-grdbk-square-line"></i><?php esc_html_e('unchecked box', 'openlab-gradebook')?>
                                    </td>
                                    <td>F</td>
                                    <td>&lt;60</td>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>