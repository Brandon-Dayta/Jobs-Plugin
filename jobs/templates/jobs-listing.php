<?php



#$args = array( 'posts_per_page' => 10, 'post_type' => 'jobs', 'post_status'=>'publish', 'order' => 'DESC', 'tax_query' => $tax_query, 'paged' => $paged, 's'=>$search_term);
#$filtered_jobs_query = new WP_Query($args);
#echo $GLOBALS['wp_query']->request;
/*
$tax_query = array();

// $tax_query[] = array(
//    'taxonomy' => 'job_company',
//    'field' => 'id',
//    'terms' => array(96,97)
// );
// $tax_query[] = array(
//     'taxonomy' => 'job_featured_blocked',
//     'field' => 'slug',
//     'terms' => 'featured',
//     );

// $jobs = get_posts(array('post_type' => 'jobs', 'numberposts' => -1, 'orderby'=>'post_title', 'order'=>'ASC', 'tax_query' => $tax_query));
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

//check if search form used to populate wp_query args
if (isset($_GET['s'])){
    $args = array( 's' => $_GET['s'], 'posts_per_page' => 10, 'post_type' => $_GET['post_type'], 'paged' => $paged, 'order' => 'ASC',  'sentence' => $_GET['sentence']);
} else {
    $args = array( 'posts_per_page' => 10, 'post_type' => 'jobs', 'paged' => $paged, 'order' => 'ASC', 'tax_query' => $tax_query );
}
$the_jobs_query = new WP_Query( $args );

$filters = array();
$terms = get_terms(array(
    'post_type' => array('jobs'),
    'hide_empty' => false,
    'orderby'=>'name',
    'order'=>'ASC'
));

foreach($terms as $term) {
    if($term->taxonomy == 'category') {
        continue;
    }
    $filters[$term->taxonomy][] = array('name'=>$term->name, 'count'=>$term->count, 'term_id'=>$term->term_id);
}

$company_terms = get_terms('job_company');
*/

#$SavedSearches = new SavedSearches();
#$SavedSearches->sendEmail();


//make read more link for the_excerpt()
// function modify_read_more_link($more)
// {
//     return '<a class="more-link" href="' . get_permalink() . '"> Read More...</a>';
// }
// add_filter('excerpt_more', 'modify_read_more_link');

// Get the search variable and track it
$s = isset($_GET['s']) ? trim($_GET['s']) : null;
if ($s) {
    //include 'wp-content/plugins/jobs/classes/tracking.class.php';
    $Tracking = new Tracking();
    $Tracking->trackSearchWords($s);

    #$_SESSION['search'] = $s;
}

$fe = isset($_GET['fe']) ? trim($_GET['fe']) : null;
$featured = isset($_GET['featured']) ? trim($_GET['featured']) : null;
echo '<script>console.log("fe: ' . $fe . '")</script>';

$search = isset($_SESSION['search'][$s]) ? $_SESSION['search'][$s] : null;

#print '<pre>';
#print_r($search);
#print '</pre>';
#exit;

