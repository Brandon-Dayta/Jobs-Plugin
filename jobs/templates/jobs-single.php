<?php
$search_term = '';
if (isset($_SESSION['search']) && !empty($_SESSION['search'])){
  $search_term = key($_SESSION['search']);
}
get_header();
?>
<div id="main-content">

    <!--<div class="job-results-search et_pb_section">-->
    <div class="job-results-search job-results-detail et_pb_section">
      <div class="et_pb_row job-results-search-hero">

        <div class="et_pb_column et_pb_column_1_3">
          <!--<img style="display:block;width:260px;max-width:80%;margin:0 auto 30px;" src="/wp-content/uploads/2020/07/logo-large-white.png" alt="Otter Tail Lakes Country"/>-->
        </div>
        <div class="et_pb_column et_pb_column_2_3">
            <!--<h1 class="job-results-search__title" style="color:#fff;">Thousands of jobs, all right here.</h1>-->
            <!-- Custom Search Form -->
            <form role="search" method="get" id="searchform" class="searchform" action="/jobs/">
                <div>
                    <div class="job-results-search__item">
                        <label class="screen-reader-text" for="s">Search Jobs Keyword:</label>
                        <input type="text" value="<?php print htmlspecialchars($s); ?>" name="s" id="s">
                        <!-- <input type="hidden" value="1" name="sentence">
                        <input type="hidden" value="jobs" name="post_type"> -->
                        <!-- <input type="hidden" value="product_cat" name="magazines,books" /> -->
                        <button type="submit" id="searchsubmit" class="job-results-search__icon" value="Search">Search Jobs</button>
                    </div>
            <!--<div class="job-results-search__item">
                        <label class="screen-reader-text" for="zip">Zip Code</label>
                        <input type="text" value="" name="zip" id="zip">
                    </div>
                    <div class="job-results-search__item">
                        <label class="screen-reader-text" for="miles">Miles</label>
                        <select name="miles">
                            <option value="">None Selected</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div> -->
                </div>
            <!--<div class="job-results-search__order">
                    <label class="screen-reader-text" for="order">Order results by:
                    <input type="radio" name="order" value="relevance" checked> Relevance
                    <input type="radio" name="order" value="date"> Listing Date
                </div> -->
            </form>
            <!-- End Custom Search Form -->
        </div>
      </div> <!-- .et_pb_row job-results-search-hero -->
      <div class="uabb-row-separator uabb-bottom-row-separator uabb-mul-triangles-separator uabb-has-svg" style="">
	    <svg class="uasvg-mul-triangles-separator" xmlns="http://www.w3.org/2000/svg" fill="#ffffff" opacity="1" width="100" height="8" preserveAspectRatio="none" viewBox="-670 197.1 1920 5.8"><polygon xmlns="http://www.w3.org/2000/svg" points="-670,197.1 -670,202.9 -664.153,197.5 -664.151,197.5 -658.513,202.801 -652.562,197.5 -646.923,202.801   -641.076,197.5 -635.334,202.801 -629.487,197.5 -623.743,202.9 -623.639,202.9 -617.688,197.5 -612.05,202.801 -606.203,197.5   -600.461,202.801 -594.614,197.5 -588.871,202.801 -583.024,197.5 -577.386,202.801 -571.54,197.5 -565.796,202.9 -565.587,202.9   -559.74,197.5 -553.999,202.801 -548.151,197.5 -542.513,202.801 -536.666,197.5 -530.923,202.801 -525.076,197.5 -519.333,202.9   -519.125,202.9 -513.278,197.5 -507.64,202.801 -501.688,197.5 -496.051,202.801 -490.204,197.5 -484.46,202.801 -478.613,197.5   -472.976,202.801 -467.024,197.5 -461.386,202.9 -461.177,202.9 -455.33,197.5 -449.587,202.801 -443.741,197.5 -437.998,202.801   -432.151,197.5 -426.512,202.801 -420.665,197.5 -414.923,202.9 -414.715,202.9 -408.868,197.5 -403.125,202.801 -397.277,197.5   -391.64,202.801 -385.793,197.5 -380.05,202.801 -374.203,197.5 -368.46,202.801 -362.613,197.5 -356.871,202.9 -356.767,202.9   -350.815,197.5 -345.177,202.801 -339.331,197.5 -333.588,202.801 -327.741,197.5 -322.103,202.801 -316.151,197.5 -310.513,202.9   -310.304,202.9 -304.456,197.5 -298.713,202.801 -292.868,197.5 -287.125,202.801 -281.278,197.5 -275.64,202.801 -269.793,197.5   -264.05,202.801 -258.204,197.5 -252.46,202.9 -252.356,202.9 -246.405,197.5 -240.766,202.801 -234.92,197.5 -229.177,202.801   -223.33,197.5 -217.587,202.801 -211.739,197.5 -205.999,202.9 -205.894,202.9 -200.046,197.5 -194.304,202.801 -188.458,197.5   -182.715,202.801 -176.867,197.5 -171.229,202.801 -165.277,197.5 -159.64,202.801 -153.792,197.5 -148.051,202.9 -147.84,202.9   -141.994,197.5 -136.251,202.801 -130.405,197.5 -124.767,202.801 -118.919,197.5 -113.176,202.801 -107.329,197.5 -101.586,202.9   -101.483,202.9 -95.532,197.5 -89.893,202.801 -84.047,197.5 -78.304,202.801 -72.457,197.5 -66.715,202.801 -60.867,197.5   -55.229,202.801 -49.383,197.5 -43.64,202.9 -43.431,202.9 -37.584,197.5 -31.842,202.801 -25.995,197.5 -20.356,202.801   -14.404,197.5 -8.768,202.801 -2.918,197.5 2.823,202.9 3.033,202.9 8.88,197.5 14.621,202.801 20.468,197.5 26.106,202.801   31.953,197.5 37.696,202.801 43.544,197.5 49.287,202.801 55.132,197.5 60.77,202.9 60.98,202.9 66.828,197.5 72.568,202.801   78.415,197.5 84.158,202.801 90.006,197.5 95.644,202.801 101.492,197.5 107.234,202.9 107.443,202.9 113.29,197.5 119.031,202.801   124.878,197.5 130.517,202.801 136.469,197.5 142.106,202.801 147.954,197.5 153.696,202.801 159.542,197.5 165.285,202.9   165.391,202.9 171.341,197.5 176.98,202.801 182.826,197.5 188.569,202.801 194.417,197.5 200.16,202.801 206.006,197.5   211.644,202.9 211.854,202.9 217.701,197.5 223.442,202.801 229.289,197.5 235.031,202.801 240.879,197.5 246.517,202.801   252.365,197.5 258.107,202.801 263.953,197.5 269.696,202.9 269.905,202.9 275.751,197.5 281.39,202.801 287.342,197.5   292.979,202.801 298.826,197.5 304.569,202.801 310.416,197.5 316.158,202.9 316.263,202.9 322.214,197.5 327.854,202.801   333.699,197.5 339.441,202.801 345.29,197.5 351.031,202.801 356.878,197.5 362.517,202.801 368.363,197.5 374.105,202.9   374.315,202.9 380.162,197.5 385.904,202.801 391.751,197.5 397.39,202.801 403.236,197.5 408.979,202.801 414.826,197.5   420.568,202.9 420.779,202.9 426.625,197.5 432.264,202.801 438.213,197.5 443.852,202.801 449.699,197.5 455.441,202.801   461.289,197.5 467.031,202.801 472.879,197.5 478.516,202.9 478.727,202.9 484.572,197.5 490.314,202.801 496.162,197.5   501.904,202.801 507.751,197.5 513.39,202.801 519.236,197.5 524.979,202.9 525.188,202.9 531.035,197.5 536.777,202.801   542.625,197.5 548.263,202.801 554.109,197.5 559.852,202.801 565.697,197.5 571.44,202.801 577.289,197.5 583.031,202.9   583.135,202.9 589.086,197.5 594.725,202.801 600.572,197.5 606.314,202.801 612.162,197.5 617.904,202.801 623.752,197.5   629.389,202.9 629.599,202.9 635.446,197.5 641.188,202.801 647.034,197.5 652.777,202.801 658.624,197.5 664.263,202.801   670.108,197.5 675.852,202.801 681.699,197.5 687.441,202.9 687.65,202.9 693.498,197.5 699.135,202.801 704.982,197.5   710.725,202.801 716.572,197.5 722.314,202.801 728.16,197.5 733.903,202.9 734.008,202.9 739.959,197.5 745.598,202.801   751.445,197.5 757.188,202.801 763.035,197.5 768.777,202.801 774.623,197.5 780.262,202.801 786.108,197.5 791.852,202.9   792.061,202.9 797.907,197.5 803.65,202.801 809.497,197.5 815.136,202.801 820.982,197.5 826.726,202.801 832.571,197.5   838.313,202.9 838.523,202.9 844.369,197.5 850.008,202.801 855.854,197.5 861.597,202.801 867.445,197.5 873.188,202.801   879.033,197.5 884.674,202.801 890.623,197.5 896.261,202.9 896.471,202.9 902.317,197.5 908.061,202.801 913.907,197.5   919.65,202.801 925.496,197.5 931.135,202.801 936.981,197.5 942.725,202.9 942.934,202.9 948.779,197.5 954.522,202.801   960.37,197.5 966.008,202.801 971.855,197.5 977.599,202.801 983.443,197.5 989.187,202.801 995.033,197.5 1000.776,202.9   1000.881,202.9 1006.728,197.5 1012.47,202.801 1018.317,197.5 1024.06,202.801 1029.906,197.5 1035.544,202.801 1041.496,197.5   1047.134,202.9 1047.344,202.9 1053.19,197.5 1058.933,202.801 1064.779,197.5 1070.521,202.801 1076.37,197.5 1082.008,202.801   1087.854,197.5 1093.598,202.801 1099.444,197.5 1105.187,202.9 1105.291,202.9 1111.242,197.5 1116.88,202.801 1122.727,197.5   1128.47,202.801 1134.316,197.5 1140.06,202.801 1145.906,197.5 1151.649,202.9 1151.753,202.9 1157.601,197.5 1163.343,202.801   1169.19,197.5 1174.933,202.801 1180.779,197.5 1186.418,202.801 1192.369,197.5 1198.006,202.801 1203.854,197.5 1209.597,202.9   1215.34,197.5 1221.188,202.801 1226.825,197.5 1232.776,202.801 1238.415,197.5 1244.261,202.801 1250,197.503 1250,197.1 "></polygon></svg></div>
    </div><!-- .job-results-search -->

    <div class="container" style="padding-bottom:30px;">
        <div id="content-area" class="clearfix content-area--single-job">
            <div id="left-area" class="left-area">
            <?php while (have_posts()) : the_post(); ?>
                <?php $additional_details = get_post_custom(get_the_ID()); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('et_pb_post'); ?>>
                    <div class="et_post_meta_wrapper">
                        <a style="font-weight:700;font-size:14px;" href="/jobs<?php print ($search_term != '') ? '/?s='.$search_term : '' ?>">&lsaquo; Back to <?php print ($search_term != '') ? 'Results Page' : 'Listings Page' ?></a>
                        <h1 class="entry-title" style="margin-top:15px;"><?php the_title(); ?></h1>
                        <p class="job-meta">

                            <?php $company_term = wp_get_post_terms(get_the_ID(), 'job_company'); ?>
                            <?php if(isset($company_term[0])): ?>
                              <span class="post_company"><?php echo $company_term[0]->name; ?></span> &bull; <?php $job_city = wp_get_post_terms(get_the_ID(), 'job_city'); ?>
                            <?php endif; ?>

                            <?php if(isset($job_city[0])): ?>
                              <span class="post_city"><?php echo $job_city[0]->name; ?></span>, MN<br>
                            <?php endif; ?>

                            <?php $job_source = wp_get_post_terms(get_the_ID(), 'job_source'); ?>
                            <?php if(isset($job_source[0])): ?>
                              <span class="post_source"><?php echo $job_source[0]->name; ?></span> &bull; <span class="post_date"><?php the_date(); ?></span>
                            <?php endif; ?>
                        </p>
                    </div> <!-- .et_post_meta_wrapper -->
                    <div class="entry-content">
                        <?php echo jobs_single_content(); ?>
                        <?php if(get_post_status(get_the_ID()) == 'publish'): ?>
                            <?php $get_job_url=get_post_meta(get_the_ID(), 'Job URL', true); ?>

                        <?php endif; ?>
                    </div> <!-- .entry-content -->
                    <?php #$get_job_url = '' ?>
                    <?php if(isset($additional_details['Job Id'][0])): ?>
                      <p>
                        <?php if(get_post_status(get_the_ID()) == 'publish'): ?>
                          <a href="<?php echo($get_job_url); ?>" target="_blank" class="button apply-now job-click" data-job-id="<?php print $additional_details['Job Id'][0]; ?>" data-post-id="<?php the_ID(); ?>">Apply Now</a>

                        <?php else: ?>
                          <a class="button apply-now job-click"></a>
                            <strong>Job has Expired</strong>
                        <?php endif; ?>
                      </p>
                    <?php endif; ?>
                </article> <!-- .et_pb_post -->
            <?php endwhile; ?>
            </div> <!-- #left-area -->
            <div id="sidebar" class="sidebar">

                <h3>Additional Details</h3>
                <div class="additional_details">
                <?php foreach ($additional_details as $category=>$values): ?>
                    <?php if ( ($category == '_edit_lock') || ($category == 'et_enqueued_post_fonts') || ($category == 'Job Lat') || ($category == 'Job Salary') || ($category == 'Job Lng') || ($category == 'Job Hash') ) { continue; } ?>
                    <?php if ($values[0] != ''): ?>
                        <div class="additional_detail">
                            <div class="additional_detail_category">
                                <strong><?php print $category; ?>:</strong>
                                    <?php if ($category == 'Job URL'): ?>

                                        <?php if(get_post_status(get_the_ID()) == 'publish'): ?>
                                          <a href="<?php print $values[0]; ?>" target="_blank" class="job-click" data-job-id="<?php print $additional_details['Job Id'][0]; ?>" data-post-id="<?php the_ID(); ?>"><?php print $values[0]; ?></a>
                                        <?php else: ?>
                                            Expired
                                        <?php endif; ?>

                                    <?php elseif ($category == 'Job Salary' && is_numeric($values[0])): ?>
                                        $<?php print number_format( $values[0] ); ?>
                                    <?php else: ?>
                                        <?php print implode(', ', $values); ?>
                                    <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                </div>
            </div>
          </div> <!-- #content-area -->



    </div> <!-- .container -->
