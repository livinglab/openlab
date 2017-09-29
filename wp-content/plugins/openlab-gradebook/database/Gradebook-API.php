<?php

class OPLB_GradeBookAPI {

    public function __construct() {
        add_action('wp_ajax_oplb_gradebook_get_settings', array($this, 'oplb_gradebook_get_settings'));
        add_action('wp_ajax_oplb_gradebook_set_settings', array($this, 'oplb_gradebook_set_settings'));
        add_action('wp_ajax_get_csv', array($this, 'get_csv'));
        add_action('wp_ajax_get_pie_chart', array($this, 'get_pie_chart'));
        add_action('wp_ajax_get_line_chart', array($this, 'get_line_chart'));
        add_action('wp_ajax_get_gradebook_config', array($this, 'get_gradebook_config'));
        add_action('wp_ajax_get_student', array($this, 'get_student'));
    }

    public function oplb_gradebook_get_settings() {
        wp_cache_delete('alloptions', 'options');
        $settings = get_option('oplb_gradebook_settings');
        echo json_encode(array('gradebook_administrators' => $settings));
        die();
    }

    public function oplb_gradebook_set_settings() {
        $params = json_decode(file_get_contents('php://input'), true);
        unset($params['action']);
        $params['administrator'] = true;
        wp_cache_delete('alloptions', 'options');
        $didupdateQ = update_option('oplb_gradebook_settings', $params);
        wp_cache_delete('alloptions', 'options');
        echo json_encode(array('gradebook_administrators' => get_option('oplb_gradebook_settings')));
        die();
    }

    public function get_csv() {
        global $wpdb, $oplb_gradebook_api, $oplb_upload_csv;

        //grab the letters in case we need them
        $letter_grades = $oplb_upload_csv->getLetterGrades();

        $gbid = $_GET['id'];
        if (!is_user_logged_in() || $oplb_gradebook_api->oplb_gradebook_get_user_role($gbid) != 'instructor') {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }
        $course = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = $gbid", ARRAY_A);
        $assignments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = $gbid", ARRAY_A);

        $grade_types_by_aid = array();

        foreach ($assignments as &$assignment) {
            $assignment['id'] = intval($assignment['id']);
            $assignment['gbid'] = intval($assignment['gbid']);
            $assignment['assign_order'] = intval($assignment['assign_order']);
            $grade_types_by_amID[$assignment['id']] = $assignment['assign_grade_type'];
        }
        usort($assignments, build_sorter('assign_order'));

        //setup weights - first three columns are blank to accommodate student info
        $weights = array("", "", "", "weight");
        foreach ($assignments as $assignment) {

            $weight = $assignment['assign_weight'] . '%';
            array_push($weights, $weight);
        }

        $column_headers_assignment_names = array();

        foreach ($assignments as &$assignment) {
            array_push($column_headers_assignment_names, $assignment['assign_name']);
        }
        $column_headers = array_merge(
                array('firstname', 'lastname', 'user_login', 'current_average_grade'), $column_headers_assignment_names
        );
        $cells = array();
        $cells = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE gbid = $gbid", ARRAY_A);
        foreach ($cells as &$cell) {
            $cell['gbid'] = intval($cell['gbid']);
        }

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = %s", $gbid, 'student');

        $students = $wpdb->get_results($query);
        foreach ($students as &$value) {
            $studentData = get_userdata($value->uid);
            $value = array(
                'firstname' => $studentData->first_name,
                'lastname' => $studentData->last_name,
                'user_login' => $studentData->user_login,
                'current_grade_average' => $value->current_grade_average,
                'id' => intval($studentData->ID),
            );
        }

        foreach ($cells as &$cell) {
            $cell['amid'] = intval($cell['amid']);
            $cell['uid'] = intval($cell['uid']);
            $cell['assign_order'] = intval($cell['assign_order']);

            //crunch grades by grade type
            $this_cell_grade_type = $grade_types_by_amID[$cell['amid']];

            switch ($this_cell_grade_type) {
                case 'letter':

                    $cell['assign_points_earned'] = $oplb_upload_csv->numeric_to_letter_grade_conversion(floatval($cell['assign_points_earned']));

                    break;
                case 'checkmark':

                    if (floatval($cell['assign_points_earned']) >= 60) {
                        $cell['assign_points_earned'] = 'x';
                    } else {
                        $cell['assign_points_earned'] = '';
                    }

                    break;
                default:

                    $cell['assign_points_earned'] = floatval($cell['assign_points_earned']);
            }
        }
        usort($cells, build_sorter('assign_order'));
        $student_records = array();
        foreach ($students as &$row) {
            $records_for_student = array_filter($cells, function($k) use ($row) {
                return $k['uid'] == $row['id'];
            });
            $scores_for_student = array_map(function($k) {
                return $k['assign_points_earned'];
            }, $records_for_student);
            $student_record = array_merge($row, $scores_for_student);
            array_push($student_records, $student_record);
        }
        header('Content-Type: text/csv; charset=utf-8');
        $filename = str_replace(" ", "_", $course['name'] . '_' . $gbid);
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        fputcsv($output, $weights);
        fputcsv($output, $column_headers);
        foreach ($student_records as &$row) {

            //don't want to output student internal GB id
            unset($row['id']);

            fputcsv($output, $row);
        }
        fclose($output);
        die();
    }

