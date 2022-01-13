<?php
/**
* Plugin Name: Jobs plugin
* Plugin URI: http://www.meta13.com
* Description: This plugin displays jobs from a feed on the website
* Version: 1.0.2
* Author: Meta 13
* Author URI: http://www.meta13.com
* License: GPL2
*/

include 'classes/saved.searches.class.php';
include 'classes/tracking.class.php';
include 'classes/jobs.class.php';
include 'classes/mandrill-api-php/src/Mandrill.php';
include 'functions/custom_functions.php';

function job_register_taxonomy() {
    // Job Source
    $labels = array(
        'name'              => 'Source',
        'singular_name'     => 'Source',
        'search_items'      => 'Search Sources',
        'all_items'         => 'All Sources',
        'edit_item'         => 'Edit Source',
        'update_item'       => 'Update Source',
        'add_new_item'      => 'Add New Source',
        'new_item_name'     => 'New Source',
        'menu_name'         => 'Sources'
    );

    register_taxonomy('job_source', 'jobs', array(
        'hierarchical' => true,
        'labels' => $labels,
        'query_var' => true,
        'show_admin_column' => true
    ));

    // Job Companies
    $labels = array(
        'name'              => 'Company',
        'singular_name'     => 'Company',
        'search_items'      => 'Search Companines',
        'all_items'         => 'All Companies',
        'edit_item'         => 'Edit Company',
        'update_item'       => 'Update Company',
        'add_new_item'      => 'Add New Company',
        'new_item_name'     => 'New Company',
        'menu_name'         => 'Companies'
    );

    register_taxonomy('job_company', 'jobs', array(
        'hierarchical' => true,
        'labels' => $labels,
        'query_var' => true,
        'show_admin_column' => true
    ));

    // Job Types
    $labels = array(
        'name'              => 'Type(s)',
        'singular_name'     => 'Type',
        'search_items'      => 'Search Types',
        'all_items'         => 'All Types',
        'edit_item'         => 'Edit Type',
        'update_item'       => 'Update Type',
        'add_new_item'      => 'Add New Type',
        'new_item_name'     => 'New Type',
        'menu_name'         => 'Types'
    );

    register_taxonomy('job_type', 'jobs', array(
        'hierarchical' => true,
        'labels' => $labels,
        'query_var' => true,
        'show_admin_column' => true
    ));

    // Job Featured/Blocked
    $labels = array(
        'name'              => 'Featured/Blocked',
        'singular_name'     => 'Featured/Blocked',
        'search_items'      => 'Search Featured/Blocked',
        'all_items'         => 'All Featured/Blocked',
        'edit_item'         => 'Edit Featured/Blocked',
        'update_item'       => 'Update Featured/Blocked',
        'add_new_item'      => 'Add New Featured/Blocked',
        'new_item_name'     => 'New Featured/Blocked',
        'menu_name'         => 'Featured/Blocked'
    );

    register_taxonomy('job_featured_blocked', 'jobs', array(
        'hierarchical' => true,
        'labels' => $labels,
        'query_var' => true,
        'show_admin_column' => true
    ));


    // Job Cities
    $labels = array(
        'name'              => 'City',
        'singular_name'     => 'City',
        'search_items'      => 'Search Cities',
        'search_items'      => 'Search Cities',
        'all_items'         => 'All Cities',
        'edit_item'         => 'Edit City',
        'update_item'       => 'Update City',
        'add_new_item'      => 'Add New City',
        'new_item_name'     => 'New City',
        'menu_name'         => 'Cities'
    );

    register_taxonomy('job_city', 'jobs', array(
        'hierarchical' => true,
        'labels' => $labels,
        'query_var' => true,
        'show_admin_column' => true
    ));
}
add_action('init', 'job_register_taxonomy');

// Limit site search to only post and page, not jobs
function searchfilter($query) {
    if ($query->is_search && !is_admin() ) {
        $query->set('post_type', array('post','page'));
    }

    return $query;
}
add_filter('pre_get_posts','searchfilter');

// Assigned templated to job pages and track clicks/views...
add_filter('template_include', 'portfolio_page_template', 9999);
function portfolio_page_template($template)
{
    $post_id = get_the_ID();
    $post_type = get_post_type($post_id);

    #global $wp;
    #$current_slug = add_query_arg( array(), $wp->request );
    #if( $current_slug == 'jobs' ) {
    #    $post_type = 'jobs';
    #}

    if ($post_type == 'jobs' || isset($_GET['s'])) {
        $template_type = @end(explode('/', $template));
        if ($template_type == 'content.php' && !isset($_GET['s'] ) ) {
            // detail page

            $additional_details = get_post_custom(get_the_ID());

            $Tracking = new Tracking();
            $Tracking->trackJobView($additional_details['Job Id'][0], $post_id);

            $job_template = plugin_dir_path(__FILE__) . 'templates/jobs-single.php';
            return $job_template;
        } else {
            // job listing
            if(strpos($_SERVER['REQUEST_URI'], 'jobs') !== false) {
                $job_template = plugin_dir_path(__FILE__) . 'templates/jobs-listing.php';
                return $job_template;
            } else {
                return $template;
            }
            #$job_template = plugin_dir_path(__FILE__) . 'templates/jobs-listing.php';

        }
    }

    if (is_page('jobs-import')) {
        $job_template = plugin_dir_path(__FILE__) . 'templates/jobs-import.php';
        return $job_template;
    }

    if(is_page('featured-jobs')) {
        $job_template = plugin_dir_path(__FILE__) . 'templates/job-featured.php';
        return $job_template;
    }

    return $template;
}

// Created custom post types
add_action('init', 'create_post_type');
function create_post_type()
{
    //create jobs custom post type
    register_post_type('jobs', array('labels'=>array('name'=>__('Jobs'), 'singular_name'=>__('Job')), 'public'=>true, 'has_archive'=>true, 'supports'=>array('title', 'editor', 'custom-fields'))); //'capabilities'=>array('create_posts'=>'do_not_allow')

    //create employers custom post type
    //Employer -> Title, Description, URL, Logo
    $labels = array(
        'name'  => _x('Employers', 'post type general name'),
        'singular_name' => _x('Employer', 'post type singular name'),
        'add_new' => _x('Add New', 'Employer'),
        'add_new_item' => __('Add New Employer'),
        'edit_item' => __('Edit Employer'),
        'new_item' => __('New Employer'),
        'view_item' => __('View Employer'),
        'search_items' => __('Search Employers'),
        'not_found' => __('No Employers Found'),
        'not_found_in_trash' => __('Nothing found in Trash')
        );
    $args = array(
        'labels' => $labels,
        'description' => 'Holds featured employers',
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'publicly_queryable' => false,
        'menu_position' => 56
        );
    register_post_type('employers', $args);
}

