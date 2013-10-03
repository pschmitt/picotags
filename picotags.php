<?php

/**
 * Picotags
 *
 * Adds page tagging functionality to Pico.
 *
 * @author Dan Reeves
 * @link https://github.com/HeyDanReeves/picotags/
 * @license http://opensource.org/licenses/MIT
 */
class Picotags {

    public $is_tag;
    public $current_tag;

    public function plugins_loaded()
    {

    }

    public function request_url(&$url)
    {
        // Set is_tag to true if the first four characters of the URL are 'tag/'
        if (substr($url, 0, 4) === 'tag/') {
            $this->is_tag = true;
        }
        else {
            $this->is_tag = false;
        }
        // If the URL does start with 'tag/', grab the rest of the URL
        if ($this->is_tag) $this->current_tag = substr($url, 4);
    }

    public function before_load_content(&$file)
    {

    }

    public function after_load_content(&$file, &$content)
    {

    }

    public function before_404_load_content(&$file)
    {

    }

    public function after_404_load_content(&$file, &$content)
    {

    }

    public function config_loaded(&$settings)
    {

    }

    public function before_read_file_meta(&$headers)
    {
        // Define tags variable
        $headers['tags'] = 'Tags';
    }

    public function file_meta(&$meta)
    {
        // Parses meta.tags to ensure it is an array
        if (isset($meta['tags']) && !is_array($meta['tags']) && $meta['tags'] !== '') {
            $meta['tags'] = split(',', $meta['tags']);
        }
    }

    public function content_parsed(&$content)
    {

    }

    public function get_page_data(&$data, $page_meta)
    {
        // Adds tags to page in pages
        $data['tags'] = split(',', $page_meta['tags']);
    }

    public function get_pages(&$pages, &$current_page, &$prev_page, &$next_page)
    {
        // If the URL starts with 'tag/' do this different logic
        if ($this->is_tag === true) {
            // Init $new_pages and $tag_list arrays
            $new_pages = array();
            $tag_list = array();
            // Loop through the pages
            foreach ($pages as $page) {
                // If the page has tags
                if ($page['tags']) {
                    // Loop through the tags
                    foreach ($page['tags'] as $tag) {
                        // And add them to the tag_list array
                        $tag_list[] = $tag;
                        // If the tag matches the current_tag
                        if ($tag === $this->current_tag) {
                            // Add that page to the new_pages
                            $new_pages[] = $page;
                        }
                    }
                }
            }
        // Add the tag list to the class scope, taking out duplicate or empty values
        $this->tag_list = array_unique(array_filter($tag_list));
        // Overwrite $pages with $new_pages
        $pages = $new_pages;
        }
    }

    public function before_twig_register()
    {

    }

    public function before_render(&$twig_vars, &$twig)
    {
        if ($this->is_tag) {
            // Override 404 header
            header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
            // Set page title to #TAG
            $twig_vars["meta"]["title"] = "#" . $this->current_tag;
            // Return current tag and list of all tags as Twig vars
            $twig_vars['current_tag'] = $this->current_tag; /* {{ current_tag }} is a string*/
            $twig_vars['tag_list'] = $this->tag_list; /* {{ tag_list }} in an array*/

        }
    }

    public function after_render(&$output)
    {

    }

}