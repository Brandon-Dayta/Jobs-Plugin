<?php
exit;
include 'classes/jobs.class.php';
$Jobs = new Jobs();

/*
$previously_downloaded = array_merge(scandir('data'), scandir('data/backup'));
$imported_jobs = $Jobs->fetchJobHashes();

#$conn_id = @ftp_connect('ftp.wantedtech.com');
$conn_id = @ftp_connect('ftp.wantedanalytics.com');
$login_result = @ftp_login($conn_id, 'gscdc', 'Kl21qw');
if($login_result) {
    ftp_pasv($conn_id, true);
    $zip_files = ftp_nlist($conn_id, ".");
    foreach($zip_files as $zip_file) {
        if($zip_file == '.' || $zip_file == '..' || in_array($zip_file, $previously_downloaded)) {
            continue;
        }

        if(!ftp_get($conn_id, 'data/'. $zip_file, $zip_file, FTP_BINARY)) {
           echo "Error: There was a problem\n";
        }
    }
} else {
    print 'Error: Cound not connect';
}

$jobs = scandir('data');
foreach($jobs as $job) {
    if($job == '.' || $job == '..' || $job ==  'backup' || $job ==  'unzip') {
        continue;
    }

    if(strtolower(substr($job, -4, 4)) != '.zip') {
        continue;
    }

    print $job . '<br>';

    $zip = new ZipArchive;
    if($zip->open('data/' . $job) === TRUE) {
        $zip->extractTo('data');
        $zip->close();
    } else {
        echo 'Error: Failed to uznip';
    }

    $delete_file = str_replace('$mode', 'DELETE', $job);
    $delete_file = str_replace('.zip', '.xml', $delete_file);
    if(is_file('data/' . $delete_file)) {
        removeJobs($delete_file, $Jobs);
        unlink('data/' . $delete_file);
    }

    $add_file = str_replace('$mode', 'NEW', $job);
    $add_file = str_replace('.zip', '.xml', $add_file);
    if(is_file('data/' . $add_file)) {
        $imported_jobs = addJobs($add_file, $imported_jobs);
        unlink('data/' . $add_file);
    }

    rename('data/' . $job, 'data/backup/' . $job);
}

function removeJobs($delete_file, $Jobs) {
    $xml = simplexml_load_file('data/' . $delete_file);
    if(isset($xml->job)) {
        foreach($xml->job as $j) {
            $hash = (string)$j->hash;
            $delete_job = $Jobs->fetchPostByJobHashes($hash);
            if(!empty($delete_job)) {
                $delete_post = get_post($delete_job[0], 'ARRAY_A');
                $delete_post['post_status'] = 'expired_jobs';
                wp_update_post($delete_post);

                global $wpdb;
                $wpdb->update('gscjs_job_archive', array('expired_date'=>date('Y-m-d')), array('hash'=>$hash));
            }
        }
    }
}

function addJobs($add_file, $imported_jobs) {
    $xml = simplexml_load_file('data/' . $add_file);
    if(isset($xml->job)) {
        foreach($xml->job as $j) {
            $hash = (string)$j->hash;
            if(in_array($hash, $imported_jobs)) {
                continue;
            } else {
                $imported_jobs[] = $hash;

                $author_id = 1;
                $title = (string)$j->title;
                $post_date = (string)$j->posteddate;
                $source = (string)$j->source;
                $job_company = (string)$j->company;
                $type = (string)$j->type;
                $job_city = trim((string)$j->city);

                $post_id = wp_insert_post(
                    array(
                        'comment_status'    =>  'closed',
                        'ping_status'       =>  'closed',
                        'post_author'       =>  $author_id,
                        //'post_name'         =>  $slug,
                        'post_content'=> (string)$j->description,

                        'post_date'=> $post_date,
                        'post_date_gmt' => get_gmt_from_date($post_date),

                        'post_title'        =>  $title,
                        'post_status'       =>  'publish',
                        'post_type'         =>  'jobs'
                    )
                );

                $job_archive = array('post_id'=>$post_id, 'post_date'=>$post_date, 'title'=>$title, 'hash'=>$hash);
                $job_archive_types = array();

                // job source
                if($source != '') {
                    $term = term_exists($source, 'job_source');
                    if(empty($term)) {
                        $term = wp_insert_term($source, 'job_source');
                    }
                    wp_set_post_terms($post_id, array($term['term_id']), 'job_source');

                    $job_archive['source'] = $source;
                }

                // job company
                if($job_company != '') {
                    $term = term_exists($job_company, 'job_company');
                    if(empty($term)) {
                        $term = wp_insert_term($job_company, 'job_company');
                    }
                    wp_set_post_terms($post_id, array($term['term_id']), 'job_company');

                    $job_archive['company'] = $job_company;
                }

                // job types
                $types = explode(',', $type);
                foreach($types as $type) {
                    $type = trim($type);
                    if($type != '') {
                        $term = term_exists($type, 'job_type');
                        if(empty($term)) {
                            $term = wp_insert_term($type, 'job_type');
                        }
                        wp_set_post_terms($post_id, array($term['term_id']), 'job_type', true);
                        $job_archive_types[] = $type;
                    }
                }

                // job cities
                if($job_city != '') {
                    $term = term_exists($job_city, 'job_city');
                    if(empty($term)) {
                        $term = wp_insert_term($job_city, 'job_city');
                    }
                    wp_set_post_terms($post_id, array($term['term_id']), 'job_city');

                    $job_archive['city'] = $job_city;
                }

                // meta values
                if(!add_post_meta($post_id, 'Job Id', (string)$j->id, true)) {
                    update_post_meta ($post_id, 'Job Id', (string)$j->id);
                    $job_archive['job_id'] = (string)$j->id;
                    // NOT SAVING? UPDATE gscjs_job_archive SET job_id = (SELECT meta_value FROM wp_postmeta WHERE meta_key = 'Job Id' AND post_id = gscjs_job_archive.post_id)
                }
                if(!add_post_meta($post_id, 'Job URL', (string)$j->url, true)) {
                    update_post_meta ($post_id, 'Job URL', (string)$j->url);
                    $job_archive['url'] = (string)$j->url;
                }
                if(!add_post_meta($post_id, 'Job Onet Code', (string)$j->onetcode, true)) {
                    update_post_meta ($post_id, 'Job Onet Code', (string)$j->onetcode);
                    $job_archive['onet_code'] = (string)$j->onetcode;
                }
                if(!add_post_meta($post_id, 'Job Salary', (string)$j->salary, true)) {
                    update_post_meta ($post_id, 'Job Salary', (string)$j->salary);
                    $job_archive['salary'] = (string)$j->salary;
                }
                if(!add_post_meta($post_id, 'Job Address 1', (string)$j->address1, true)) {
                    update_post_meta ($post_id, 'Job Address 1', (string)$j->address1);
                    $job_archive['address1'] = (string)$j->address1;
                }
                if(!add_post_meta($post_id, 'Job Address 2', (string)$j->address2, true)) {
                    update_post_meta ($post_id, 'Job Address 2', (string)$j->address2);
                    $job_archive['address2'] = (string)$j->address2;
                }
                if(!add_post_meta($post_id, 'Job State', (string)$j->state, true)) {
                    update_post_meta ($post_id, 'Job State', (string)$j->state);
                    $job_archive['state'] = (string)$j->state;
                }
                if(!add_post_meta($post_id, 'Job Zipcode', (string)$j->zipcode, true)) {
                    update_post_meta ($post_id, 'Job Zipcode', (string)$j->zipcode);
                    $job_archive['zipcode'] = (string)$j->zipcode;
                }
                if(!add_post_meta($post_id, 'Job Lat', (string)$j->latd, true)) {
                    update_post_meta ($post_id, 'Job Lat', (string)$j->latd);
                }
                if(!add_post_meta($post_id, 'Job Lng', (string)$j->long, true)) {
                    update_post_meta ($post_id, 'Job Lng', (string)$j->long);
                }
                if(!add_post_meta($post_id, 'Job County', (string)$j->county, true)) {
                    update_post_meta ($post_id, 'Job County', (string)$j->county);
                    $job_archive['county'] = (string)$j->county;
                }
                if(!add_post_meta($post_id, 'Job Education', (string)$j->education, true)) {
                    update_post_meta ($post_id, 'Job Education', (string)$j->education);
                    $job_archive['education'] = (string)$j->education;
                }
                if(!add_post_meta($post_id, 'Job Experience', (string)$j->experience, true)) {
                    update_post_meta ($post_id, 'Job Experience', (string)$j->experience);
                    $job_archive['experience'] = (string)$j->experience;
                }
                if(!add_post_meta($post_id, 'Job Skills', (string)$j->skills, true)) {
                    update_post_meta ($post_id, 'Job Skills', (string)$j->skills);
                    $job_archive['skills'] = (string)$j->skills;
                }
                if(!add_post_meta($post_id, 'Job Hash', (string)$j->hash, true)) {
                    update_post_meta ($post_id, 'Job Hash', (string)$j->hash);
                }

                global $wpdb;
                $wpdb->insert('gscjs_job_archive', $job_archive);
                $job_archive_id = $wpdb->insert_id;

                if(!empty($job_archive_types)) {
                    foreach($job_archive_types as $job_archive_type) {
                        $data = array('gscjs_job_id'=>$job_archive_id, 'type'=>$job_archive_type);
                        $wpdb->insert('gscjs_job_archive_types', $data);
                    }
                }
            }
        }
    }
    return $imported_jobs;
}
*/
?>