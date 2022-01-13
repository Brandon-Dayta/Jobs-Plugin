<?php
#include 'classes/tracking.class.php';
#include 'classes/jobs.class.php';

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
wp_enqueue_style( 'jquery-ui' );

$Tracking = new Tracking();

$filter_by = isset($_POST['filter_by']) ? $_POST['filter_by'] : null;
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;

if($filter_by) {
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

    switch($filter_by) {
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
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Historic Tracking Report', 'textdomain' ); ?></h1>
    <p>This report is generated based on number of jobs posted by period and: Job Type, Company Name, Feed Source, or City.</p>

    <form method="post" action="<?php print admin_url('/admin.php?page=jobs/pages/admin-historic-tracking-report.php'); ?>">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top">From Date</label>
                <input type="text" class="datepicker" name="from_date" value="<?php print htmlentities($from_date, ENT_QUOTES); ?>">
            </div>

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top">To Date</label>
                <input type="text" class="datepicker" name="to_date" value="<?php print htmlentities($to_date, ENT_QUOTES); ?>">
            </div>

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Filter By</label>
                <select name="filter_by" id="filter_by" class="postform">
                    <option value="">Filter By</option>
                    <option value="Job Type" <?php print ($filter_by == 'Job Type') ? 'selected="selected"' : ''; ?>>Job Type</option>
                    <option value="Company Name" <?php print ($filter_by == 'Company Name') ? 'selected="selected"' : ''; ?>>Company Name</option>
                    <option value="Feed Source" <?php print ($filter_by == 'Feed Source') ? 'selected="selected"' : ''; ?>>Feed Source</option>
                    <option value="City" <?php print ($filter_by == 'City') ? 'selected="selected"' : ''; ?>>City</option>
                </select>
            </div>

            <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">

            <br class="clear">
        </div>
    </form>

    <?php if($filter_by): ?>
        <?php if(!empty($historic_data)): ?>
             <form method="post" action="<?php print admin_url('admin-post.php?action=historic-tracking-report.csv'); ?>">
                <input type="hidden" name="from_date" value="<?php print htmlentities($from_date, ENT_QUOTES); ?>">
                <input type="hidden" name="to_date" value="<?php print htmlentities($to_date, ENT_QUOTES); ?>">
                <input type="hidden" name="filter_by" value="<?php print htmlentities($filter_by, ENT_QUOTES); ?>">
                <p class="search-box"><input type="submit" id="search-submit" class="button" value="Export"></p>
            </form>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <th scope="col" class="manage-column"><?php print $filter_by; ?></th>
                    <th scope="col" class="manage-column">Total</th>
                </tr>
                </thead>
                <tbody id="the-list">
                    <?php foreach($historic_data as $hd): ?>
                        <tr>
                            <td class="title column-title"><?php print $hd['filter']; ?></td>
                            <td class="title column-title"><?php print $hd['total_jobs']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th scope="col" class="manage-column"><?php print $filter_by; ?></th>
                    <th scope="col" class="manage-column">Total</th>
                </tr>
                </tfoot>
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