$filter_s = '%'.$s.'%';
if($fe) {
    $search_terms = $wpdb->get_results($wpdb->prepare("SELECT wt.name, wt.term_id, wtt.taxonomy, COUNT(wt.term_id) AS total FROM wp_term_taxonomy AS wtt
                                        INNER JOIN wp_terms AS wt ON wtt.term_id = wt.term_id
                                        INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                        WHERE (wtt.taxonomy = 'job_city' OR wtt.taxonomy = 'job_company' OR wtt.taxonomy = 'job_type' OR wtt.taxonomy = 'job_source')
                                        AND wtr.object_id IN (SELECT DISTINCT wp_posts.ID FROM wp_posts
                                            INNER JOIN wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_id
                                            INNER JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
                                            WHERE wp_term_taxonomy.term_id = %d
                                                AND (((wp_posts.post_title LIKE %s) OR (wp_posts.post_excerpt LIKE %s) OR (wp_posts.post_content LIKE %s)))
                                                AND (wp_posts.post_password = '')
                                                AND wp_posts.post_type = 'jobs'
                                                AND (wp_posts.post_status = 'publish' OR wp_posts.post_status = 'acf-disabled'))
                                        GROUP BY wt.term_id
                                        ORDER BY wt.name", $fe, $filter_s, $filter_s, $filter_s));
} elseif ($featured == 1) {
    $search_terms = $wpdb->get_results($wpdb->prepare("SELECT wt.name, wt.term_id, wtt.taxonomy, COUNT(wt.term_id) AS total FROM wp_term_taxonomy AS wtt
                                        INNER JOIN wp_terms AS wt ON wtt.term_id = wt.term_id
                                        INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                        WHERE (wtt.taxonomy = 'job_city' OR wtt.taxonomy = 'job_company' OR wtt.taxonomy = 'job_type' OR wtt.taxonomy = 'job_source')
                                        AND wtr.object_id IN (
                                        SELECT p.ID
                                        FROM wp_posts AS p
                                        INNER JOIN wp_terms AS t
                                        INNER JOIN wp_term_relationships AS tr ON (p.ID = tr.object_id)
                                        INNER JOIN wp_term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                                        INNER JOIN wp_terms AS t2 ON (t2.term_id = tt.term_id)
                                        WHERE (t2.slug = 'featured-employer' OR t2.slug = 'featured')
                                        AND (((p.post_title LIKE %s) OR (p.post_excerpt LIKE %s) OR (p.post_content LIKE %s)))  AND (p.post_password = '')
                                          AND p.post_type = 'jobs' AND (p.post_status = 'publish' OR p.post_status = 'acf-disabled')
                                            )
                                        GROUP BY wt.term_id, wtt.taxonomy
                                        ORDER BY wt.name", $filter_s, $filter_s, $filter_s));
} else {
    $search_terms = $wpdb->get_results($wpdb->prepare("SELECT wt.name, wt.term_id, wtt.taxonomy, COUNT(wt.term_id) AS total FROM wp_term_taxonomy AS wtt
                                        INNER JOIN wp_terms AS wt ON wtt.term_id = wt.term_id
                                        INNER JOIN wp_term_relationships AS wtr ON wtt.term_taxonomy_id = wtr.term_taxonomy_id
                                        WHERE (wtt.taxonomy = 'job_city' OR wtt.taxonomy = 'job_company' OR wtt.taxonomy = 'job_type' OR wtt.taxonomy = 'job_source')
                                        AND wtr.object_id IN (SELECT wp_posts.ID FROM wp_posts
                                          WHERE (((wp_posts.post_title LIKE %s) OR (wp_posts.post_excerpt LIKE %s) OR (wp_posts.post_content LIKE %s)))  AND (wp_posts.post_password = '')
                                          AND wp_posts.post_type = 'jobs' AND (wp_posts.post_status = 'publish' OR wp_posts.post_status = 'acf-disabled'))
                                        GROUP BY wt.term_id
                                        ORDER BY wt.name", $filter_s, $filter_s, $filter_s));
}

$city_terms = [];
$company_terms = [];
$type_terms = [];
$source_terms = [];

foreach ($search_terms as $search_term) {
    if ($search_term->taxonomy == 'job_company'){
        // print '<pre>';
        // print_r($search_term->name);
        // print '</pre>';
        $temp_obj = new stdClass();
        $temp_obj->name = $search_term->name;
        $temp_obj->total = $search_term->total;
        $temp_obj->term_id = $search_term->term_id;
        $company_terms[] = $temp_obj;

        // $company_terms[$i]['name'] = $search_term->name;
        // $company_terms[$i]['total'] = $search_term->total;
    }
    if ($search_term->taxonomy == 'job_city'){
        $temp_obj = new stdClass();
        $temp_obj->name = $search_term->name;
        $temp_obj->total = $search_term->total;
        $temp_obj->term_id = $search_term->term_id;
        $city_terms[] = $temp_obj;
        // $city_terms[$i]['name'] = $search_term->name;
        // $city_terms[$i]['total'] = $search_term->total;
    }
    if ($search_term->taxonomy == 'job_type'){
        $temp_obj = new stdClass();
        $temp_obj->name = $search_term->name;
        $temp_obj->total = $search_term->total;
        $temp_obj->term_id = $search_term->term_id;
        $type_terms[] = $temp_obj;
        // $type_terms[$i]['name'] = $search_term->name;
        // $type_terms[$i]['total'] = $search_term->total;
    }
    if ($search_term->taxonomy == 'job_source'){
        $temp_obj = new stdClass();
        $temp_obj->name = $search_term->name;
        $temp_obj->total = $search_term->total;
        $temp_obj->term_id = $search_term->term_id;
        $source_terms[] = $temp_obj;
        // $source_terms[$i]['name'] = $search_term->name;
        // $source_terms[$i]['total'] = $search_term->total;
    }
}
    // print '<pre>';
    // print_r($search_terms);
    // print '</pre>';
    // foreach ($company_terms as $cterm) {
    //     print_r($cterm->name);
    //     print '<br>';// . ' value: ' . $value;
    // }
#}
?>

<?php get_header(); ?>


<div id="main-content">

        <?php $count_jobs = wp_count_posts('jobs'); ?>
        <div class="counter-panel">
            <h2>
                <span><?php print number_format( $count_jobs->publish ); ?></span>
                Great jobs available, right now.
            </h2>
            <div class="uabb-row-separator uabb-bottom-row-separator uabb-mul-triangles-separator uabb-has-svg" style="">
                <svg class="uasvg-mul-triangles-separator fill-tan" xmlns="http://www.w3.org/2000/svg" fill="#ffffff" opacity="1" width="100" height="8" preserveAspectRatio="none" viewBox="-670 197.1 1920 5.8"><polygon xmlns="http://www.w3.org/2000/svg" points="-670,197.1 -670,202.9 -664.153,197.5 -664.151,197.5 -658.513,202.801 -652.562,197.5 -646.923,202.801   -641.076,197.5 -635.334,202.801 -629.487,197.5 -623.743,202.9 -623.639,202.9 -617.688,197.5 -612.05,202.801 -606.203,197.5   -600.461,202.801 -594.614,197.5 -588.871,202.801 -583.024,197.5 -577.386,202.801 -571.54,197.5 -565.796,202.9 -565.587,202.9   -559.74,197.5 -553.999,202.801 -548.151,197.5 -542.513,202.801 -536.666,197.5 -530.923,202.801 -525.076,197.5 -519.333,202.9   -519.125,202.9 -513.278,197.5 -507.64,202.801 -501.688,197.5 -496.051,202.801 -490.204,197.5 -484.46,202.801 -478.613,197.5   -472.976,202.801 -467.024,197.5 -461.386,202.9 -461.177,202.9 -455.33,197.5 -449.587,202.801 -443.741,197.5 -437.998,202.801   -432.151,197.5 -426.512,202.801 -420.665,197.5 -414.923,202.9 -414.715,202.9 -408.868,197.5 -403.125,202.801 -397.277,197.5   -391.64,202.801 -385.793,197.5 -380.05,202.801 -374.203,197.5 -368.46,202.801 -362.613,197.5 -356.871,202.9 -356.767,202.9   -350.815,197.5 -345.177,202.801 -339.331,197.5 -333.588,202.801 -327.741,197.5 -322.103,202.801 -316.151,197.5 -310.513,202.9   -310.304,202.9 -304.456,197.5 -298.713,202.801 -292.868,197.5 -287.125,202.801 -281.278,197.5 -275.64,202.801 -269.793,197.5   -264.05,202.801 -258.204,197.5 -252.46,202.9 -252.356,202.9 -246.405,197.5 -240.766,202.801 -234.92,197.5 -229.177,202.801   -223.33,197.5 -217.587,202.801 -211.739,197.5 -205.999,202.9 -205.894,202.9 -200.046,197.5 -194.304,202.801 -188.458,197.5   -182.715,202.801 -176.867,197.5 -171.229,202.801 -165.277,197.5 -159.64,202.801 -153.792,197.5 -148.051,202.9 -147.84,202.9   -141.994,197.5 -136.251,202.801 -130.405,197.5 -124.767,202.801 -118.919,197.5 -113.176,202.801 -107.329,197.5 -101.586,202.9   -101.483,202.9 -95.532,197.5 -89.893,202.801 -84.047,197.5 -78.304,202.801 -72.457,197.5 -66.715,202.801 -60.867,197.5   -55.229,202.801 -49.383,197.5 -43.64,202.9 -43.431,202.9 -37.584,197.5 -31.842,202.801 -25.995,197.5 -20.356,202.801   -14.404,197.5 -8.768,202.801 -2.918,197.5 2.823,202.9 3.033,202.9 8.88,197.5 14.621,202.801 20.468,197.5 26.106,202.801   31.953,197.5 37.696,202.801 43.544,197.5 49.287,202.801 55.132,197.5 60.77,202.9 60.98,202.9 66.828,197.5 72.568,202.801   78.415,197.5 84.158,202.801 90.006,197.5 95.644,202.801 101.492,197.5 107.234,202.9 107.443,202.9 113.29,197.5 119.031,202.801   124.878,197.5 130.517,202.801 136.469,197.5 142.106,202.801 147.954,197.5 153.696,202.801 159.542,197.5 165.285,202.9   165.391,202.9 171.341,197.5 176.98,202.801 182.826,197.5 188.569,202.801 194.417,197.5 200.16,202.801 206.006,197.5   211.644,202.9 211.854,202.9 217.701,197.5 223.442,202.801 229.289,197.5 235.031,202.801 240.879,197.5 246.517,202.801   252.365,197.5 258.107,202.801 263.953,197.5 269.696,202.9 269.905,202.9 275.751,197.5 281.39,202.801 287.342,197.5   292.979,202.801 298.826,197.5 304.569,202.801 310.416,197.5 316.158,202.9 316.263,202.9 322.214,197.5 327.854,202.801   333.699,197.5 339.441,202.801 345.29,197.5 351.031,202.801 356.878,197.5 362.517,202.801 368.363,197.5 374.105,202.9   374.315,202.9 380.162,197.5 385.904,202.801 391.751,197.5 397.39,202.801 403.236,197.5 408.979,202.801 414.826,197.5   420.568,202.9 420.779,202.9 426.625,197.5 432.264,202.801 438.213,197.5 443.852,202.801 449.699,197.5 455.441,202.801   461.289,197.5 467.031,202.801 472.879,197.5 478.516,202.9 478.727,202.9 484.572,197.5 490.314,202.801 496.162,197.5   501.904,202.801 507.751,197.5 513.39,202.801 519.236,197.5 524.979,202.9 525.188,202.9 531.035,197.5 536.777,202.801   542.625,197.5 548.263,202.801 554.109,197.5 559.852,202.801 565.697,197.5 571.44,202.801 577.289,197.5 583.031,202.9   583.135,202.9 589.086,197.5 594.725,202.801 600.572,197.5 606.314,202.801 612.162,197.5 617.904,202.801 623.752,197.5   629.389,202.9 629.599,202.9 635.446,197.5 641.188,202.801 647.034,197.5 652.777,202.801 658.624,197.5 664.263,202.801   670.108,197.5 675.852,202.801 681.699,197.5 687.441,202.9 687.65,202.9 693.498,197.5 699.135,202.801 704.982,197.5   710.725,202.801 716.572,197.5 722.314,202.801 728.16,197.5 733.903,202.9 734.008,202.9 739.959,197.5 745.598,202.801   751.445,197.5 757.188,202.801 763.035,197.5 768.777,202.801 774.623,197.5 780.262,202.801 786.108,197.5 791.852,202.9   792.061,202.9 797.907,197.5 803.65,202.801 809.497,197.5 815.136,202.801 820.982,197.5 826.726,202.801 832.571,197.5   838.313,202.9 838.523,202.9 844.369,197.5 850.008,202.801 855.854,197.5 861.597,202.801 867.445,197.5 873.188,202.801   879.033,197.5 884.674,202.801 890.623,197.5 896.261,202.9 896.471,202.9 902.317,197.5 908.061,202.801 913.907,197.5   919.65,202.801 925.496,197.5 931.135,202.801 936.981,197.5 942.725,202.9 942.934,202.9 948.779,197.5 954.522,202.801   960.37,197.5 966.008,202.801 971.855,197.5 977.599,202.801 983.443,197.5 989.187,202.801 995.033,197.5 1000.776,202.9   1000.881,202.9 1006.728,197.5 1012.47,202.801 1018.317,197.5 1024.06,202.801 1029.906,197.5 1035.544,202.801 1041.496,197.5   1047.134,202.9 1047.344,202.9 1053.19,197.5 1058.933,202.801 1064.779,197.5 1070.521,202.801 1076.37,197.5 1082.008,202.801   1087.854,197.5 1093.598,202.801 1099.444,197.5 1105.187,202.9 1105.291,202.9 1111.242,197.5 1116.88,202.801 1122.727,197.5   1128.47,202.801 1134.316,197.5 1140.06,202.801 1145.906,197.5 1151.649,202.9 1151.753,202.9 1157.601,197.5 1163.343,202.801   1169.19,197.5 1174.933,202.801 1180.779,197.5 1186.418,202.801 1192.369,197.5 1198.006,202.801 1203.854,197.5 1209.597,202.9   1215.34,197.5 1221.188,202.801 1226.825,197.5 1232.776,202.801 1238.415,197.5 1244.261,202.801 1250,197.503 1250,197.1 "></polygon></svg>
            </div>
        </div>

        <div class="job-results-search et_pb_section">
            <div class="et_pb_row job-results-search-hero">

				<div class="et_pb_column et_pb_column_1_3">
					<!--<img style="display:block;width:260px;max-width:80%;margin:0 auto 30px;" src="/wp-content/uploads/2020/07/logo-large-white.png" alt="Otter Tail Lakes Country"/>-->
				</div>
				<div class="et_pb_column et_pb_column_2_3">
                <h1 class="job-results-search__title">Find Your Perfect Job!</h1>
                <!-- Custom Search Form -->
                <form role="search" method="get" id="searchform" class="searchform" action="/jobs/">
                    <div>
                        <div class="job-results-search__item">
                            <label class="screen-reader-text" for="s">Search Jobs Keyword:</label>
                            <input type="text" value="<?php print htmlspecialchars($s); ?>" name="s" id="s">
                            <!-- <input type="hidden" value="1" name="sentence">
                            <input type="hidden" value="jobs" name="post_type"> -->
                            <!-- <input type="hidden" value="product_cat" name="magazines,books" /> -->
                            <button type="submit" id="searchsubmit" class="job-results-search__icon" value="Search">Search</button>
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
            </div> <!-- .et_pb_row -->
        </div><!-- job-results-search -->

        <div id="content-area" class="job-results clearfix">

			<!--<div class="et_pb_row et_pb_row_1">-->

			<div class="job-results-container">

				<div class="job-results__filter et_pb_column et_pb_column_1_4  et_pb_column_0 et_pb_column_single">

					<h2>Filtering</h2>
                    <a href="#" id="job_filter_clear">Clear</a>
					<input type="hidden" id="hidden-page-number" value="<?php print isset($search) ? $search['page_number'] : null; ?>">

					<!-- <div class="job-$company_term[0]->name;"> -->

					<div class="job-filter job-city">
						<div class="job-filter-type" id="city-title">City <a id="ci-plus">+</a></div>
						<div class="job-filter-values">
							<div class="job-filter-value" id="j-cities" style="display: none;" >
							<ul>
							<?php #$city_terms = get_terms('job_city'); ?>
							<?php foreach ($city_terms as $ci_term): ?>
								<li id="<?php echo $ci_term->term_id; ?>" class="jobs_filter job_city <?php echo $ci_term->term_id; ?> <?php print (isset($search) && in_array(trim($ci_term->name), $search['city_filters'])) ? 'selected' : ''; ?>"
									data-city-id="<?php echo $ci_term->term_id; ?>"
									data-filter-name="<?php echo htmlspecialchars($ci_term->name, ENT_QUOTES); ?>
								 ">
								 <a href="#"><?php echo $ci_term->name . ' (' . $ci_term->total . ')'; ?></a>
								</li>
							<?php endforeach; ?>
							</ul>
							</div>
						</div><!-- end div job-filter-values -->
					</div> <!-- end div job-city -->

					<div class="job-filter job-companies">
						<div class="job-filter-type" id="companies-title">Companies <a id="c-plus">+</a></div>
						<div class="job-filter-values" >
							<div class="job-filter-value" id="j-companies" style="display: none;" >
							<ul>
							<?php //$company_terms = get_terms('job_company'); ?>
							<?php foreach ($company_terms as $c_term): ?>
								<li id="<?php echo $c_term->term_id; ?>" class="jobs_filter job_company <?php print (isset($fe) && $fe == $c_term->term_id) ? 'selected' : ''; ?> <?php  print (isset($search) && in_array(trim($c_term->name), $search['company_filters'])) ? 'selected' : ''; ?>" data-company-id="<?php echo $c_term->term_id; ?>"
									data-filter-name="<?php echo htmlspecialchars($c_term->name, ENT_QUOTES); ?>"><a onclick="document.location='#results';return false;" href="#"><?php echo $c_term->name . ' (' . $c_term->total .')'; ?></a></li>
							<?php endforeach; ?>
							</ul>
							</div>
							<!-- <div class="expand-filters"><a href="#">More...</a></div> -->
						</div><!-- end div job-filter-values -->
					</div> <!-- end div job-companies -->

					<div class="job-filter job-types">
						<div class="job-filter-type" id="type-title">Type <a id="t-plus">+</a></div>
						<div class="job-filter-values">
							<div class="job-filter-value" id="j-types" style="display: none;" >
							<ul>
							<?php // $type_terms = get_terms('job_type'); ?>
							<?php foreach ($type_terms as $t_term): ?>
								<li class="jobs_filter job_type <?php print (isset($search) && in_array(trim($t_term->name), $search['type_filters'])) ? 'selected' : ''; ?>"
									data-type-id="<?php echo $t_term->term_id; ?>" data-filter-name="<?php echo htmlspecialchars($t_term->name, ENT_QUOTES); ?>"><a onclick="document.location='#results';return false;" href="#"><?php echo $t_term->name . ' (' . $t_term->total .')'; ?></a>
								</li>
							<?php endforeach; ?>
							</ul>
							</div>
						</div><!-- end div job-filter-values -->
					</div> <!-- end div job-types -->

					<div class="job-filter job-sources">
						<div class="job-filter-type" id="source-title">Source <a id="s-plus">+</a></div>
						<div class="job-filter-values">
							<div class="job-filter-value" id="j-sources" style="display: none;" >
							<ul>
							<?php // $source_terms = get_terms('job_source'); ?>
							<?php foreach ($source_terms as $s_term): ?>
								<li class="jobs_filter job_source <?php print (isset($search) && in_array(trim($s_term->name), $search['source_filters'])) ? 'selected' : ''; ?>"
									data-source-id="<?php echo $s_term->term_id; ?>"
									data-filter-name="<?php echo htmlspecialchars($s_term->name, ENT_QUOTES); ?>"><a onclick="document.location='#results';return false;" href="#"><?php echo $s_term->name . ' (' . $s_term->total . ')'; ?></a>
								</li>
							<?php endforeach; ?>
							</ul>
							</div>
						</div><!-- end div job-filter-values -->
					</div> <!-- end div job-sources -->
					<input type="hidden" id="hidden_featured" name="hidden_featured" value="<?php print (isset($_GET['featured'])) ? htmlspecialchars($_GET['featured'], ENT_QUOTES) : '' ?>">


				</div>



            <div class="job-results__jobs et_pb_column et_pb_column_3_4 et_pb_column_1 et_pb_specialty_column">
				<div style="text-align: center; display: none;" id="ajax_results_spinner"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/otcla-loading.gif"></div>
				<div name="results" id="results"></div>
				<div id="ajax_results_html">
					
					</div>
                </div><!-- #ajax_results_html -->
            </div>
            <!-- #left-area -->

					</div>

        </div> <!-- #content-area -->
</div> <!-- #main-content -->

<div class="founding-partners et_pb_section et_pb_section_3 et_pb_with_background et_section_regular" style="display: none;">

    <div class=" et_pb_row et_pb_row_1">
        <div class="et_pb_column et_pb_column_4_4  et_pb_column_1">
            <div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_1">
                <div class="et_pb_text_inner">
                    <h2 style="text-align: center;">FOUNDING PARTNERS</h2>
                </div>
            </div> <!-- .et_pb_text -->
        </div> <!-- .et_pb_column -->
    </div> <!-- .et_pb_row -->

    <div class=" et_pb_row et_pb_row_2">
        <div class="et_pb_column et_pb_column_4_4 v-center et_pb_column_2">
            <!--
            <div class="et_pb_module et_pb_image five-column et_pb_image_0 et_always_center_on_mobile">
                <img src="https://ottertaillakescountry.com/wp-content/uploads/2017/06/logo-centracare-health.png" alt="">
            </div>
            <div class="et_pb_module et_pb_image five-column et_pb_image_1 et_always_center_on_mobile">
                <img src="https://ottertaillakescountry.com/wp-content/uploads/2017/06/logo-bernicks.png" alt="">
            </div>
            <div class="et_pb_module et_pb_image five-column et_pb_image_2 et_always_center_on_mobile">
                <img src="https://ottertaillakescountry.com/wp-content/uploads/2017/06/logo-coborns.png" alt="">
            </div>
            <div class="et_pb_module et_pb_image five-column et_pb_image_3 et_always_center_on_mobile">
                <img src="https://ottertaillakescountry.com/wp-content/uploads/2017/06/logo-park-industires.png" alt="">
            </div>
            <div class="et_pb_module et_pb_image five-column et_pb_image_4 et_always_center_on_mobile">
                <img src="https://ottertaillakescountry.com/wp-content/uploads/2017/06/logo-marco.png" alt="">
            </div>
            -->
        </div> <!-- .et_pb_column -->
    </div> <!-- .et_pb_row -->
</div>
																									 
<?php get_footer(); ?>
