<?php
#include 'classes/tracking.class.php';
#include 'classes/jobs.class.php';

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
wp_enqueue_style( 'jquery-ui' );

$Tracking = new Tracking();
$Jobs = new Jobs();

$filter_by = isset($_POST['filter_by']) ? $_POST['filter_by'] : null;
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;
$q = isset($_POST['q']) ? $_POST['q'] : null;
$job_type = isset($_POST['job_type']) ? $_POST['job_type'] : null;

$job_types = $Tracking->fetchDistinctJobTypesFromJobsArchive();

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
    foreach($historic_data as $hd) {
        $gscjs_job_ids[] = $hd['id'];
        $jobs[$hd['job_id']] = $hd;
    }

    $job_ids = array_keys($jobs);

    if(!empty($job_ids)) {
        $clicks = $Tracking->fetchJobsClickedByJobs($job_ids);
        foreach($clicks as $click) {
            if(isset($jobs[$click['job_id']])) {
                $jobs[$click['job_id']]['clicks'] = $click['total_jobs'];
            }
        }

        $views = $Tracking->fetchJobsViewedByJobs($job_ids);
        foreach($views as $view) {
            if(isset($jobs[$view['job_id']])) {
                $jobs[$view['job_id']]['viewed'] = $view['total_jobs'];
            }
        }

        $gscjs_job_id_to_job = array_column($jobs, 'job_id', 'id');
        $types = $Jobs->fetchJobTypesByJobs($gscjs_job_ids);
        foreach($types as $type) {
            if(isset($gscjs_job_id_to_job[$type['gscjs_job_id']])) {
                $jobs[$gscjs_job_id_to_job[$type['gscjs_job_id']]]['types'][] = $type['type'];
            }
        }
    }
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Duration Report', 'textdomain'); ?></h1>
    <p>This report allows you to serach by Type, Company Name, and Job Title that will then display the matching jobs and the duration they were listed on the site. Also inclues click through and view information.</p>

    <form method="post" action="<?php print admin_url('/admin.php?page=jobs/pages/admin-duration-report.php'); ?>">
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
                    <option value="Job Title" <?php print ($filter_by == 'Job Title') ? 'selected="selected"' : ''; ?>>Job Title</option>
                </select>
            </div>

            <div class="alignleft actions bulkactions search-q" style="<?php print ($filter_by == 'Job Title' || $filter_by == 'Company Name') ? '' : 'display: none;'; ?>">
                <label for="bulk-action-selector-top"></label>
                <input type="text" name="q" value="<?php print htmlentities($q, ENT_QUOTES); ?>">
            </div>

            <div class="alignleft actions bulkactions search-job-type" style="<?php print ($filter_by == 'Job Type') ? '' : 'display: none;'; ?>">
                <label for="bulk-action-selector-top"></label>
                <select name="job_type">
                    <option value=""></option>
                    <?php foreach($job_types as $jt): ?>
                        <option value="<?php print htmlentities($jt['type'], ENT_QUOTES); ?>" <?php print ($job_type == $jt['type']) ? 'selected="selected"' : ''; ?>><?php print htmlentities($jt['type']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">

            <br class="clear">
        </div>
    </form>

    <?php if($filter_by): ?>
        <?php if(!empty($historic_data)): ?>
             <form method="post" action="<?php print admin_url('admin-post.php?action=duration-report.csv'); ?>">
                <input type="hidden" name="from_date" value="<?php print htmlentities($from_date, ENT_QUOTES); ?>">
                <input type="hidden" name="to_date" value="<?php print htmlentities($to_date, ENT_QUOTES); ?>">
                <input type="hidden" name="filter_by" value="<?php print htmlentities($filter_by, ENT_QUOTES); ?>">
                <input type="hidden" name="q" value="<?php print htmlentities($q, ENT_QUOTES); ?>">
                <input type="hidden" name="job_type" value="<?php print htmlentities($job_type, ENT_QUOTES); ?>">
                <p class="search-box"><input type="submit" id="search-submit" class="button" value="Export"></p>
            </form>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <th scope="col" class="manage-column">Job Title</th>
                    <th scope="col" class="manage-column">Company</th>
                    <th scope="col" class="manage-column">Type</th>
                    <th scope="col" class="manage-column">Duration</th>
                    <th scope="col" class="manage-column">Clicks</th>
                    <th scope="col" class="manage-column">Viewed</th>
                </tr>
                </thead>
                <tbody id="the-list">
                    <?php foreach($jobs as $j): ?>
                        <tr>
                            <td class="title column-title"><?php print $j['title']; ?></td>
                            <td class="title column-title"><?php print $j['company']; ?></td>
                            <td class="title column-title"><?php print !empty($j['types']) ? implode(', ', $j['types']) : ''; ?></td>
                            <td class="title column-title"><?php print $j['duration']; ?></td>
                            <td class="title column-title"><?php print isset($j['clicks']) ? $j['clicks'] : 0; ?></td>
                            <td class="title column-title"><?php print isset($j['viewed']) ? $j['viewed'] : 0; ?></td>
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

        jQuery('#filter_by').change(function() {
            jQuery('.search-q, .search-job-type').hide().val('');

            if(jQuery(this).val() == 'Job Title' || jQuery(this).val() == 'Company Name') {
                jQuery('.search-q').show();
            }

            if(jQuery(this).val() == 'Job Type') {
                jQuery('.search-job-type').show();
            }
        });

    });
</script>