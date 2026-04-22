<?php
/**
 * Plugin Name: Elementor Whole Widget Link for Icon Box
 * Description: Adds a full widget link control to Elementor Icon Box under Advanced, with dynamic tags, new tab, nofollow, and custom attributes.
 * Version: 1.1.0
 * Author: srkpics
 * Author URI: https://sumonrahmankabbo.com/
 * Text Domain: elementor-whole-widget-link
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/*
|--------------------------------------------------------------------------
| 1. ADD CONTROL TO ALL WIDGETS
|--------------------------------------------------------------------------
*/

add_action('elementor/element/common/_section_style/after_section_end', function($element){

    $element->start_controls_section(
        'srk_global_widget_link_section',
        [
            'label' => 'Whole Widget Link',
            'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
        ]
    );

    $element->add_control(
        'srk_global_widget_link',
        [
            'label'         => 'Link',
            'type'          => \Elementor\Controls_Manager::URL,
            'placeholder'   => 'https://your-link.com',
            'show_external' => true,
            'dynamic'       => [
                'active' => true,
            ],
        ]
    );

    $element->end_controls_section();

}, 10, 1);


/*
|--------------------------------------------------------------------------
| 2. ADD LINK ATTRIBUTES TO WRAPPER
|--------------------------------------------------------------------------
*/

add_action('elementor/frontend/widget/before_render', function($widget){

    $settings = $widget->get_settings_for_display();

    if ( empty($settings['srk_global_widget_link']['url']) ) return;

    $widget->add_render_attribute(
        '_wrapper',
        'class',
        'srk-widget-link-wrap'
    );

    $widget->add_render_attribute(
        '_wrapper',
        'data-srk-link',
        esc_url($settings['srk_global_widget_link']['url'])
    );

    if ( ! empty($settings['srk_global_widget_link']['is_external']) ) {
        $widget->add_render_attribute('_wrapper', 'data-srk-target', '_blank');
    }

    if ( ! empty($settings['srk_global_widget_link']['nofollow']) ) {
        $widget->add_render_attribute('_wrapper', 'data-srk-rel', 'nofollow');
    }

}, 10, 1);


/*
|--------------------------------------------------------------------------
| 3. FRONTEND JS (GLOBAL CLICK HANDLER)
|--------------------------------------------------------------------------
*/

add_action('wp_footer', function(){
?>
<script>
document.addEventListener('click', function(e) {

    const widget = e.target.closest('.srk-widget-link-wrap');
    if (!widget) return;

    // prevent clicking inner links/buttons
    if (e.target.closest('a, button, input, select, textarea')) return;

    const url = widget.getAttribute('data-srk-link');
    if (!url) return;

    const target = widget.getAttribute('data-srk-target');
    const rel = widget.getAttribute('data-srk-rel');

    if (target === '_blank') {
        window.open(url, '_blank');
    } else {
        window.location.href = url;
    }

});
</script>
<?php
});


/*
|--------------------------------------------------------------------------
| 4. OPTIONAL CSS (UX IMPROVEMENT)
|--------------------------------------------------------------------------
*/

add_action('wp_head', function(){
?>
<style>
.srk-widget-link-wrap {
    cursor: pointer;
}
</style>
<?php
});