    public function get_pie_chart() {
        global $wpdb;
        //need to check that user has access to this assignment.
        if (!is_user_logged_in()) {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }
        $pie_chart_data = $wpdb->get_col("SELECT assign_points_earned FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid = {$_GET['amid']}");

        function isA($n) {
            return ($n >= 90 ? true : false);
        }

        function isB($n) {
            return ($n >= 80 && $n < 90 ? true : false);
        }

        function isC($n) {
            return ($n >= 70 && $n < 80 ? true : false);
        }

        function isD($n) {
            return ($n >= 60 && $n < 70 ? true : false);
        }

        function isF($n) {
            return ($n < 60 ? true : false);
        }

        $is_A = count(array_filter($pie_chart_data, 'isA'));
        $is_B = count(array_filter($pie_chart_data, 'isB'));
        $is_C = count(array_filter($pie_chart_data, 'isC'));
        $is_D = count(array_filter($pie_chart_data, 'isD'));
        $is_F = count(array_filter($pie_chart_data, 'isF'));

        $output = array(
            "grades" => array($is_A, $is_B, $is_C, $is_D, $is_F)
        );

        echo json_encode($output);
        die();
    }

    public function get_line_chart() {
        global $wpdb;
        //need to check that user has access to this gradebook.		
        $uid = get_current_user_id();
        if (!is_user_logged_in()) {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }
        $line_chart_data1 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = {$_GET['uid']} AND gbid = {$_GET['gbid']}", ARRAY_A);
        $line_chart_data2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_assignments WHERE gbid = {$_GET['gbid']}", ARRAY_A);

        foreach ($line_chart_data1 as &$line_chart_value1) {
            $line_chart_value1['assign_order'] = intval($line_chart_value1['assign_order']);
            $line_chart_value1['assign_points_earned'] = floatval($line_chart_value1['assign_points_earned']);
            foreach ($line_chart_data2 as $line_chart_value2) {
                if ($line_chart_value2['id'] == $line_chart_value1['amid']) {
                    $all_homework_scores = $wpdb->get_col("SELECT assign_points_earned FROM {$wpdb->prefix}oplb_gradebook_cells WHERE amid = {$line_chart_value2['id']}");
                    $class_average = array_sum($all_homework_scores) / count($all_homework_scores);

                    $line_chart_value1 = array_merge($line_chart_value1, array('assign_name' => $line_chart_value2['assign_name'], 'class_average' => $class_average));
                }
            }
        }
        $result = array(array("Assignment", "Student Score", "Class Average"));
        foreach ($line_chart_data1 as $line_chart_value3) {
            array_push($result, array($line_chart_value3['assign_name'], $line_chart_value3['assign_points_earned'], $line_chart_value3['class_average']));
        }


        echo json_encode($result);
        die();
    }

    public function get_gradebook_config() {
        if (!is_user_logged_in()) {
            echo json_encode(array("status" => "Not Allowed."));
            die();
        }
        global $wpdb;
        $user_id = wp_get_current_user()->ID;
        $wp_role = get_userdata($user_id)->roles;
        $user_courses = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = $user_id", ARRAY_A);
        foreach ($user_courses as &$user_course) {
            $user_data = get_userdata($user_course['uid']);
            $user_course['first_name'] = $user_data->first_name;
            $user_course['last_name'] = $user_data->last_name;
            $user_course['user_login'] = $user_data->user_login;
            $user_course['id'] = intval($user_course['id']);
            $user_course['gbid'] = intval($user_course['gbid']);
            $user_course['uid'] = intval($user_course['uid']);
        }
        $sql = "( SELECT gbid FROM {$wpdb->prefix}oplb_gradebook_users WHERE uid = $user_id )";
        $courses = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id IN $sql", ARRAY_A);
        foreach ($courses as &$course) {
            $course['id'] = intval($course['id']);
            $course['year'] = intval($course['year']);
        }
        $administrators = get_option('oplb_gradebook_settings');
        echo json_encode(array('administrators' => $administrators, 'courses' => $courses, 'roles' => $user_courses, 'wp_role' => $wp_role[0]));
        die();
    }

}

?>