//Remove custom taxonomies from admin menu
add_action('admin_menu', 'remove_custom_taxonomies_menu', 999);

function remove_custom_taxonomies_menu()
{
    //remove featured/blocked
    remove_submenu_page('edit.php?post_type=jobs', 'edit-tags.php?taxonomy=job_featured_blocked&amp;post_type=jobs');
    //remove sources
    remove_submenu_page('edit.php?post_type=jobs', 'edit-tags.php?taxonomy=job_source&amp;post_type=jobs');
    //remove companies
    // remove_submenu_page('edit.php?post_type=jobs', 'edit-tags.php?taxonomy=job_company&amp;post_type=jobs');
    //remove types
    remove_submenu_page('edit.php?post_type=jobs', 'edit-tags.php?taxonomy=job_type&amp;post_type=jobs');
    //remove cities
    remove_submenu_page('edit.php?post_type=jobs', 'edit-tags.php?taxonomy=job_city&amp;post_type=jobs');
}


//Add Employer URL custom field
add_action('admin_init', 'add_employer_url_custom_field');
function add_employer_url_custom_field()
{
    // add_meta_box('employer_url_meta', 'Employer URL', 'employer_url', 'employers', 'normal', 'low');
}
function employer_url()
{
    global $post;
    $custom = get_post_custom($post->ID);
    $employer_url = $custom["employer_url"][0]; ?>
    <label>URL</label>
    <input name="employer_url" value="<?php echo $employer_url; ?>" size="100" />
    <?php
}
//save Employer URL field to employer post
add_action('save_post', 'save_employer_url');
function save_employer_url()
{
    global $post;
    $post_type = @get_post_type($post->ID);
    if ("employers" == $post_type) {
        update_post_meta($post->ID, "employer_url", $_POST["employer_url"]);
    }
}


// Add the JS file and register for ajax
function theme_name_scripts()
{
    $Jobs = new Jobs();

    list($page, $params) = array_pad(explode('?', $_SERVER['REQUEST_URI'], 2), 2, null);
    if($page == '/jobs/' || $page == '/jobs') {
        wp_enqueue_script('jobs-filters-ajax', plugins_url('/jobs/js/filters.js'), true);
        wp_localize_script('jobs-filters-ajax', 'jfs_vars', array('ajaxurl' => admin_url('admin-ajax.php'),'security' => wp_create_nonce($Jobs->ajax_key)));
    }
    wp_enqueue_style( 'jobs-style', '/wp-content/plugins/jobs/css/jobs.css', array(), _S_VERSION );
    wp_enqueue_script('script-name', '/wp-content/plugins/jobs/js/jobs.js', array('jquery'), '1.0.0', true);
    wp_localize_script('script-name', 'jfs_vars', array('ajaxurl' => admin_url('admin-ajax.php'),'security' => wp_create_nonce($Jobs->ajax_key)));

    // Auto Complete
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_register_script('my-autocomplete', plugins_url('/jobs/js/my-autocomplete.js'), array( 'jquery', 'jquery-ui-autocomplete' ), '1.0', false);
    wp_localize_script('my-autocomplete', 'MyAutocomplete', array( 'url' => admin_url('admin-ajax.php') ));
    wp_enqueue_script('my-autocomplete');
}
add_action('wp_enqueue_scripts', 'theme_name_scripts');

// Ajax call backs
function job_click()
{
    $Jobs = new Jobs();
    check_ajax_referer($Jobs->ajax_key, 'security');

    $Tracking = new Tracking();
    $Tracking->trackJobClick($_POST['job_id'], $_POST['post_id']);
    die();
}
add_action("wp_ajax_nopriv_job_click", "job_click"); // Non logged in users
add_action("wp_ajax_job_click", "job_click"); // Logged in users. Kind of odd, but maybe remove this so employees don't count?

// Add admin pages
function admin_menu_job_reports()
{
    add_menu_page(
        __('Job Reports', 'textdomain'),
        'Job Reports',
        'manage_options',
        'jobs/pages/admin-job-report.php',
        '',
        '',
        55
    );

    add_submenu_page('jobs/pages/admin-job-report.php', 'Jobs Viewed Report', 'Jobs Viewed Report', 'manage_options', 'jobs/pages/admin-jobs-viewed-report.php');
    add_submenu_page('jobs/pages/admin-job-report.php', 'Jobs Clicked Report', 'Jobs Clicked Report', 'manage_options', 'jobs/pages/admin-jobs-clicked-report.php');
    add_submenu_page('jobs/pages/admin-job-report.php', 'Companies Report', 'Companies Report', 'manage_options', 'jobs/pages/admin-companies-report.php');
    add_submenu_page('jobs/pages/admin-job-report.php', 'Historic Tracking Report', 'Historic Tracking Report', 'manage_options', 'jobs/pages/admin-historic-tracking-report.php');
    add_submenu_page('jobs/pages/admin-job-report.php', 'Duration Report', 'Duration Report', 'manage_options', 'jobs/pages/admin-duration-report.php');
    add_submenu_page('jobs/pages/admin-job-report.php', 'Search Term Report', 'Search Term Report', 'manage_options', 'jobs/pages/admin-search-term-report.php');
}
add_action('admin_menu', 'admin_menu_job_reports');

