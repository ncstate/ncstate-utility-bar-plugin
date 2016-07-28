<?php

/**
 * Plugin Name: NC State Utility Bar
 * Plugin URI: https://brand.ncsu.edu
 * Description: Inserts the NC State Brand Utility Bar at the top of every page
 * Version: 0.1
 * Author: NC State University Communications
 *
 * GitHub Plugin URI: https://github.com/ncstate/ncstate-utility-bar-plugin
 */

namespace NCSU;

defined('ABSPATH') or die();

class UtilityBar
{
    private $options = array();

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'register_menu'));
        add_action('admin_init', array($this, 'register_settings'));

        $this->options = get_option('ncsu_ub_options');
    }

    public function enqueue_scripts()
    {
        //map WP option names to CDN recognized GET values
        $query_string = array(
            'googleCustomSearchCode' => $this->options['ub_google_code'],
            'placeholder' => $this->options['ub_search_placeholder'],
            'maxWidth' => $this->options['ub_max_width'],
            'color' => $this->options['ub_bar_color'],
            'showBrick' => $this->options['ub_tf_brick']
        );

        wp_enqueue_script('ncstate-utility-bar', 'https://cdn.ncsu.edu/brand-assets/utility-bar/ub.php?' . http_build_query($query_string), false, false, true);
    }

    public function register_menu()
    {
        add_options_page('NC State Utility Bar Options', 'NC State Utility Bar', 'manage_options', 'ncsu-utility-bar', array($this, 'options_page_callback'));
    }

    public function options_page_callback()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        } ?>
        <div class="wrap">
            <h2>NC State Brand Utility Bar</h2>
            <form method="post" action="options.php">

                <?php settings_fields('ncsu_ub_options'); ?>
                <?php do_settings_sections('ncsu_ub_settings'); ?>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function register_settings()
    {
        register_setting('ncsu_ub_options', 'ncsu_ub_options', array($this, 'options_validate'));
        add_settings_section('ncsu_ub_main_section', 'Settings', array($this, 'display_section'), 'ncsu_ub_settings');
        add_settings_field('ub_google_code', 'Google Custom Search Code', array($this, 'display_google_code'), 'ncsu_ub_settings', 'ncsu_ub_main_section');
        add_settings_field('ub_search_placeholder', 'Search Placeholder', array($this, 'display_placeholder'), 'ncsu_ub_settings', 'ncsu_ub_main_section');
        add_settings_field('ub_max_width', 'Max Width', array($this, 'display_width'), 'ncsu_ub_settings', 'ncsu_ub_main_section');
        add_settings_field('ub_bar_color', 'Bar Color', array($this, 'display_color'), 'ncsu_ub_settings', 'ncsu_ub_main_section');
        add_settings_field('ub_tf_brick', 'Use NC State Brick?', array($this, 'display_brick'), 'ncsu_ub_settings', 'ncsu_ub_main_section');
    }

    public function display_section()
    {
        ?>
        <p>The NC State brand utility bar offers a simple way to connect our thousands of websites and let Web users know where they are at all times. This element must appear at the top of all official NC State sites. No content or padding of any kind may appear above this bar. The utility bar is available in various color combinations (black, gray and red) consistent with official university colors, but it may not be edited or
            altered in any way beyond the options presented when embedding. The utility bar should only be used if the NC State logo is prominently displayed in the upper portion of your site.
        </p>
        <p>For certain entities operating under unique circumstances, the utility bar might not be required. Exceptions will be handled on a case-by-case basis. Web Communications will provide support for the utility bar but will rely on each unit’s internal Web or IT staff to implement the bar. If you have questions about implementation or exceptions, email Web Communications at <a href="mailto:web_feedback@ncsu.edu">web_feedback@ncsu.edu</a>.
        </p>

        <p><strong>All fields are optional.</strong></p>
        <?php
    }

    public function display_google_code()
    {
        ?>
        <input type="text" size=40 name="ncsu_ub_options[ub_google_code]" value="<?php echo $this->options['ub_google_code'] ?>" />

        <p><em>The search box within the utility bar can search both your own site and the entire ncsu.edu domain. To search your own site you must create a search engine through <a href="https://cse.google.com/cse/" target="_blank">Google Custom Search.</a> If this isn't set, the utility bar will only perform a global ncsu.edu search.</em></p>
        <?php
    }

    public function display_placeholder()
    {
        ?>
        <input type="text" name="ncsu_ub_options[ub_search_placeholder]" value="<?php echo preg_replace('/(\+)/', ' ', $this->options['ub_search_placeholder']); ?>" />
        <p><em>Customization of the placeholder text in the search bar.</em></p>
        <?php
    }

    public function display_width()
    {
        ?>
        <input type="text" name="ncsu_ub_options[ub_max_width]" value="<?php echo $this->options['ub_max_width'] ?>" />
        <p><em>The utility bar is responsive. The width value should be set to your site's maximum breakpoint to enable a fluid container width. If no width is set, the bar will align its contents to the default <a href='https://brand.ncsu.edu/bootstrap/css/#grid-options' target="_blank">Bootstrap container sizes</a> and breakpoints.</em></p>
        <?php
    }

    public function display_color()
    {
        ?>
        <select id="ncsu_utility_bar_color" name="ncsu_ub_options[ub_bar_color]">
            <option value="gray" <?php selected($this->options['ub_bar_color'], 'gray'); ?>>Gray</option>
            <option value="red" <?php selected($this->options['ub_bar_color'], 'red'); ?>>Red</option>
            <option value="black" <?php selected($this->options['ub_bar_color'], 'black'); ?>>Black</option>
        </select>
        <p><em>The color of the utility bar - Gray, Black, or Red</em></p>
        <?php
    }

    public function display_brick()
    {
        ?>
        <input type="checkbox" value="1" id="ncsu_utility_bar_brick" name="ncsu_ub_options[ub_tf_brick]" <?php checked($this->options['ub_tf_brick'], 1); ?> />
        <p><em>Choose whether to use the official 2x2 NC State brick logo in place of the default black and white 'NC State Home' button. This option may be used in lieu of prominently displaying the NC State logo in the upper portion of the site. Keep in mind the brick will hang and additional 30px further than the bottom of the utility bar and should be reasonably accommodated by your site's design.</em></p>
        <?php
    }


    public function options_validate($input)
    {
        //validate google custom code
        $newinput['ub_google_code'] = trim($input['ub_google_code']);
        if (empty($newinput['ub_google_code']) || !preg_match('/^[a-z0-9\:]{3,50}$/i', $newinput['ub_google_code'])) {
            $newinput['ub_google_code'] = null;
        }
        //validate search placeholder
        $newinput['ub_search_placeholder'] = trim($input['ub_search_placeholder']);
        if (empty($newinput['ub_search_placeholder']) || !preg_match('/^[a-z0-9_\s+-]/',
                $newinput['ub_search_placeholder'])
        ) {
            $newinput['ub_search_placeholder'] = null;
        }
        //validate width
        $newinput['ub_max_width'] = trim($input['ub_max_width']);
        if (empty($newinput['ub_max_width']) || !preg_match('/^[0-9]{2,5}$/i', $newinput['ub_max_width'])) {
            $newinput['ub_max_width'] = null;
        }
        //validate color
        $newinput['ub_bar_color'] = $input['ub_bar_color'];
        if (!$newinput['ub_bar_color'] == 'gray' || !$newinput['ub_bar_color'] == 'red' || !$newinput['ub_bar_color'] == 'black') {
            $newinput['ub_bar_color'] = null;
        }
        //validate showBrick
        if (!empty($input['ub_tf_brick']) && $input['ub_tf_brick'] == 1) {
            $newinput['ub_tf_brick'] = 1;
        } else {
            $newinput['ub_tf_brick'] = null;
        }

        return $newinput;
    }

}

new UtilityBar();
