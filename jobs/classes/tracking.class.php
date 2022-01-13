<?php
class Tracking {

    private $wpdb;
    private $current_date;

    function __construct() {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->current_date = date('Y-m-d');

    }

    // Import Job Log
    public function insertJobLog( $job_id, $job_hash, $post_id, $action ) {
        $sql = $this->wpdb->prepare('INSERT INTO gscjs_job_log (job_id, job_hash, post_id, action) VALUES (%s, %s, %d, %s)', $job_id, $job_hash, $post_id, $action);
        $this->wpdb->query($sql);
    }

    // Imported Jobs
    public function fetchImportedJobs() {
        $files = $this->wpdb->get_results('SELECT id, file, download_date FROM gscjs_imported_files WHERE type = "downloaded"', ARRAY_A);
        return $files;
    }
    public function fetchImportedJobsByUnzipped() {
        $files = $this->wpdb->get_results('SELECT id, file, download_date FROM gscjs_imported_files WHERE type = "unzipped"', ARRAY_A);
        return $files;
    }
    public function insertImportedJobs($file, $type) {
        $sql = $this->wpdb->prepare('INSERT INTO gscjs_imported_files (file, type) VALUES (%s, %s)', $file, $type);
        $this->wpdb->query($sql);
    }

    public function fetchJobLogByDateAction($date, $action) {
        $date = $date . '%';
        $jobs = $this->wpdb->get_results($this->wpdb->prepare('SELECT job_id FROM gscjs_job_log WHERE created_at LIKE %s AND action = %s', $date, $action), ARRAY_A);
        return $jobs;
    }