// Custom Post Status
function wpdocs_custom_post_status()
{
    register_post_status('expired_jobs', array(
        'label'                     => _x('Expired Jobs', 'post'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Expired Jobs <span class="count">(%s)</span>', 'Expired Jobs <span class="count">(%s)</span>'),
    ));
}
add_action('init', 'wpdocs_custom_post_status');

// CSV Export
add_action('admin_post_search-term-report.csv', 'admin_search_term_report');
function admin_search_term_report()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $filehandler = fopen("php://output", 'w');

    $Tracking = new Tracking();

    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;

    $from_date_query = '1900-01-01';
    $to_date_query = date('Y-m-d');

    if ($from_date) {
        $from_date_object = DateTime::createFromFormat('m/d/Y', $from_date);
        if (is_object($from_date_object)) {
            $from_date_query = $from_date_object->format('Y-m-d');
        }
    }

    if ($to_date) {
        $to_date_object = DateTime::createFromFormat('m/d/Y', $to_date);
        if (is_object($to_date_object)) {
            $to_date_query = $to_date_object->format('Y-m-d');
        }
    }

    $search_terms = $Tracking->fetchSearchWords($from_date_query, $to_date_query);

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=search_term_report.csv');
    header('Pragma: no-cache');

    $row = array('Search Term', 'Count');
    fputcsv($filehandler, $row, ',', '"');

    foreach ($search_terms as $term) {
        $row = array($term['term'], $term['total_count']);
        fputcsv($filehandler, $row, ',', '"');
    }

    fclose($filehandler);
}
add_action('admin_post_jobs-clicked-report.csv', 'jobs_clicked_report');
function jobs_clicked_report()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $filehandler = fopen("php://output", 'w');

    $Tracking = new Tracking();
    $top_ten_jobs_clicked = $Tracking->fetchJobsClicked(null, null);

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=jobs_clicked_report.csv');
    header('Pragma: no-cache');

    $row = array('# of Clicks', 'Job', 'Job #', 'Company');
    fputcsv($filehandler, $row, ',', '"');

    foreach ($top_ten_jobs_clicked as $jobs_click) {
        $row = array($jobs_click['total_jobs'], $jobs_click['post_title'], $jobs_click['job_id'], $jobs_click['company_name']);
        fputcsv($filehandler, $row, ',', '"');
    }

    fclose($filehandler);
}
add_action('admin_post_jobs-companies-report.csv', 'jobs_companies_report');
function jobs_companies_report()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $filehandler = fopen("php://output", 'w');

    $Jobs = new Jobs();
    $top_ten_jobs_clicked = $Jobs->fetchEmployersByJobsListed('listings', 'DESC');

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=jobs_companies_report.csv');
    header('Pragma: no-cache');

    $row = array('Company', '# of Listings', '# of Views', '# of Clicks');
    fputcsv($filehandler, $row, ',', '"');

    foreach ($top_ten_jobs_clicked as $jobs_click) {
        $row = array($jobs_click['company_name'], $jobs_click['total_jobs'], isset($jobs_click['jobs_view']) ? $jobs_click['jobs_view'] : 0, isset($jobs_click['jobs_click']) ? $jobs_click['jobs_click'] : 0);
        fputcsv($filehandler, $row, ',', '"');
    }

    fclose($filehandler);
}

add_action('admin_post_duration-report.csv', 'duration_report');
function duration_report()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $filehandler = fopen("php://output", 'w');

    $Tracking = new Tracking();
    $Jobs = new Jobs();

    $filter_by = isset($_POST['filter_by']) ? $_POST['filter_by'] : null;
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;
    $q = isset($_POST['q']) ? $_POST['q'] : null;
    $job_type = isset($_POST['job_type']) ? $_POST['job_type'] : null;

    $from_date_query = '1900-01-01';
    $to_date_query = date('Y-m-d');

    if ($from_date) {
        $from_date_object = DateTime::createFromFormat('m/d/Y', $from_date);
        if (is_object($from_date_object)) {
            $from_date_query = $from_date_object->format('Y-m-d');
        }
    }

    if ($to_date) {
        $to_date_object = DateTime::createFromFormat('m/d/Y', $to_date);
        if (is_object($to_date_object)) {
            $to_date_query = $to_date_object->format('Y-m-d');
        }
    }

    switch ($filter_by) {
        case 'Job Type':
            $historic_data = $Tracking->fetchJobsArchiveByDurationJobType($from_date_query, $to_date_query, $job_type);
            break;
        case 'Company Name':
            $historic_data = $Tracking->fetchJobsArchiveByDurationCompanyName($from_date_query, $to_date_query, $q);
            break;
        case 'Job Title':
            $historic_data = $Tracking->fetchJobsArchiveByDurationJobTitle($from_date_query, $to_date_query, $q);
            break;
    }

    $jobs = array();
    $gscjs_job_ids = array();
    foreach ($historic_data as $hd) {
        $gscjs_job_ids[] = $hd['id'];
        $jobs[$hd['job_id']] = $hd;
    }

    $job_ids = array_keys($jobs);

    if (!empty($job_ids)) {
        $clicks = $Tracking->fetchJobsClickedByJobs($job_ids);
        foreach ($clicks as $click) {
            if (isset($jobs[$click['job_id']])) {
                $jobs[$click['job_id']]['clicks'] = $click['total_jobs'];
            }
        }

        $views = $Tracking->fetchJobsViewedByJobs($job_ids);
        foreach ($views as $view) {
            if (isset($jobs[$view['job_id']])) {
                $jobs[$view['job_id']]['viewed'] = $view['total_jobs'];
            }
        }

        $gscjs_job_id_to_job = array_column($jobs, 'job_id', 'id');
        $types = $Jobs->fetchJobTypesByJobs($gscjs_job_ids);
        foreach ($types as $type) {
            if (isset($gscjs_job_id_to_job[$type['gscjs_job_id']])) {
                $jobs[$gscjs_job_id_to_job[$type['gscjs_job_id']]]['types'][] = $type['type'];
            }
        }
    }

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=duration_report.csv');
    header('Pragma: no-cache');

    $row = array('Job Title', 'Company', 'Type', 'Duration', 'Clicks', 'Viewed');
    fputcsv($filehandler, $row, ',', '"');

    foreach ($jobs as $j) {
        $types = !empty($j['types']) ? implode(', ', $j['types']) : '';
        $clicks = isset($j['clicks']) ? $j['clicks'] : 0;
        $viewed = isset($j['viewed']) ? $j['viewed'] : 0;

        $row = array($j['title'], $j['company'], $types, $j['duration'], $clicks, $viewed);
        fputcsv($filehandler, $row, ',', '"');
    }

    fclose($filehandler);
}

