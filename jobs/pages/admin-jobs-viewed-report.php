<?php
#include 'classes/tracking.class.php';
#include 'classes/jobs.class.php';

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'clicks';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

$clicks_sort = ($orderby == 'clicks') ? ($order == 'DESC') ? 'ASC' : 'DESC' : 'DESC';
$job_sort = ($orderby == 'job') ? ($order == 'DESC') ? 'ASC' : 'DESC' : 'ASC';
$company_sort = ($orderby == 'company') ? ($order == 'DESC') ? 'ASC' : 'DESC' : 'ASC';

$Tracking = new Tracking();
$top_ten_jobs_viewed = $Tracking->fetchJobsViewed($orderby, $order);
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Jobs Viewed Report', 'textdomain' ); ?></h1>

    <form method="post" action="<?php print admin_url('admin-post.php?action=jobs-clicked-report.csv'); ?>">
        <p class="search-box"><input type="submit" id="search-submit" class="button" value="Export"></p>
    </form>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-primary sortable <?php print ($clicks_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-viewed-report.php&order=<?php print $clicks_sort; ?>&orderby=clicks"><span># of Clicks</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($job_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-viewed-report.php&order=<?php print $job_sort; ?>&orderby=job"><span>Job</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column">Job #</th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($company_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-viewed-report.php&order=<?php print $company_sort; ?>&orderby=company"><span>Company</span><span class="sorting-indicator"></span></a></th>
        </tr>
        </thead>
        <tbody id="the-list">
            <?php foreach($top_ten_jobs_viewed as $jobs_view): ?>
                <tr id="post-209" class="iedit author-other level-0 post-209 type-jobs status-publish hentry job_source-monster job_company-headway-workforce-solutions job_type-full-time job_type-permanent">
                    <td class="title column-title"><?php print $jobs_view['total_jobs']; ?></td>
                    <td class="title column-title"><?php print $jobs_view['post_title']; ?></td>
                    <td class="title column-title"><?php print $jobs_view['job_id']; ?></td>
                    <td class="title column-title"><?php print $jobs_view['company_name']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-primary sortable <?php print ($clicks_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-viewed-report.php&order=<?php print $clicks_sort; ?>&orderby=clicks"><span># of Clicks</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($job_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-viewed-report.php&order=<?php print $job_sort; ?>&orderby=job"><span>Job</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column">Job #</th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($company_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-viewed-report.php&order=<?php print $company_sort; ?>&orderby=company"><span>Company</span><span class="sorting-indicator"></span></a></th>
        </tr>
        </tfoot>
    </table>
</div>