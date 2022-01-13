<?php
global $wpdb;

if( isset( $_GET['action'] ) && $_GET['action'] == 'import_jobs_nightly_new' ) {
    do_action( 'import_jobs_nightly_new' );
}

$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'listings';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

$company_sort = ($orderby == 'company') ? ($order == 'DESC') ? 'ASC' : 'DESC' : 'ASC';
$listings_sort = ($orderby == 'listings') ? ($order == 'DESC') ? 'ASC' : 'DESC' : 'DESC';
$views_sort = ($orderby == 'views') ? ($order == 'DESC') ? 'ASC' : 'DESC' : 'DESC';
$clicks_sort = ($orderby == 'clicks') ? ($order == 'DESC') ? 'ASC' : 'DESC' : 'DESC';

$Jobs = new Jobs();
$top_ten_jobs_clicked = $Jobs->fetchEmployersByJobsListed($orderby, $order);
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Companies Report', 'textdomain' ); ?></h1>

    <form method="post" action="<?php print admin_url('admin-post.php?action=jobs-companies-report.csv'); ?>">
        <p class="search-box"><input type="submit" id="search-submit" class="button" value="Export"></p>
    </form>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-primary sortable <?php print ($company_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $company_sort; ?>&orderby=company"><span>Company</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($listings_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $listings_sort; ?>&orderby=listings"><span># of Listings</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($views_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $views_sort; ?>&orderby=views"><span># of Views</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($clicks_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $clicks_sort; ?>&orderby=clicks"><span># of Clicks</span><span class="sorting-indicator"></span></a></th>
        </tr>
        </thead>
        <tbody id="the-list">
            <?php foreach($top_ten_jobs_clicked as $jobs_click): ?>
                <tr id="post-209" class="iedit author-other level-0 post-209 type-jobs status-publish hentry job_source-monster job_company-headway-workforce-solutions job_type-full-time job_type-permanent">
                    <td class="title column-title"><?php print $jobs_click['company_name']; ?></td>
                    <td class="title column-title"><?php print $jobs_click['total_jobs']; ?></td>
                    <td class="title column-title"><?php print isset($jobs_click['jobs_view']) ? $jobs_click['jobs_view'] : 0; ?></td>
                    <td class="title column-title"><?php print isset($jobs_click['jobs_click']) ? $jobs_click['jobs_click'] : 0; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-primary sortable <?php print ($company_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $company_sort; ?>&orderby=company"><span>Company</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($listings_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $listings_sort; ?>&orderby=listings"><span># of Listings</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($views_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $views_sort; ?>&orderby=views"><span># of Views</span><span class="sorting-indicator"></span></a></th>
            <th scope="col" class="manage-column column-primary sortable <?php print ($clicks_sort == 'ASC') ? 'desc' : 'asc'; ?>"><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=<?php print $clicks_sort; ?>&orderby=clicks"><span># of Clicks</span><span class="sorting-indicator"></span></a></th>
        </tr>
        </tfoot>
    </table>
</div>