add_action('admin_post_historic-tracking-report.csv', 'historic_tracking_report');
function historic_tracking_report()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $filehandler = fopen("php://output", 'w');

    $Tracking = new Tracking();

    $filter_by = isset($_POST['filter_by']) ? $_POST['filter_by'] : null;
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;

    $from_date_query = '1900-01-01';
    $to_date_query = date('Y-m-d');

    if ($from_date) {
        $from_date_object = DateTime::createFromFormat('m/d/Y', $from_date);
        if (is_object($from_date_object)) {
            $from_date_query = $from_date_object->format('Y-m-d');
        }
    }

    if ($to_date) {
        $to_date_object = DateTime::createFromFormat('m/d/Y', $to_date);
        if (is_object($to_date_object)) {
            $to_date_query = $to_date_object->format('Y-m-d');
        }
    }

    switch ($filter_by) {
        case 'Job Type':
            $historic_data = $Tracking->fetchJobsArchiveByJobType($from_date_query, $to_date_query);
            break;
        case 'Company Name':
            $historic_data = $Tracking->fetchJobsArchiveByCompanyName($from_date_query, $to_date_query);
            break;
        case 'Feed Source':
            $historic_data = $Tracking->fetchJobsArchiveByFeedSource($from_date_query, $to_date_query);
            break;
        case 'City':
            $historic_data = $Tracking->fetchJobsArchiveByCity($from_date_query, $to_date_query);
            break;
    }

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=historic_tracking_report.csv');
    header('Pragma: no-cache');

    $row = array($filter_by, 'Total');
    fputcsv($filehandler, $row, ',', '"');

    foreach ($historic_data as $hd) {
        $row = array($hd['filter'], $hd['total_jobs']);
        fputcsv($filehandler, $row, ',', '"');
    }

    fclose($filehandler);
}
// 20211123 - Added brandon@daytamarketing.com as email recipient
function send_daily_email() {
    $Tracking = new Tracking();

    $date = new DateTime();
    $today = $date->format('Y-m-d');

    $jobs_removed = $Tracking->fetchJobLogByDateAction($today, 'remove');
    $jobs_added = $Tracking->fetchJobLogByDateAction($today, 'add');

    $body = 'Date: ' . $today;
    $body .= "\n" . 'Jobs Added: ' . count($jobs_added);
    $body .= "\n" . 'Jobs Removed: ' . count($jobs_removed);

    #$mandrill = new Mandrill('IQ2e3HOu9J5Z4PkXrnaLNA');
    $to_emails = array('nathan@meta13.com', 'justin@meta13.com', 'brandon@daytamarketing.com');
    foreach($to_emails as $to) {
        wp_mail( $to, 'stcloudshines.com - Daily Job Report', $body );


        #$message = array(
        #    'html'=>$body,
        #    'text'=>$body,
        #    'subject'=>'ottertaillakescountry.com - Daily Job Report',
        #    'from_email'=>'noreply@ottertaillakescountry.com',
        #    'from_name'=>'ottertaillakescountry.com',
        #    'to' => array(
        #        array(
        #            'email'=>$to,
        #            'name'=>$to,
        #            'type'=>'to'
        #        )
        #    ),
        #    'headers'=>array('Reply-To'=>'noreply@ottertaillakescountry.com'),
        #);
        #$result = $mandrill->messages->send($message, false);

    }
    exit;
}
add_action('send_daily_email', 'send_daily_email');

if(isset($_GET['send_daily_email'])) {
    function send_daily_email_function() {
        do_action('send_daily_email');
    }
    add_action('init', 'send_daily_email_function');
}

//Add session to store filters/page
// add_action('init', 'start_session', 1);
// function start_session() {
//    if ( !session_id() ) {
//        session_start();
//    }
// }
// add_action('wp_logout', 'end_session');
// add_action('wp_login', 'end_session');
// function end_session() {
//    session_destroy();
// }