</div> <!-- #main-content -->

<!-- insert custom ottertaillakescountry_featured_jobs() -->
<div class="featured-jobs-feed">
  <div class="content">
    <div class="featured-jobs-feed__header">
      <h3>FEATURED JOBS</h3>
      <a class="button button--view-more-jobs" href="/jobs/?featured=1" data-icon="5">View More</a>
    </div>
    <div class="featured-jobs-feed__listings">
      <?php print insert_featured_jobs_mod(); ?>
    </div>
  </div>
</div>

<?php
global $wp;

$streetAddress = get_post_meta(get_the_ID(), 'Job Address 1');
$addressRegion = get_post_meta(get_the_ID(), 'Job State');
$postalCode = get_post_meta(get_the_ID(), 'Job Zipcode');
$salary = get_post_meta(get_the_ID(), 'Job Salary');
$educationRequirements = get_post_meta(get_the_ID(), 'Job Education');
$experienceRequirements = get_post_meta(get_the_ID(), 'Job Experience');
$skills = get_post_meta(get_the_ID(), 'Job Skills');


$employmentType = array();
$job_types = wp_get_post_terms(get_the_ID(), 'job_type');
foreach($job_types as $jt) {
    $employmentType[] = $jt->name;
}
?>
<script type="application/ld+json"> {
  "@context" : "http://schema.org/",
  "@type" : "JobPosting",
  "title" : "<?php print htmlentities(get_the_title(), ENT_QUOTES); ?>",
  "description" : "<?php print htmlentities(get_the_content(), ENT_QUOTES); ?>",
  "datePosted" : "<?php print htmlentities(get_the_date(), ENT_QUOTES); ?>",
  //"validThrough" : "2017-03-18T00:00",
  "employmentType" : "<?php print htmlentities(implode(',', $employmentType), ENT_QUOTES); ?>",
  "hiringOrganization" : {
    "@type" : "Organization",
    "name" : "<?php print isset($company_term[0]) ? htmlentities($company_term[0]->name, ENT_QUOTES) : ''; ?>",
    "sameAs" : ""
  },
  "jobLocation" : {
    "@type" : "Place",
    "address" : {
      "@type" : "PostalAddress",
      "streetAddress" : "<?php print htmlentities($streetAddress[0], ENT_QUOTES); ?>",
      "addressLocality" : "<?php print isset($job_city[0]) ? htmlentities($job_city[0]->name) : ''; ?></span>",
      "addressRegion" : "<?php print htmlentities($addressRegion[0], ENT_QUOTES); ?>",
      "postalCode" : "<?php print htmlentities($postalCode[0], ENT_QUOTES); ?>",
      "addressCountry": "US"
    }
  },
  "baseSalary": {
    "@type": "MonetaryAmount",
    "currency": "USD",
    "value": {
      "@type": "QuantitativeValue",
      "value": "<?php print htmlentities($salary[0], ENT_QUOTES); ?>"
    }
  },
  "educationRequirements" : "<?php print htmlentities($educationRequirements[0], ENT_QUOTES); ?>",
  "experienceRequirements" : "<?php print htmlentities($experienceRequirements[0], ENT_QUOTES); ?>",
  "skills" : "<?php print htmlentities($skills[0], ENT_QUOTES); ?>",
  "url" : "<?php home_url(add_query_arg(array(),$wp->request)); ?>"
}
</script>


