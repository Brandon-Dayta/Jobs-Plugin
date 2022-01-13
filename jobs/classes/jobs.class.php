<?php
class Jobs {

    public $ajax_key;

    function __construct() {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->ajax_key = 'kd83))@038@kds9835ds+23';
    }

    public function fetchTop10EmployersByJobsListed() {
        $employers = $this->wpdb->get_results('SELECT wt.name, count(wtr.object_id) AS total_jobs
                                          FROM wp_terms AS wt
                                          INNER JOIN wp_term_taxonomy AS wtt ON wt.term_id = wtt.term_id
                                          INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                          INNER JOIN wp_posts AS wp ON wtr.object_id = wp.ID
                                          WHERE wtt.taxonomy = "job_company" AND wp.post_status = "publish"
                                          GROUP BY wt.term_id
                                          ORDER BY total_jobs DESC, wt.name
                                          DESC LIMIT 10', ARRAY_A);
        return $employers;
    }

    public function fetchEmployersByJobsListed($orderby, $order) {
        $sql = 'SELECT wt.term_id, wt.name AS company_name, count(wtr.object_id) AS total_jobs
                                          FROM wp_terms AS wt
                                          INNER JOIN wp_term_taxonomy AS wtt ON wt.term_id = wtt.term_id
                                          INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                          INNER JOIN wp_posts AS wp ON wtr.object_id = wp.ID
                                          WHERE wtt.taxonomy = "job_company" AND wp.post_status = "publish"
                                          GROUP BY wt.term_id';

        switch($orderby) {
            case 'company':
                $sql .= ' ORDER BY company_name ' . $order . ', total_jobs';
                break;
            case 'listings':
                $sql .= ' ORDER BY total_jobs ' . $order . ', company_name';
                break;
        }

        $employers = $this->wpdb->get_results($sql, ARRAY_A);

        $sort_views = array();
        $sort_clicks = array();
        $employers_return = array();
        foreach($employers as $e) {
            $employers_return[$e['term_id']] = $e;
            $sort_views[$e['term_id']] = 0;
            $sort_clicks[$e['term_id']] = 0;
        }

        $term_ids = array_keys($employers_return);

        if(!empty($term_ids)) {
            $sql = 'SELECT wt.term_id, SUM(gtjv.count) AS total_jobs
                                              FROM wp_terms AS wt
                                              INNER JOIN wp_term_taxonomy AS wtt ON wt.term_id = wtt.term_id
                                              INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                              INNER JOIN gscjs_tracking_job_view AS gtjv ON wtr.object_id = gtjv.post_id
                                              INNER JOIN wp_posts AS wp ON gtjv.post_id = wp.ID
                                              WHERE wtt.taxonomy = "job_company" AND wt.term_id IN ('.implode(',', $term_ids).') AND wp.post_status = "publish"
                                              GROUP BY wt.term_id';
            $job_views = $this->wpdb->get_results($sql, ARRAY_A);
            foreach($job_views as $jv) {
                $sort_views[$jv['term_id']] = $jv['total_jobs'];
                $employers_return[$jv['term_id']]['jobs_view'] = $jv['total_jobs'];
            }

            $sql = 'SELECT wt.term_id, SUM(gtjc.count) AS total_jobs
                                              FROM wp_terms AS wt
                                              INNER JOIN wp_term_taxonomy AS wtt ON wt.term_id = wtt.term_id
                                              INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                              INNER JOIN gscjs_tracking_job_click AS gtjc ON wtr.object_id = gtjc.post_id
                                              INNER JOIN wp_posts AS wp ON gtjc.post_id = wp.ID
                                              WHERE wtt.taxonomy = "job_company" AND wt.term_id IN ('.implode(',', $term_ids).') AND wp.post_status = "publish"
                                              GROUP BY wt.term_id';
            $job_views = $this->wpdb->get_results($sql, ARRAY_A);
            foreach($job_views as $jv) {
                $sort_clicks[$jv['term_id']] = $jv['total_jobs'];
                $employers_return[$jv['term_id']]['jobs_click'] = $jv['total_jobs'];
            }
        }

        switch($orderby) {
            case 'views':
                $sort_order = ($order == 'ASC') ? SORT_ASC : SORT_DESC;
                array_multisort($sort_views, $sort_order, SORT_NUMERIC, $employers_return);
                break;
            case 'clicks':
                $sort_order = ($order == 'ASC') ? SORT_ASC : SORT_DESC;
                array_multisort($sort_clicks, $sort_order, SORT_NUMERIC, $employers_return);
                break;
        }

        return $employers_return;
    }

    public function fetchJobHashes() {
        $current_jobs = array();
        $sql = 'SELECT wp.ID, wpm.meta_value
                FROM wp_postmeta AS wpm
                INNER JOIN wp_posts AS wp ON wp.ID = wpm.post_id
                WHERE wpm.meta_key = "Job Hash"';
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function fetchJobHashesActive() {
        $current_jobs = array();
        $sql = 'SELECT wp.ID, wpm.meta_value
                FROM wp_postmeta AS wpm
                INNER JOIN wp_posts AS wp ON wp.ID = wpm.post_id
                WHERE wpm.meta_key = "Job Hash" AND wp.post_status = "publish"';
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function fetchJobIdsActive() {
        $current_jobs = array();
        $sql = 'SELECT wp.ID, wpm.meta_value
                FROM wp_postmeta AS wpm
                INNER JOIN wp_posts AS wp ON wp.ID = wpm.post_id
                WHERE wpm.meta_key = "Job Id" AND wp.post_status = "publish"';
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function fetchActiveJobHashes() {
        $current_jobs = array();
        $sql = 'SELECT pm.meta_value, pm.post_id
                FROM wp_postmeta AS pm
                INNER JOIN wp_posts AS p ON pm.post_id = p.ID
                WHERE pm.meta_key = "Job Hash" AND p.post_status = "publish"';
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function fetchPostByJobHashes($hash) {
        $sql = $this->wpdb->prepare('SELECT post_id FROM wp_postmeta WHERE meta_key = "Job Hash" AND meta_value = %s', $hash);
        return $this->wpdb->get_col($sql);
    }

    /*
    public function fetchJobTypesByJobs($post_ids) {
        $sql = $this->wpdb->prepare('SELECT wtr.object_id, wt.name
                                        FROM wp_term_relationships AS wtr
                                        INNER JOIN wp_term_taxonomy AS wtt ON wtr.term_taxonomy_id = wtt.term_taxonomy_id
                                        INNER JOIN wp_terms AS wt ON wtt.term_id = wt.term_id
                                        WHERE wtr.object_id IN (' . implode(',', $post_ids) . ')
                                        AND wtt.taxonomy = \'job_type\'
                                        ORDER BY wt.name');
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }
    */
    public function fetchJobTypesByJobs($gscjs_job_ids) {
        $sql = 'SELECT gscjs_job_id, type
                      FROM gscjs_job_archive_types
                      WHERE gscjs_job_id IN (' . implode(',', $gscjs_job_ids) . ')
                      ORDER BY type';
        $jobs = $this->wpdb->get_results($sql, ARRAY_A);
        return $jobs;
    }

    public function fetchJobPostById( $post_id ) {
        $sql = $this->wpdb->prepare( 'SELECT * FROM wp_posts WHERE ID = %d', $post_id );
        return $this->wpdb->get_row( $sql, ARRAY_A );
    }
}
?>