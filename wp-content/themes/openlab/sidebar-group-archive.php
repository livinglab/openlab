<?php
global $bp, $wp_query;
$post_obj = $wp_query->get_queried_object();
$group_type = openlab_page_slug_to_grouptype();
$group_slug = $group_type . 's';

//conditional for people archive sidebar
if ($group_type == 'not-archive' && $post_obj->post_title == "People") {
    $group_type = "people";
    $group_slug = $group_type;
    $sidebar_title = 'Find People';
} else {
    $sidebar_title = 'Find a ' . ucfirst($group_type);
}
?>

<h2 class="sidebar-title"><?php echo $sidebar_title; ?></h2>
<div class="sidebar-block">
    <?php
//determine class type for filtering
    $school_color = "passive";
    $dept_color = "passive";
    $semester_color = "passive";
    $badge_color = "passive";
    $sort_color = "passive";
    $user_color = "passive";

//school filter - easiest to do this with a switch statment
    if (empty($_GET['school'])) {
        $_GET['school'] = "";
    } else if ($_GET['school'] == 'school_all') {
        $_GET['school'] = "school_all";
        $school_color = "active";
    } else {
        $school_color = "active";
    }

    $schools_and_offices = array_merge( openlab_get_school_list(), openlab_get_office_list() );
	switch ( $_GET['school'] ) {
		case 'school_all':
			$option_value_school = 'school_all';
			break;

		default :
			if ( isset( $schools_and_offices[ $_GET['school'] ] ) ) {
				$option_value_school = $_GET['school'];
			} else {
				$option_value_school = '';
			}
	}

//processing the department value - now dynamic instead of a switch statement
    if (empty($_GET['department'])) {
        $option_value_dept = "";
    } else if ($_GET['department'] == 'dept_all') {
        $option_value_dept = "dept_all";
    } else {
        $dept_color = "active";
        $option_value_dept = $_GET['department'];
    }

    //categories
    if (empty($_GET['cat'])) {
        $display_option_bpcgc = "Select Category";
        $option_value_bpcgc = "";
    } else if ($_GET['cat'] == 'cat_all') {
        $display_option_bpcgc = "All";
        $option_value_bpcgc = "cat_all";
    } else {
        $dept_color = "active";
        $display_option_bpcgc = ucwords(str_replace('-', ' ', $_GET['cat']));
        $display_option_bpcgc = str_replace('And', '&', $display_option_bpcgc);
        $option_value_bpcgc = $_GET['cat'];
    }

//semesters
    if (empty($_GET['semester'])) {
        $_GET['semester'] = "";
    } else {
        $semester_color = "active";
    }
//processing the semester value - now dynamic instead of a switch statement
    if (empty($_GET['semester'])) {
        $display_option_semester = "Select Semester";
        $option_value_semester = "";
    } else if ($_GET['semester'] == 'semester_all') {
        $display_option_semester = "All";
        $option_value_semester = "semester_all";
    } else {
        $dept_color = "active";
        $display_option_semester = ucfirst(str_replace('-', ' ', $_GET['semester']));
        $option_value_semester = $_GET['semester'];
    }

	if ( ! empty( $_GET['group_badge'] ) ) {
		$badge_color = 'active';
		$badge_value = wp_unslash( $_GET['group_badge'] );
	} else {
		$badge_value = '';
	}

//user types - for people archive page
    if (empty($_GET['usertype'])) {
        $_GET['usertype'] = "";
    } else {
        $user_color = "active";
    }
    switch ($_GET['usertype']) {

        case "student" :
            $display_option_user_type = "Student";
            $option_value_user_type = "student";
            break;
        case "faculty" :
            $display_option_user_type = "Faculty";
            $option_value_user_type = "faculty";
            break;
        case "staff" :
            $display_option_user_type = "Staff";
            $option_value_user_type = "staff";
            break;
        case 'alumni' :
            $display_option_user_type = 'Alumni';
            $option_value_user_type = 'alumni';
        case "user_type_all":
            $display_option_user_type = "All";
            $option_value_user_type = "user_type_all";
            break;
        default:
            $display_option_user_type = "Select User Type";
            $option_value_user_type = "";
            break;
    }