//Ajax call back for jobs-listing filtering
function show_filtered_jobs()
{
    $hasFiltered = false;

    //populate arrays with incoming data from ajax
    $company_filters = json_decode(stripslashes($_POST['company_filters']));
    $source_filters = json_decode(stripslashes($_POST['source_filters']));
    $type_filters = json_decode(stripslashes($_POST['type_filters']));
    $city_filters = json_decode(stripslashes($_POST['city_filters']));
    $featured = json_decode(stripslashes($_POST['featured']));
    $paged = json_decode(stripslashes($_POST['page_number']));

    $search_term = null;
    if ($_POST['search_term']) {
        $search_term = trim($_POST['search_term']);
        $hasFiltered = true;
    }

     //unset($_SESSION['search']);
     $_SESSION['search'][$search_term] = array('company_filters'=>$company_filters);
     $_SESSION['search'][$search_term]['company_filters'] = $company_filters;
     $_SESSION['search'][$search_term]['source_filters'] = $source_filters;
     $_SESSION['search'][$search_term]['type_filters'] = $type_filters;
     $_SESSION['search'][$search_term]['city_filters'] = $city_filters;
     $_SESSION['search'][$search_term]['page_number'] = $paged;

     $_SESSION['search'][$search_term]['company_filters'] = array_map( 'trim', $_SESSION['search'][$search_term]['company_filters'] );
     $_SESSION['search'][$search_term]['source_filters'] = array_map( 'trim', $_SESSION['search'][$search_term]['source_filters'] );
     $_SESSION['search'][$search_term]['type_filters'] = array_map( 'trim', $_SESSION['search'][$search_term]['type_filters'] );
     $_SESSION['search'][$search_term]['city_filters'] = array_map( 'trim', $_SESSION['search'][$search_term]['city_filters'] );
     $_SESSION['search'][$search_term]['page_number'] = array_map( 'trim', $_SESSION['search'][$search_term]['page_number'] );

    //create taxonomy array based on filter arrays
    //initialize array if no data
    $tax_query = array();

    //if featured, add featured-employer term to tax_query
    if ($featured == 1){
        $tax_query[] = array('taxonomy' => 'job_featured_blocked', 'field' => 'slug', 'terms' => array('featured', 'featured-employer'));
    }

    //populate array if filters are selected
    if (!empty($company_filters)) {
        $tax_query[] = array('taxonomy' => 'job_company', 'field' => 'name', 'terms' => $company_filters);
        $hasFiltered = true;
    }
    if (!empty($source_filters)) {
        $tax_query[] = array('taxonomy' => 'job_source', 'field' => 'name', 'terms' => $source_filters);
        $hasFiltered = true;
    }
    if (!empty($type_filters)) {
        $tax_query[] = array('taxonomy' => 'job_type', 'field' => 'name', 'terms' => $type_filters);
        $hasFiltered = true;
    }
    if (!empty($city_filters)) {
        $tax_query[] = array('taxonomy' => 'job_city', 'field' => 'name', 'terms' => $city_filters);
        $hasFiltered = true;
    }

    $args = array( 'posts_per_page' => 10, 'post_type' => 'jobs', 'post_status'=>'publish', 'order' => 'DESC', 'tax_query' => $tax_query, 'paged' => $paged, 's'=>$search_term);
    $filtered_jobs_query = new WP_Query($args);

    $post_count = $filtered_jobs_query->found_posts;

    if ($filtered_jobs_query->have_posts()):

        if(function_exists('custom_pagination')) {
            $pagination = custom_pagination($filtered_jobs_query->max_num_pages, "", $paged);
        }

        ?>
        <div class="job-results__jobs--top">


            <?php if($search_term): ?>
                <h2>Displaying results for: <strong><?php print htmlspecialchars($search_term); ?></strong></h2>
                <h3><?php echo $post_count; ?> Results</h3>
            <?php endif; ?>

            <input type="hidden" id="page_num" value="<?php print $paged; ?>" />
            <?php print isset($pagination['displaying']) ? $pagination['displaying'] : ''; ?>

            <?php if($hasFiltered): ?>
                <div id="saved-search-div">
                    <!--<form method="get" id="saved-search" class="searchform" action="">-->
                        <label class="screen-reader-text" for="saved-search-email" style="display:none;">Saved Searches</label>
                        <input type="text" id="saved-search-email" placeholder="Email Address">
                        <button type="button" id="saved-search-button" class="job-results-search__icon" value="Search">Save Search</button>
                    <!--</form>-->
                </div>
            <?php endif; ?>
        </div>
        <?php
        while ($filtered_jobs_query->have_posts()) : $filtered_jobs_query->the_post(); ?>
        <?php echo '<!-- get_page_template' . basename( get_page_template() ) . ' -->'; ?>
             <div class="job-results__jobs--listing">
                <h2><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h2>
                <?php $company_term = wp_get_post_terms(get_the_ID(), 'job_company'); ?>
                <p class="job-meta">
                    <?php $company_term = wp_get_post_terms(get_the_ID(), 'job_company'); ?>
                    <span class="post_company"><?php echo isset($company_term[0]) ? $company_term[0]->name : ''; ?></span> &bull; <?php $job_city = wp_get_post_terms(get_the_ID(), 'job_city'); ?>
                    <span class="post_city"><?php echo isset($job_city[0]) ? $job_city[0]->name : ''; ?></span>, MN<br>
                    <?php $job_source = wp_get_post_terms(get_the_ID(), 'job_source'); ?>
                    <span class="post_source"><?php echo $job_source[0]->name; ?></span> &bull; <span class="post_date"><?php echo get_the_date( 'F j, Y' ); ?></span>
                </p>
                <?php //the_excerpt(); ?>
                <?php echo jobs_listing_excerpt(); ?>
                <p><a href="<?php the_permalink(); ?>">View Listing</a></p>
            </div>
        <?php endwhile; ?>

        <!-- pagination-->
        <?php print isset($pagination['pages']) ? $pagination['pages'] : ''; ?>
        <?php wp_reset_postdata(); ?>
    <?php else: ?>
        <p><?php _e('Sorry, no jobs matched your criteria. Please adjust your filtering to broaden your search.'); ?></p>
    <?php endif; ?>

    <?php
    die();
}
add_action("wp_ajax_nopriv_show_filtered_jobs", "show_filtered_jobs");
add_action("wp_ajax_show_filtered_jobs", "show_filtered_jobs");

function create_saved_search()
{
    $SavedSearches = new SavedSearches();
    $SavedSearches->createSavedSearch($_POST['email'], json_encode($_POST));
    die();
}
add_action("wp_ajax_nopriv_create_saved_search", "create_saved_search");
add_action("wp_ajax_create_saved_search", "create_saved_search");

function saved_search($atts)
{
    $msg = '';
    $SavedSearches = new SavedSearches();

    if (isset($_GET['verify'])) {
        $SavedSearches->activateSavedSearch($_GET['id'], $_GET['code']);
        $msg = 'Your saved search has been activated.';
    } elseif (isset($_GET['stop'])) {
        $SavedSearches->deleteSavedSearch($_GET['id'], $_GET['code']);
        $msg = 'Your saved search has been removed.';
    }

    return $msg;
}
add_shortcode('saved-search', 'saved_search');

function saved_search_cron()
{
    // Send out Emails Nighly
    $SavedSearches = new SavedSearches();
    $SavedSearches->deleteOldSavedSearch();
    $saved_searches = $SavedSearches->fetchSavedSearches();

    foreach ($saved_searches as $saved_search) {
        $sd = json_decode($saved_search['search_data'], true);

        // find results and send out email here
        $company_filters = json_decode(stripslashes($sd['company_filters']));
        $source_filters = json_decode(stripslashes($sd['source_filters']));
        $type_filters = json_decode(stripslashes($sd['type_filters']));
        $city_filters = json_decode(stripslashes($sd['city_filters']));

        $tax_query = array();
        if (!empty($company_filters)) {
            $tax_query[] = array('taxonomy' => 'job_company', 'field' => 'name', 'terms' => $company_filters);
        }
        if (!empty($source_filters)) {
            $tax_query[] = array('taxonomy' => 'job_source', 'field' => 'name', 'terms' => $source_filters);
        }
        if (!empty($type_filters)) {
            $tax_query[] = array('taxonomy' => 'job_type', 'field' => 'name', 'terms' => $type_filters);
        }
        if (!empty($city_filters)) {
            $tax_query[] = array('taxonomy' => 'job_city', 'field' => 'name', 'terms' => $city_filters);
        }

        $search_term = null;
        if ($sd['search_term']) {
            $search_term = trim($sd['search_term']);
        }

        $body = '<h4>Jobs</h4>';

        #if( empty( $tax_query ) ) {
        #    $args = array('posts_per_page'=>100, 'post_type'=>'jobs', 'post_status'=>'publish', 'orderby'=>'title', 'order'=>'ASC', 's'=>$search_term);
        #} else {
        #    $args = array('posts_per_page'=>100, 'post_type'=>'jobs', 'post_status'=>'publish', 'orderby'=>'title', 'order'=>'ASC', 'tax_query'=>$tax_query, 's'=>$search_term);
        #}

        global $wpdb;
        $job_ids = [];
        $jobs = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM wp_posts WHERE ( post_title LIKE %s OR post_content LIKE %s ) AND post_type ='jobs' AND post_status = 'publish' ORDER BY post_modified DESC LIMIT 200", '%' . $search_term . '%', '%' . $search_term . '%' ) );
        foreach( $jobs as $j ) {
            $job_ids[] = $j->ID;
        }

        $args = array (
            'post_type'              => 'jobs',
            'post_status'            => 'publish',
            'pagination'             => true,
            'paged'                  => 1,
            'posts_per_page'         => 100,
            'order'                  => 'DESC',
            'orderby'                => 'date',
            'tax_query'              => $tax_query,
            'post__in'               => $job_ids
        );

        $filtered_jobs_query = new WP_Query($args);
        #$body .= $filtered_jobs_query->request;

        if ($filtered_jobs_query->have_posts()) {
            while ($filtered_jobs_query->have_posts()) {
                $filtered_jobs_query->the_post();
                $body .= '<a href="'. get_the_permalink() .'">' . get_the_title() . '</a><br>';
            }
        }


        $body .= '<br><a href="https://ottertaillakescountry.com/saved-search?stop=1&code='.$saved_search['code'].'&id='.$saved_search['id'].'">Click here to stop this saved serach</a>';

        $SavedSearches->sendEmail($saved_search['email'], 'Otter Tail Lakes Country - Saved Search', $body);
    }
}
add_action('saved_search_cron', 'saved_search_cron');
if( isset($_GET['ss'])) {
    do_action('saved_search_cron');
}