<?php get_footer(); ?>

<?php //custom jobs module insert
function insert_featured_jobs_mod(){
  /*
global $wpdb;
                $posts_ids = array();
                $sql = '(SELECT wp.ID FROM wp_posts AS wp
                        INNER JOIN wp_term_relationships AS wtr ON wp.ID = wtr.object_id
                        INNER JOIN wp_term_taxonomy AS wtt ON wtr.term_taxonomy_id = wtt.term_taxonomy_id
                        WHERE term_id IN (
                        SELECT DISTINCT wpm.meta_value FROM wp_posts AS wp
                        INNER JOIN wp_postmeta AS wpm
                        ON wp.ID = wpm.post_id
                        WHERE wp.post_type = "employers"
                        AND meta_key = "featured_employer_link")
                        AND wp.post_status = "publish")
                        UNION
                        (
                        SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id = 2899
                        )
                        ORDER BY RAND() LIMIT 3';
                $random_jobs = $wpdb->get_results($sql, ARRAY_A);
                foreach($random_jobs as $rj) {
                    $posts_ids[] = $rj['ID'];
                }

                // Query Arguments
                $args = array(
                    'post_type' => array('jobs'),
                    'post_status' => array('publish'),
                    'posts_per_page' => 3,
                    'order' => 'DESC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'job_featured_blocked',
                            'field' => 'name',
                            'terms' => array('featured'),
                            'include_children' => false,
                        ),
                    ),
                );

                // The Query
                $featured_jobs_query = new WP_Query($args);

                $return = "";
                // The Loop
                if (!is_admin()) {
                    foreach($posts_ids as $post_id) {
                        #$return = 'postid' . $posts_id;
                        $f_post = get_post($post_id);

                        $company_term = wp_get_post_terms($f_post->ID, 'job_company');
                        $job_city = wp_get_post_terms($f_post->ID, 'job_city');

                        $company_name = isset($company_term[0]->name) ? $company_term[0]->name : '';
                        $job_city = isset($job_city[0]->name) ? $job_city[0]->name : '';

                        $return .= '<div class="featured-job et_pb_column et_pb_column_1_3"><h3 class="featured-job__title"><a href="' . get_post_permalink($f_post->ID) . '">' . get_the_title($f_post->ID) . '</a></h3><p class="featured-job__meta">' . $company_name .' &bull; ' . $job_city . ', MN</p><a href="' . get_post_permalink($f_post->ID) . '" class="featured-job__link">Learn More</a></div>';
                    }

                wp_reset_postdata();
                }

                */

                $return = <<<EOD
<script type="text/javascript">
jQuery('.featured-jobs').load('/featured-jobs/');
</script>
EOD;

                return $return;
}

?>