//sequence filter - easy enough to keep this as a switch for now
    if (empty($_GET['group_sequence'])) {
        $_GET['group_sequence'] = "active";
    } else {
        $sort_color = "active";
    }
    switch ($_GET['group_sequence']) {
        case "alphabetical":
            $display_option = "Alphabetical";
            $option_value = "alphabetical";
            break;
        case "newest":
            $display_option = "Newest";
            $option_value = "newest";
            break;
        case "active":
            $display_option = "Last Active";
            $option_value = "active";
            break;
        default:
            $display_option = "Order By";
            $option_value = "";
            break;
    }
    ?>
    <div class="filter">
        <p>Narrow down your search using the filters or search box below.</p>
        <form id="group_seq_form" name="group_seq_form" action="#" method="get">
            <div id="sidebarCustomSelect" class="custom-select-parent">
                <?php if ( 'course' === $group_type ) : ?>
                    <div class="custom-select" id="schoolSelect">
                        <label for="school-select" class="sr-only">Select School</label>
                        <select name="school" class="last-select <?php echo $school_color; ?>-text" id="school-select" tabindex="0">
                            <option value="" <?php selected('', $option_value_school) ?>>Select School</option>
                            <option value='school_all' <?php selected('school_all', $option_value_school) ?>>All Schools</option>
                            <?php foreach ( openlab_get_school_list() as $school_key => $school_label ) : ?>
                                <option value='<?php echo esc_attr( $school_key ); ?>' <?php selected( $school_key, $option_value_school ); ?>><?php echo esc_html( $school_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else : ?>
                    <div class="custom-select" id="schoolOfficeSelect">
                        <label for="school-select" class="sr-only">Select School/Office</label>
                        <select name="school" class="last-select" id="school-select" tabindex="0">
                            <option value="" <?php selected( '', $option_value_school ); ?>>Select School / Office</option>
                            <optgroup label="All Schools">
                            <?php foreach ( openlab_get_school_list() as $school_key => $school_label ) : ?>
                                <option value="<?php echo esc_attr( $school_key ); ?>" <?php selected( $school_key, $option_value_school ); ?>><?php echo esc_html( $school_label ); ?></option>
                            <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="All Offices">
                            <?php foreach ( openlab_get_office_list() as $office_key => $office_label ) : ?>
                                <option value="<?php echo esc_attr( $office_key ); ?>" <?php selected( $office_key, $option_value_school ); ?>><?php echo esc_html( $office_label ); ?></option>
                            <?php endforeach; ?>

                            </optgroup>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="hidden" id="nonce-value"><?php echo wp_create_nonce("dept_select_nonce"); ?></div>
                <div class="custom-select">
                    <label for="dept-select" class="sr-only">Select Department</label>
                    <select name="department" class="last-select processing <?php echo $dept_color; ?>-text" id="dept-select" <?php disabled('', $option_value_school) ?>>
                        <?php echo openlab_return_course_list($option_value_school, $option_value_dept); ?>
                    </select>
                </div>

                <?php if (function_exists('bpcgc_get_terms_by_group_type')): ?>
                    <?php if ($group_type === 'project' || $group_type === 'club'): ?>

                        <?php $group_terms = bpcgc_get_terms_by_group_type($group_type); ?>

                        <?php if ($group_terms && !empty($group_terms)): ?>

                            <div class="custom-select">
                                <label for="bp-group-categories-select" class="sr-only">Select Category</label>
                                <select name="cat" class="last-select <?php echo $bpcgc_color; ?>-text" id="bp-group-categories-select">
                                    <option value="" <?php selected('', $option_value_bpcgc) ?>>Select Category</option>
                                    <option value='cat_all' <?php selected('cat_all', $option_value_bpcgc) ?>>All</option>
                                    <?php foreach ($group_terms as $term) : ?>
                                        <option value="<?php echo $term->slug ?>" <?php selected($option_value_bpcgc, $term->slug) ?>><?php echo $term->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif;
                ?>

                <?php // @todo figure out a way to make this dynamic ?>
                <?php if ($group_type == 'course'): ?>
                    <div class="custom-select">
                        <label for="semester-select" class="sr-only">Select Semester</label>
                        <select id="semester-select" name="semester" class="last-select <?php echo $semester_color; ?>-text">
                            <option value='' <?php selected('', $option_value_semester) ?>>Select Semester</option>
                            <option value='semester_all' <?php selected('semester_all', $option_value_semester) ?>>All</option>
                            <?php foreach (openlab_get_active_semesters() as $sem) : ?>
                                <option value="<?php echo esc_attr($sem['option_value']) ?>" <?php selected($option_value_semester, $sem['option_value']) ?>><?php echo esc_attr($sem['option_label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($group_type == 'portfolio' || $post_obj->post_title == 'People'): ?>
                    <div class="custom-select">
                        <label for="user-type-select" class="sr-only">Select User Type</label>
                        <select id="user-type-select" name="usertype" class="last-select <?php echo $user_color; ?>-text">
                            <option value='' <?php selected('', $option_value_user_type) ?>>Select User Type</option>
                            <option value='student' <?php selected('student', $option_value_user_type) ?>>Student</option>
                            <option value='faculty' <?php selected('faculty', $option_value_user_type) ?>>Faculty</option>
                            <option value='staff' <?php selected('staff', $option_value_user_type) ?>>Staff</option>
                            <option value='alumni' <?php selected('alumni', $option_value_user_type) ?>>Alumni</option>
                            <option value='user_type_all' <?php selected('user_type_all', $option_value_user_type) ?>>All</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="custom-select">
                    <label for="sequence-select" class="sr-only">Select Sort Order</label>
                    <select id="sequence-select" name="group_sequence" class="last-select <?php echo $sort_color; ?>-text">
                        <option <?php selected($option_value, 'alphabetical') ?> value='alphabetical'>Alphabetical</option>
                        <option <?php selected($option_value, 'newest') ?>  value='newest'>Newest</option>
                        <option <?php selected($option_value, 'active') ?> value='active'>Last Active</option>
                    </select>
                </div>

				<?php $badges = \OpenLab\Badges\Badge::get( array( 'hide_empty' => true ) ); ?>
				<?php if ( $badges && in_array( $group_type, array( 'course', 'project' ), true ) ) : ?>
					<div class="custom-select">
						<label for="badge-select" class="sr-only">Select Type</label>
						<select id="badge-select" name="group_badge" class="last-select <?php echo $badge_color; ?>-text">
                            <option value='all' <?php selected( '', $badge_value ) ?>>Select Type</option>
                            <option value='all' <?php selected( 'all', $badge_value ) ?>>All</option>
							<?php foreach ( $badges as $badge ) : ?>
								<option <?php selected( $badge->get_id(), $badge_value ); ?> value="<?php echo esc_attr( $badge->get_id() ); ?>"><?php echo esc_html( $badge->get_name() ); ?></option>

							<?php endforeach; ?>

							<?php if ( 'course' === $group_type ) : ?>
								<option value='cloneable' <?php selected( 'cloneable', $badge_value ); ?>>Cloneable Courses</option>
							<?php endif; ?>
						</select>
					</div>
				<?php endif; ?>

            </div>
            <input class="btn btn-primary" type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
            <input class="btn btn-default" type="button" value="Reset" onClick="window.location.href = '<?php echo $bp->root_domain ?>/<?php echo $group_slug; ?>/'">
        </form>

        <div class="archive-search">
            <h3 class="bold font-size font-14">Search</h3>
            <form method="get" class="form-inline btn-combo" role="form">
                <div class="form-group">
                    <input id="search-terms" class="form-control" type="text" name="search" placeholder="Enter keyword" /><label class="sr-only" for="search-terms">Enter keyword</label><button class="btn btn-primary top-align" id="search-submit" type="submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search</span></button>
                </div>
            </form>
            <div class="clearfloat"></div>
        </div><!--archive search-->
    </div><!--filter-->
</div>
<?php

function slug_maker($full_string) {
    $slug_val = str_replace(" ", "-", $full_string);
    $slug_val = strtolower($slug_val);
    return $slug_val;
}