function my_search()
{
    $term = strtolower($_GET['term']);
    $suggestions = array();

    $args = array('post_type' => 'jobs', 's'=>$term);
    #$loop = new WP_Query('s=' . $term);
    $loop = new WP_Query($args);

    while ($loop->have_posts()) {
        $loop->the_post();
        $suggestion = array();
        $suggestion['label'] = get_the_title();
        $suggestion['link'] = get_permalink();

        $suggestions[] = $suggestion;
    }

    wp_reset_query();


    $response = json_encode($suggestions);
    echo $response;
    exit();
}

add_action('wp_ajax_my_search', 'my_search');
add_action('wp_ajax_nopriv_my_search', 'my_search');

function featured_employer_update_jobs_cron()
{
    //remove all fe checkboxes
    $remove_posts = get_posts(
            array(
                'post_type' => 'jobs',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'job_featured_blocked',
                        'field' => 'slug',
                        'terms' => 'featured-employer'
                    )
                )
            )
        );
        foreach ($remove_posts as $rpost) {
            wp_remove_object_terms( $rpost->ID, 'featured-employer', 'job_featured_blocked');
        }

        global $wpdb;

        //grab all featured employers' job posts
        $job_ids = [];
        $jobs = $wpdb->get_results( "SELECT DISTINCT wpm.meta_value FROM wp_posts AS wp
                         INNER JOIN wp_postmeta AS wpm
                         ON wp.ID = wpm.post_id
                         WHERE wp.post_type = 'employers'
                         AND meta_key = 'featured_employer_link' AND wp.post_status = 'publish'"
        );
        foreach( $jobs as $job ) {
            if( is_numeric( $job->meta_value ) ) {
                $job_ids[] = $job->meta_value;
            } else {
                $ids = unserialize( $job->meta_value );
                foreach( $ids as $id ) {
                    $job_ids[] = $id;
                }
            }
        }

        if( !empty( $job_ids ) ) {
            $featured_jobs_by_featured_employer = $wpdb->get_results(
                "SELECT wp.ID FROM wp_posts AS wp
                             INNER JOIN wp_term_relationships AS wtr ON wp.ID = wtr.object_id
                             INNER JOIN wp_term_taxonomy AS wtt ON wtr.term_taxonomy_id = wtt.term_taxonomy_id
                             WHERE term_id IN (" . implode( ',', $job_ids ) . ")
                             AND wp.post_status = 'publish'"
            );
        }

        //mark jobs Featured Employer (custom taxonomy)
        foreach ($featured_jobs_by_featured_employer as $fjbfe) {
           wp_set_object_terms ( $fjbfe->ID, 'featured-employer', 'job_featured_blocked', true);
        }


}
add_action('featured_employer_update_jobs_cron', 'featured_employer_update_jobs_cron');

function jobs_single_content(){
    return get_the_content();
    #$content = substr(get_the_content(), 0, 250);
    #$content .= '...';
    #return $content;
}
function jobs_listing_excerpt(){
    $excerpt = substr(get_the_excerpt(), 0, 250);
    $excerpt .= '...';
    return $excerpt;
}

// Get the jobs from the feed
function import_jobs_nightly() {
    $page_size = 100;
    $tracking = new Tracking();
    $tracking->truncateJobActive();

    $date = new DateTime( 'now' );
    $date = $date->modify( '-1 year' )->format( 'Y-m-d' );

    $functions = [ 1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21 ];
    foreach( $functions as $f ) {
        $response = json_decode( file_get_contents( 'https://tnrp-api.gartner.com/wantedapi/v5.0/jobs?passkey=3781a96203adb92b6225trpf8aded89&responsetype=json&pagesize=1&pageindex=1&date='.$date.'&county=27111&Function=' . $f ), true );

        if( !isset( $response['response'] ) ) {
            continue;
        }

        if( !isset( $response['response']['numfound'] ) || $response['response']['numfound'] <= 0 ) {
            continue;
        }

        $page_index = 1;
        $number_of_jobs = $response['response']['numfound'];
        $number_of_pages = ceil( $number_of_jobs / $page_size );
        while( $page_index <= $number_of_pages ) {
            $response = json_decode( file_get_contents( 'https://tnrp-api.gartner.com/wantedapi/v5.0/jobs?passkey=3781a96203adb92b6225trpf8aded89&responsetype=json&pagesize='.$page_size.'&pageindex='.$page_index.'&date='.$date.'&county=27111&descriptiontype=long&Function=' . $f ), true );

            $jobs = $response['response']['jobs']['job'];
            foreach( $jobs as $job ) {
                $tracking->insertJobActive( $job['id'], $job['hash'], json_encode( $job ) );
            }

            $page_index++;
        }
    }
    exit;
}
add_action('import_jobs_nightly', 'import_jobs_nightly');

