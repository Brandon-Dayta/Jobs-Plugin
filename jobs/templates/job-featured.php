<?php
global $wpdb;
$return = null;
$posts_ids = array();

$job_ids = [-1];
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

$sql = '(SELECT wp.ID FROM wp_posts AS wp
        INNER JOIN wp_term_relationships AS wtr ON wp.ID = wtr.object_id
        INNER JOIN wp_term_taxonomy AS wtt ON wtr.term_taxonomy_id = wtt.term_taxonomy_id
        WHERE term_id IN (' . implode( ',', $job_ids ) . ') AND wp.post_status = "publish")
        UNION
        (
        SELECT ID FROM wp_posts WHERE ID IN(SELECT object_id FROM `wp_term_relationships` WHERE `term_taxonomy_id` = 2899) AND post_status = "publish"
        )';
$random_jobs = $wpdb->get_results($sql, ARRAY_A);

foreach($random_jobs as $rj) {
    $posts_ids[] = $rj['ID'];
}
$random_post_keys = array_rand($posts_ids, 6);

foreach($random_post_keys as $post_key) {
    $f_post = get_post($posts_ids[$post_key]);

    $company_term = wp_get_post_terms($f_post->ID, 'job_company');
    $job_city = wp_get_post_terms($f_post->ID, 'job_city');

    $company_name = isset($company_term[0]->name) ? $company_term[0]->name : '';
    $job_city = isset($job_city[0]->name) ? $job_city[0]->name : '';

    $return .= '<div class="featured-job et_pb_column et_pb_column_1_3"><h3 class="featured-job__title"><a href="' . get_post_permalink($f_post->ID) . '">' . get_the_title($f_post->ID) . '</a></h3><p class="featured-job__meta">' . $company_name .' &bull; ' . $job_city . ', MN</p><a href="' . get_post_permalink($f_post->ID) . '" class="featured-job__link">Learn More</a></div>';
}

print $return;
?>