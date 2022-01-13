<?php
class SavedSearches {
    function __construct() {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    public function fetchSavedSearches() {
        $saved_searches = $this->wpdb->get_results('SELECT * FROM gscjs_saved_searches WHERE approved = 1', ARRAY_A);
        return $saved_searches;
    }

    public function createSavedSearch($email, $search_data) {
        $sql = $this->wpdb->prepare('SELECT COUNT(*) AS cnt FROM gscjs_saved_searches WHERE email = %s', $email);
        $count = $this->wpdb->get_var($sql);
        if($count <= 20) {
            $code = $this->generateRandomCode();
            $sql = $this->wpdb->prepare('INSERT INTO gscjs_saved_searches (code, email, search_data) VALUES (%s, %s, %s)', $code, $email, $search_data);
            $this->wpdb->query($sql);
            $lastid = $this->wpdb->insert_id;

            $this->sendEmail($email, 'Otter Tail Lakes Country - Saved Search Signup', 'You have signed up for a saved search. <a href="https://ottertaillakescountry.com/saved-search?verify=1&code='.$code.'&id='.$lastid.'">Click here to activate the saved serach</a>');
        }
    }

    public function activateSavedSearch($id, $code) {
        $sql = $this->wpdb->prepare('UPDATE gscjs_saved_searches SET approved = 1 WHERE id = %d AND code = %s', $id, $code);
        $this->wpdb->query($sql);
    }

    public function deleteSavedSearch($id, $code) {
        $sql = $this->wpdb->prepare('DELETE FROM gscjs_saved_searches WHERE id = %d AND code = %s', $id, $code);
        $this->wpdb->query($sql);
    }

    public function deleteOldSavedSearch() {
        $sql = 'DELETE FROM gscjs_saved_searches WHERE (created_at + INTERVAL 30 DAY) <= NOW()';
        $this->wpdb->query($sql);
    }

    private function generateRandomCode() {
        return substr(md5(uniqid(mt_rand(), true)) , 0, 8);
    }

    public function sendEmail($to, $subject, $message) {

        $headers = [];
        $content_type = function() { return 'text/html'; };

        add_filter( 'wp_mail_content_type', $content_type );
        wp_mail( $to, $subject, $message, $headers );
        remove_filter( 'wp_mail_content_type', $content_type );

        return;
    }
}