// To run the job manully
if(isset($_GET['import_jobs_nightly'])) {
    function import_jobs_nightly_function() {
        do_action('import_jobs_nightly');
    }
    add_action('init', 'import_jobs_nightly_function');
}

// Create/Update/Remove the jobs after they have been grabbed from the feed
function import_jobs() {
    global $wpdb;
    $jobs = new Jobs();
    $tracking = new Tracking();

    // Add Jobs
    $job_ids = [];
    $job_hashes = [];

    $rows = $jobs->fetchJobIdsActive();
    foreach( $rows as $row ) {
        $job_ids[$row['meta_value']] = $row['ID'];
    }

    $rows = $jobs->fetchJobHashesActive();
    foreach( $rows as $row ) {
        $job_hashes[$row['ID']] = $row['meta_value'];
    }

    $active_job_count = $tracking->fetchJobActive();
    if( count($active_job_count) <= 10 ) {
        print 'No active jobs found. Don\'t Import';
        exit;
    }

    $job_rows = $tracking->fetchJobActiveNeededToImport();
    foreach( $job_rows as $job ) {
        if( isset( $job_ids[$job['job_id']] ) ) {
            // Job is active
            if( isset( $job_hashes[$job_ids[$job['job_id']]] ) && $job_hashes[$job_ids[$job['job_id']]] == $job['job_hash'] ) {
                // Job is already active and the hash matches, lets skip
                $tracking->importedJobActive( $job['job_id'], $job_ids[$job['job_id']] );
                continue;
            } else {
                // Job is already active, but the hash doesn't match, lets update.
                $post_id = add_job( json_decode( $job['data'], true ), $job_ids[$job['job_id']] );
                $tracking->importedJobActive( $job['job_id'], $post_id );
            }
        } else {
            $previous_job = $tracking->fetchJobsArchiveByJobId( $job['job_id'] );
            if( empty( $previous_job ) ) {
                // job not found. lets add it.
                $post_id = add_job( json_decode( $job['data'], true ) );
                $tracking->importedJobActive( $job['job_id'], $post_id );
            } else {
                // job was found, but is not active. let's make it active.
                $is_post = $jobs->fetchJobPostById( $previous_job['post_id'] );
                if( empty( $is_post ) ) {
                    $post_id = add_job( json_decode( $job['data'], true ) );
                    $tracking->importedJobActive( $job['job_id'], $post_id );
                } else {
                    $post_id = add_job( json_decode( $job['data'], true ), $previous_job['post_id'] );
                    $tracking->importedJobActive( $job['job_id'], $previous_job['post_id'] );
                    $tracking->insertJobLog( $job['job_id'], $job['job_hash'], $previous_job['post_id'], 'reactivate' );
                }
            }
        }
    }

    // Remove Jobs
    $job_ids = [];
    $rows = $jobs->fetchJobIdsActive();
    foreach( $rows as $row ) {
        $job_ids[$row['meta_value']] = $row['ID'];
    }

    $active_job_ids = [];
    $active_jobs = $tracking->fetchJobActive();
    foreach( $active_jobs as $aj ) {
        $active_job_ids[] = $aj['job_id'];
    }

    foreach( $job_ids as $job_id=>$post_id) {
        if( !in_array( $job_id, $active_job_ids ) ) {
            $post_hash = $tracking->fetchJobsHashByPostId( $post_id );
            $hash = isset( $post_hash['meta_value'] ) ? $post_hash['meta_value'] : '';

            $post_data = [
                'ID'                =>  $post_id,
                'post_status'       =>  'expired_jobs'
            ];
            wp_update_post( $post_data );

            $tracking->insertJobLog( $job_id, $hash, $post_id, 'remove' );
            $wpdb->update( 'gscjs_job_archive', [ 'expired_date'=>date( 'Y-m-d' ) ], [ 'job_id'=>$job_id ] );
        }
    }

    exit;
}

add_action('import_jobs', 'import_jobs');

// To run the job manully
if(isset($_GET['import_jobs'])) {
    function import_jobs_function() {
        do_action('import_jobs');
    }
    add_action('init', 'import_jobs_function');
}

