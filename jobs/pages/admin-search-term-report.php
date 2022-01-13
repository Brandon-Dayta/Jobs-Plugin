<?php
#include 'classes/tracking.class.php';
#include 'classes/jobs.class.php';

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
wp_enqueue_style( 'jquery-ui' );

$Tracking = new Tracking();

$search_terms = array();
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;

if(isset($_POST['filter_action'])) {
    $from_date_query = '1900-01-01';
    $to_date_query = date('Y-m-d');

    if($from_date) {
        $from_date_object = DateTime::createFromFormat('m/d/Y', $from_date);
        if(is_object($from_date_object)) {
            $from_date_query = $from_date_object->format('Y-m-d');
        }
    }

    if($to_date) {
        $to_date_object = DateTime::createFromFormat('m/d/Y', $to_date);
        if(is_object($to_date_object)) {
            $to_date_query = $to_date_object->format('Y-m-d');
        }
    }

    $search_terms = $Tracking->fetchSearchWords($from_date_query, $to_date_query);
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Search Term Report', 'textdomain'); ?></h1>
    <!--<p>This report allows you to serach by Type, Company Name, and Job Title that will then display the matching jobs and the duration they were listed on the site. Also inclues click through and view information.</p>-->

    <form method="post" action="<?php print admin_url('/admin.php?page=jobs/pages/admin-search-term-report.php'); ?>">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top">From Date</label>
                <input type="text" class="datepicker" name="from_date" value="<?php print htmlentities($from_date, ENT_QUOTES); ?>">
            </div>

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top">To Date</label>
                <input type="text" class="datepicker" name="to_date" value="<?php print htmlentities($to_date, ENT_QUOTES); ?>">
            </div>

            <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">

            <br class="clear">
        </div>
    </form>

    <?php if(isset($_POST['filter_action'])): ?>
        <?php if(!empty($search_terms)): ?>
             <form method="post" action="<?php print admin_url('admin-post.php?action=search-term-report.csv'); ?>">
                <input type="hidden" name="from_date" value="<?php print htmlentities($from_date, ENT_QUOTES); ?>">
                <input type="hidden" name="to_date" value="<?php print htmlentities($to_date, ENT_QUOTES); ?>">
                <p class="search-box"><input type="submit" id="search-submit" class="button" value="Export"></p>
            </form>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <th scope="col" class="manage-column">Search Term</th>
                    <th scope="col" class="manage-column">Count</th>
                </tr>
                </thead>
                <tbody id="the-list">
                    <?php foreach($search_terms as $term): ?>
                        <tr>
                            <td class="title column-title"><?php print ucwords($term['term']); ?></td>
                            <td class="title column-title"><?php print $term['total_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No Results Found.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    jQuery(function() {
        jQuery('.datepicker').datepicker();
    });
</script>