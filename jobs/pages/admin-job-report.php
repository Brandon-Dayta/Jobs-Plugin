<?php
#print 'inmport';
#do_action('job_cleanup');
#exit;

$Tracking = new Tracking();
$Jobs = new Jobs();

$top_ten_jobs_views = $Tracking->fetchTop10JobsViewed();
$top_ten_jobs_clicked = $Tracking->fetchTop10JobsClicked();
$top_ten_employers_by_jobs_listed = $Jobs->fetchTop10EmployersByJobsListed();
$top_ten_employers_by_jobs_views = $Tracking->fetchTop10EmployersByJobsViewed();
?>
<h1 class="wp-heading-inline"><?php esc_html_e('Jobs Dashboard', 'textdomain'); ?></h1>

<h4><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-viewed-report.php">Top 10 Jobs Viewed</a></h4>
<ul>
    <?php foreach($top_ten_jobs_views as $top_ten_jobs_view): ?>
        <li><?php print $top_ten_jobs_view['total_jobs']; ?> - <?php print $top_ten_jobs_view['post_title']; ?> (<?php print $top_ten_jobs_view['job_id']; ?>)</li>
    <?php endforeach; ?>
</ul>

<h4><a href="/wp-admin/admin.php?page=jobs/pages/admin-jobs-clicked-report.php">Top 10 Jobs Clicked</a></h4>
<ul>
    <?php foreach($top_ten_jobs_clicked as $top_ten_jobs_click): ?>
        <li><?php print $top_ten_jobs_click['total_jobs']; ?> - <?php print $top_ten_jobs_click['post_title']; ?> (<?php print $top_ten_jobs_click['job_id']; ?>)</li>
    <?php endforeach; ?>
</ul>

<h4><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php">Top 10 Employers By Jobs Listed</a></h4>
<ul>
    <?php foreach($top_ten_employers_by_jobs_listed as $top_ten_employers_by_jobs_list): ?>
        <li><?php print $top_ten_employers_by_jobs_list['total_jobs']; ?> - <?php print $top_ten_employers_by_jobs_list['name']; ?></li>
    <?php endforeach; ?>
</ul>

<h4><a href="/wp-admin/admin.php?page=jobs/pages/admin-companies-report.php&order=DESC&orderby=views">Top 10 Employers By Jobs Viewed</a></h4>
<ul>
    <?php foreach($top_ten_employers_by_jobs_views as $top_ten_employers_by_jobs_view): ?>
        <li><?php print $top_ten_employers_by_jobs_view['total_jobs']; ?> - <?php print $top_ten_employers_by_jobs_view['name']; ?></li>
    <?php endforeach; ?>
</ul>