function add_job( $job, $post_id = null )  {
    global $wpdb;
    $tracking = new Tracking();

    $type = null;
    $author_id = 1;
    $title = $job['title']['value'];
    $post_date = $job['dates']['posted'];
    $source = $job['sources']['source'][0]['name'];
    $job_company = $job['employer']['name'];
    $types = $job['jobtypes']['jobtype'];
    $job_city = $job['locations']['location'][0]['city']['label'];

    if( !$post_id ) {
        $action = 'add';

        $post_id = wp_insert_post(
            array(
                'comment_status'    =>  'closed',
                'ping_status'       =>  'closed',
                'post_author'       =>  $author_id,
                'post_content'      =>  $job['description']['value'],
                'post_date'         =>  $post_date,
                'post_date_gmt'     =>  get_gmt_from_date( $post_date ),
                'post_title'        =>  $title,
                'post_status'       =>  'publish',
                'post_type'         =>  'jobs'
            )
        );
    } else {
        $action = 'update';

        $post_data = [
            'ID'           => $post_id,
            'post_content'      =>  $job['description']['value'],
            'post_date'         =>  $post_date,
            'post_date_gmt'     =>  get_gmt_from_date( $post_date ),
            'post_title'        =>  $title,
            'post_status'       =>  'publish',
            'post_type'         =>  'jobs'
        ];

        wp_update_post( $post_data );
    }

    $job_archive = array('post_id'=>$post_id, 'post_date'=>$post_date, 'title'=>$title, 'hash'=>$job['hash'], 'job_id'=>$job['id']);
    $job_archive_types = array();

    // job source
    if ($source != '') {
        $term = term_exists($source, 'job_source');
        if (empty($term)) {
            $term = wp_insert_term($source, 'job_source');

            if($term->errors) {
                $term_id = $term->error_data['term_exists'];
            } else {
                $term_id = $term['term_id'];
            }
        } else {
            $term_id = $term['term_id'];
        }

        wp_set_post_terms($post_id, array($term_id), 'job_source');

        $job_archive['source'] = $source;
    }

    // job company
    if ($job_company != '') {
        $term = term_exists($job_company, 'job_company');

        if(empty($term)) {
            $term = wp_insert_term($job_company, 'job_company');

            if($term->errors) {
                $term_id = $term->error_data['term_exists'];
            } else {
                $term_id = $term['term_id'];
            }
        } else {
            $term_id = $term['term_id'];
        }
        wp_set_post_terms($post_id, array($term_id), 'job_company');

        $job_archive['company'] = $job_company;
    }

    // job types
    foreach ($types as $key=>$type) {
        $append = ( $key == 0 ) ? false : true;

        $type = trim($type['label']);
        if ($type != '') {
            $term = term_exists($type, 'job_type');
            if (empty($term)) {
                $term = wp_insert_term($type, 'job_type');
            }
            wp_set_post_terms($post_id, array($term['term_id']), 'job_type', $append);
            $job_archive_types[] = $type;
        }
    }

    // job cities
    if ($job_city != '') {
        $term = term_exists($job_city, 'job_city');
        if (empty($term)) {
            $term = wp_insert_term($job_city, 'job_city');
        }
        wp_set_post_terms($post_id, array($term['term_id']), 'job_city');

        $job_archive['city'] = $job_city;
    }

    // meta values
    if (!add_post_meta($post_id, 'Job Id', $job['id'], true)) {
        update_post_meta($post_id, 'Job Id', $job['id']);
        $job_archive['job_id'] = $job['id'];
    }

    if (!add_post_meta($post_id, 'Job URL', $job['sources']['source'][0]['url'], true)) {
        update_post_meta($post_id, 'Job URL', $job['sources']['source'][0]['url']);
        $job_archive['url'] = $job['sources']['source'][0]['url'];
    }

    if (!add_post_meta($post_id, 'Job Salary', $job['salaries']['salary']['0']['value'], true)) {
        update_post_meta($post_id, 'Job Salary', $job['salaries']['salary']['0']['value']);
        $job_archive['salary'] = $job['salaries']['salary']['0']['value'];
    }

    if (!add_post_meta($post_id, 'Job State', $job['locations']['location'][0]['state']['label'], true)) {
        update_post_meta($post_id, 'Job State', $job['locations']['location'][0]['state']['label']);
        $job_archive['state'] = $job['locations']['location'][0]['state']['label'];
    }

    if (!add_post_meta($post_id, 'Job Lat', $job['locations']['location'][0]['position']['latitude'], true)) {
        update_post_meta($post_id, 'Job Lat', $job['locations']['location'][0]['position']['latitude']);
    }

    if (!add_post_meta($post_id, 'Job Lng', $job['locations']['location'][0]['position']['longitude'], true)) {
        update_post_meta($post_id, 'Job Lng', $job['locations']['location'][0]['position']['longitude']);
    }

    if (!add_post_meta($post_id, 'Job County', $job['locations']['location'][0]['county']['label'], true)) {
        update_post_meta($post_id, 'Job County', $job['locations']['location'][0]['county']['label']);
        $job_archive['county'] = (string)$j->AdCounty;
    }

    if (!add_post_meta($post_id, 'Job Education', $job['education']['label'], true)) {
        update_post_meta($post_id, 'Job Education', $job['education']['label']);
        $job_archive['education'] = $job['education']['label'];
    }

    if (!add_post_meta($post_id, 'Job Skills', $job['skillslist']['skill'][0]['name'], true)) {
        update_post_meta($post_id, 'Job Skills', $job['skillslist']['skill'][0]['name']);
        $job_archive['skills'] = $job['skillslist']['skill'][0]['name'];
    }

    if (!add_post_meta($post_id, 'Job Hash', $job['hash'], true)) {
        update_post_meta($post_id, 'Job Hash', $job['hash']);
    }

    if( $action == 'add' ) {
        $wpdb->insert( 'gscjs_job_archive', $job_archive );
        $job_archive_id = $wpdb->insert_id;

        if (!empty($job_archive_types)) {
            foreach ($job_archive_types as $job_archive_type) {
                $data = array( 'gscjs_job_id'=>$job_archive_id, 'type'=>$job_archive_type );
                $wpdb->insert( 'gscjs_job_archive_types', $data );
            }
        }

        $tracking->insertJobLog( $job['id'], $job['hash'], $post_id, 'add' );
    } else {
        $job_archive['expired_date'] = null;
        $wpdb->update( 'gscjs_job_archive', $job_archive, [ 'job_id'=>$job['id'] ] );

        $job_archive = $tracking->fetchJobsArchiveByJobId( $job['id'] );
        $job_archive_id = $job_archive['id'];

        $wpdb->delete( 'gscjs_job_archive_types', [ 'gscjs_job_id'=>$job_archive_id ], [ '%s' ] );
        if (!empty($job_archive_types)) {
            foreach ($job_archive_types as $job_archive_type) {
                $data = array( 'gscjs_job_id'=>$job_archive_id, 'type'=>$job_archive_type );
                $wpdb->insert( 'gscjs_job_archive_types', $data );
            }
        }

        $tracking->insertJobLog( $job['id'], $job['hash'], $post_id, 'update' );
    }

    return $post_id;
}

// Job Cleanup
function job_cleanup() {
    global $wpdb;
    $jobs = $wpdb->get_results( 'SELECT * FROM gscjs_job_archive WHERE expired_date < DATE_SUB( NOW(), INTERVAL 1 YEAR ) AND expired_date != "0000-00-00" ORDER BY expired_date' );
    foreach( $jobs as $job ) {
        if( wp_delete_post( $job->post_id ) ) {
            $wpdb->delete( 'gscjs_tracking_job_view', [ 'post_id'=>$job->post_id ], [ '%d' ] );
            $wpdb->delete( 'gscjs_tracking_job_click', [ 'post_id'=>$job->post_id ], [ '%d' ] );
            $wpdb->delete( 'gscjs_job_log', [ 'post_id'=>$job->post_id ], [ '%d' ] );
            $wpdb->delete( 'gscjs_job_archive_types', [ 'gscjs_job_id'=>$job->id ], [ '%d' ] ); // this is wrong, the gscjs_job_id i beleive is the id from job_archive
            $wpdb->delete( 'gscjs_job_archive', [ 'post_id'=>$job->post_id ], [ '%d' ] );
        }
    }
}
add_action('job_cleanup', 'job_cleanup');
/**/
?>