    // Job Views
    public function fetchTop10JobsViewed() {
        $jobs = $this->wpdb->get_results('SELECT tj.job_id, SUM(tj.count) AS total_jobs, wp.post_title
                                          FROM gscjs_tracking_job_view AS tj
                                          INNER JOIN wp_posts AS wp ON tj.post_id = wp.ID
                                          WHERE wp.post_status = "publish"
                                          GROUP BY tj.job_id
                                          ORDER BY total_jobs DESC, wp.post_title
                                          DESC LIMIT 10', ARRAY_A);
        return $jobs;
    }
    public function fetchJobsViewed($order_by, $order) {
        $sql = 'SELECT gtjb.job_id, SUM(gtjb.count) AS total_jobs, wp.post_title, wt.name AS company_name
                                          FROM gscjs_tracking_job_view AS gtjb
                                          INNER JOIN wp_posts AS wp ON gtjb.post_id = wp.ID
                                          INNER JOIN wp_term_relationships AS wtr ON wp.ID = wtr.object_id
                                          INNER JOIN wp_term_taxonomy AS wtt ON wtr.term_taxonomy_id = wtt.term_taxonomy_id
                                          INNER JOIN wp_terms AS wt ON wtt.term_id = wt.term_id
                                          WHERE wtt.taxonomy = "job_company" AND wp.post_status = "publish"
                                          GROUP BY gtjb.job_id';
        switch($order_by) {
            case 'job':
                $sql .= ' ORDER BY post_title '.$order.', total_jobs DESC';
                break;
            case 'company':
                $sql .= ' ORDER BY company_name '.$order.', total_jobs DESC';
                break;
            default:
                $sql .= ' ORDER BY total_jobs '.$order.', wp.post_title';
        }

        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }
    public function fetchTop10EmployersByJobsViewed() {
        $jobs = $this->wpdb->get_results('SELECT wt.name, SUM(gtjv.count) AS total_jobs
                                          FROM wp_terms AS wt
                                          INNER JOIN wp_term_taxonomy AS wtt ON wt.term_id = wtt.term_id
                                          INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                          INNER JOIN gscjs_tracking_job_view AS gtjv ON wtr.object_id = gtjv.post_id
                                          INNER JOIN wp_posts AS wp ON gtjv.post_id = wp.ID
                                          WHERE wtt.taxonomy = "job_company" AND wp.post_status = "publish"
                                          GROUP BY wt.term_id
                                          ORDER BY total_jobs DESC, wt.name
                                          DESC LIMIT 10', ARRAY_A);
        return $jobs;

    }
    public function fetchJobsViewedByJobs($job_ids) {
         $jobs = $this->wpdb->get_results('SELECT tj.job_id, SUM(tj.count) AS total_jobs
                                          FROM gscjs_tracking_job_view AS tj
                                          INNER JOIN wp_posts AS wp ON tj.post_id = wp.ID
                                          GROUP BY tj.job_id', ARRAY_A);
        return $jobs;
    }
    public function trackJobView($job_id, $post_id) {
        $sql = $this->wpdb->prepare('INSERT INTO gscjs_tracking_job_view (tracking_date, job_id, post_id, count) VALUES (%s, %s, %d, 1) ON DUPLICATE KEY UPDATE count = (count + 1)', $this->current_date, $job_id, $post_id);
        $this->wpdb->query($sql);
    }

    // Jobs Clicked
    public function fetchTop10JobsClicked() {
        $jobs = $this->wpdb->get_results('SELECT tj.job_id, SUM(tj.count) AS total_jobs, wp.post_title
                                          FROM gscjs_tracking_job_click AS tj
                                          INNER JOIN wp_posts AS wp ON tj.post_id = wp.ID
                                          WHERE wp.post_status = "publish"
                                          GROUP BY tj.job_id
                                          ORDER BY total_jobs DESC, wp.post_title
                                          LIMIT 10', ARRAY_A);
        return $jobs;
    }
    public function fetchJobsClicked($order_by, $order) {
        $sql = 'SELECT tj.job_id, SUM(tj.count) AS total_jobs, wp.post_title, wt.name AS company_name
                                          FROM gscjs_tracking_job_click AS tj
                                          INNER JOIN wp_posts AS wp ON tj.post_id = wp.ID
                                          INNER JOIN wp_term_relationships AS wtr ON wp.ID = wtr.object_id
                                          INNER JOIN wp_term_taxonomy AS wtt ON wtr.term_taxonomy_id = wtt.term_taxonomy_id
                                          INNER JOIN wp_terms AS wt ON wtt.term_id = wt.term_id
                                          WHERE wtt.taxonomy = "job_company" AND wp.post_status = "publish"
                                          GROUP BY tj.job_id';
        switch($order_by) {
            case 'job':
                $sql .= ' ORDER BY post_title '.$order.', total_jobs DESC';
                break;
            case 'company':
                $sql .= ' ORDER BY company_name '.$order.', total_jobs DESC';
                break;
            default:
                $sql .= ' ORDER BY total_jobs '.$order.', wp.post_title';
        }

        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }
    public function fetchJobsClickedByJobs($job_ids) {
        $jobs = $this->wpdb->get_results('SELECT tj.job_id, SUM(tj.count) AS total_jobs, wp.post_title
                                          FROM gscjs_tracking_job_click AS tj
                                          INNER JOIN wp_posts AS wp ON tj.post_id = wp.ID
                                          WHERE tj.job_id IN (' . implode(',', $job_ids) . ')
                                          GROUP BY tj.job_id', ARRAY_A);
        return $jobs;
    }
    public function trackJobClick($job_id, $post_id) {
        $sql = $this->wpdb->prepare('INSERT INTO gscjs_tracking_job_click (tracking_date, job_id, post_id, count) VALUES (%s, %s, %d, 1) ON DUPLICATE KEY UPDATE count = (count + 1)', $this->current_date, $job_id, $post_id);
        $this->wpdb->query($sql);
    }

    // Historic Data
    public function fetchJobsArchiveByJobId( $job_id ) {
        $sql = $this->wpdb->prepare( 'SELECT * FROM gscjs_job_archive WHERE job_id = %s', $job_id );
        return $this->wpdb->get_row( $sql, ARRAY_A );
    }

    public function fetchJobsArchiveByJobType($from_date, $to_date) {
        $sql = $this->wpdb->prepare('SELECT COUNT(id) AS total_jobs, title AS filter
                                    FROM gscjs_job_archive
                                    WHERE (%s <= expired_date AND %s >= post_date)
                                    GROUP BY title
                                    ORDER BY total_jobs DESC, title', $from_date, $to_date);
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchJobsArchiveByCompanyName($from_date, $to_date) {
        $sql = $this->wpdb->prepare('SELECT COUNT(id) AS total_jobs, company AS filter
                                    FROM gscjs_job_archive
                                    WHERE (%s <= expired_date AND %s >= post_date)
                                    GROUP BY company
                                    ORDER BY total_jobs DESC, company', $from_date, $to_date);
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchJobsArchiveByFeedSource($from_date, $to_date) {
        $sql = $this->wpdb->prepare('SELECT COUNT(id) AS total_jobs, source AS filter
                                    FROM gscjs_job_archive
                                    WHERE (%s <= expired_date AND %s >= post_date)
                                    GROUP BY source
                                    ORDER BY total_jobs DESC, source', $from_date, $to_date);
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchJobsArchiveByCity($from_date, $to_date) {
        $sql = $this->wpdb->prepare('SELECT COUNT(id) AS total_jobs, city AS filter
                                    FROM gscjs_job_archive
                                    WHERE (%s <= expired_date AND %s >= post_date)
                                    GROUP BY city
                                    ORDER BY total_jobs DESC, city', $from_date, $to_date);
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchJobsArchiveByDurationJobTitle($from_date, $to_date, $title) {
        $sql = $this->wpdb->prepare('SELECT id, post_id, job_id, title, company, DATEDIFF(expired_date, post_date) AS duration
                                    FROM gscjs_job_archive
                                    WHERE (%s <= expired_date AND %s >= post_date)
                                    AND title LIKE %s
                                    ORDER BY duration DESC', $from_date, $to_date, '%' . $title . '%');
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchJobsArchiveByDurationCompanyName($from_date, $to_date, $company) {
        $sql = $this->wpdb->prepare('SELECT id, post_id, job_id, title, company, DATEDIFF(expired_date, post_date) AS duration
                                    FROM gscjs_job_archive
                                    WHERE (%s <= expired_date AND %s >= post_date)
                                    AND company LIKE %s
                                    ORDER BY duration DESC', $from_date, $to_date, '%' . $company . '%');
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchJobsArchiveByDurationJobType($from_date, $to_date, $job_type) {
        $sql = $this->wpdb->prepare('SELECT DISTINCT gja.id, gja.post_id, gja.job_id, gja.title, gja.company, DATEDIFF(gja.expired_date, gja.post_date) AS duration
                                    FROM gscjs_job_archive AS gja
                                    INNER JOIN gscjs_job_archive_types AS gjat ON gja.id = gjat.gscjs_job_id
                                    WHERE (%s <= gja.expired_date AND %s >= gja.post_date)
                                    AND gjat.type LIKE %s
                                    ORDER BY duration DESC', $from_date, $to_date, '%' . $job_type . '%');
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchDistinctJobTypesFromJobsArchive() {
        $sql = 'SELECT DISTINCT type FROM gscjs_job_archive_types ORDER BY type';
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    // Search tracking
    public function fetchSearchWords($from_date, $to_date) {
        $sql = $this->wpdb->prepare('SELECT SUM(count) AS total_count, term FROM gscjs_tracking_search_word
                                        WHERE tracking_date >= %s AND tracking_date <= %s
                                        GROUP BY term
                                        ORDER BY total_count DESC, term', $from_date, $to_date);
        $terms = $this->wpdb->get_results($sql, ARRAY_A);
        return $terms;
    }
    public function trackSearchWords($term) {
        $term = strtolower($term);
        $sql = $this->wpdb->prepare('INSERT INTO gscjs_tracking_search_word (tracking_date, term, count) VALUES (%s, %s, 1) ON DUPLICATE KEY UPDATE count = (count + 1)', $this->current_date, $term);
        $this->wpdb->query($sql);
    }

    // Job Active
    public function truncateJobActive() {
        $sql = $this->wpdb->prepare( 'TRUNCATE TABLE gscjs_job_active' );
        $this->wpdb->query($sql);
    }

    public function insertJobActive( $job_id, $job_hash, $data ) {
        $sql = $this->wpdb->prepare( 'INSERT INTO gscjs_job_active ( job_id, job_hash, data ) VALUES ( %s, %s, %s ) ', $job_id, $job_hash, $data );
        $this->wpdb->query($sql);
    }

    public function fetchJobActiveNeededToImport( ) {
        $sql = $this->wpdb->prepare('SELECT * FROM gscjs_job_active WHERE imported = %d', 0 );
        $rows = $this->wpdb->get_results( $sql, ARRAY_A );
        return $rows;
    }

    public function fetchJobActive() {
        $sql = $this->wpdb->prepare( 'SELECT * FROM gscjs_job_active', 0 );
        $rows = $this->wpdb->get_results( $sql, ARRAY_A );
        return $rows;
    }

    public function importedJobActive( $job_id, $post_id ) {
        $this->wpdb->query( $this->wpdb->prepare( 'UPDATE gscjs_job_active SET imported = %d, post_id = %d WHERE job_id = %s', 1, $post_id, $job_id ) );
    }

    // Wordpress Post
    public function getActiveWordpressPostJobs() {
        $sql = $this->wpdb->prepare( 'SELECT wp.ID, wpm.meta_value
                                      FROM wp_posts AS wp
                                      INNER JOIN wp_postmeta AS wpm ON wp.ID = wpm.post_id
                                      WHERE wp.post_type = %s AND wp.post_status = %s AND wpm.meta_key = %s', 'jobs', 'publish', 'Job Id' );
        $rows = $this->wpdb->get_results( $sql, ARRAY_A );
        return $rows;
    }

    public function fetchJobsHashByPostId( $post_id ) {
        $sql = $this->wpdb->prepare( 'SELECT * FROM wp_postmeta WHERE post_id = %d AND meta_key = %s', $post_id, 'Job Hash' );
        return $this->wpdb->get_row( $sql, ARRAY_A );
    }
}
?>