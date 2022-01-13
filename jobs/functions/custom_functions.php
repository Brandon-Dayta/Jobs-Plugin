<?php
function custom_pagination($numpages = '', $pagerange = '', $paged='') {

    if(empty($pagerange)) {
        $pagerange = 3;
    }

    if(empty($paged)) {
        $paged = 1;
    }

    if($numpages == '') {
        global $wp_query;
        $numpages = $wp_query->max_num_pages;
        if(!$numpages) {
            $numpages = 1;
        }
    }

    $pagination_args = array(
        'base'        => '/jobs/%_%',
        // 'format'   => 'page/%#%',
        'format'      => '',
        'total'       => $numpages,
        'current'     => $paged,
        'show_all'    => False,
        'end_size'    => 1,
        'mid_size'    => $pagerange,
        'prev_next'   => True,
        'prev_text'   => __('&laquo;'),
        'next_text'   => __('&raquo;'),
        'type'        => 'plain',
        'add_args'    => false,
        'add_fragment'=> ''
    );

    $paginate_links = paginate_links($pagination_args);

    $pages = '';
    $displaying = '';

    if ($paginate_links) {
        $pages .= "<nav class='custom-pagination'>";
        $pages .= "<div class='page-links'>";
        $pages .= $paginate_links;
        $pages .= "</div>";
        $pages .= "</nav>";
    }

    $displaying = "<div class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</div> ";

    return array('pages'=>$pages, 'displaying'=>$displaying);
}
?>