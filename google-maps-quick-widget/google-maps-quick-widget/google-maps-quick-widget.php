<?php
/*
Plugin Name: Google Maps Quick Widget
Plugin URI: 
Description: A quick widget to add Google Maps as a widget
Version: 1.0
Author: Giancarlo Diana
Author URI: http://unmoscerinonelweb.com
License: GPL2
*/

/**
 * Register the widget
 */
add_action('widgets_init', create_function('', 'return register_widget("Widget_Google_Maps_Quick_Widget");'));

/**
 * Class Widget_Google_Maps_Quick_Widget
 */
class Widget_Google_Maps_Quick_Widget extends WP_Widget
{
  /** Basic Widget Settings */
  const WIDGET_NAME = "Google Maps Quick Widget";
  const WIDGET_DESCRIPTION = "A quick widget to add Google Maps as a widget";

  var $textdomain;
  var $fields;

  /**
   * Construct the widget
   */
  function __construct()
  {
    //We're going to use $this->textdomain as both the translation domain and the widget class name and ID
    $this->textdomain = strtolower(get_class($this));

    //Figure out your textdomain for translations via this handy debug print
    //var_dump($this->textdomain);

    //Add fields
    $this->add_field('title', 'Enter title', '', 'text');
    $this->add_field('latitude', 'Latitude', 'Enter latitude. Example: 49.1230992', 'text');
    $this->add_field('longitude', 'Longitude', 'Enter longitude. Example: 14.1230992', 'text');
    $this->add_field('api', 'Google Maps api', '', 'text');
    $this->add_field('msg', 'Dialog message', '', 'textarea');

    //Translations
    load_plugin_textdomain($this->textdomain, false, basename(dirname(__FILE__)) . '/languages' );

    //Init the widget
    parent::__construct($this->textdomain, __(self::WIDGET_NAME, $this->textdomain), array( 'description' => __(self::WIDGET_DESCRIPTION, $this->textdomain), 'classname' => $this->textdomain));
  }

  /**
   * Widget frontend
   *
   * @param array $args
   * @param array $instance
   */
  public function widget($args, $instance)
  {
    $title = apply_filters('widget_title', $instance['title']);

    /* Before and after widget arguments are usually modified by themes */
    echo $args['before_widget'];

    if (!empty($title)){
      echo $args['before_title'] . $title . $args['after_title'];
    }
    /* adding Gmap */
    
    /* Widget output here */
    $this->widget_output($args, $instance);

    /* After widget */
    echo $args['after_widget'];
  }
  
  /**
   * This function will execute the widget frontend logic.
   * Everything you want in the widget should be output here.
   */
  private function widget_output($args, $instance)
  {
    extract($instance);
    
    
    //adding gmap js file and css
    $dir = plugins_url()."/google-maps-quick-widget/";
    wp_enqueue_script('gmap-quick-widget-js', $dir.'gmap.js', array('jquery'), '1.0', true );
    wp_enqueue_style('gmap-quick-widget-css', $dir.'gmap.css', array(), '1.0', 'all');
    /**
     * This is where you write your custom code.
     */
    ?>
      <div class="gmap_container">
        <?php if($title!='') { print '<h4>'.$title.'</h4>'; } ?>
        <div class="gmap_area" id="gmap_area-<?=time()?>"></div>
        <div class="gmap_data" style="display:none;">
          <span class="title"><?php echo $title; ?></span>
          <span class="latitude"><?php echo $latitude; ?></span>
          <span class="longitude"><?php echo $longitude; ?></span>
          <span class="api"><?php echo $api; ?></span>
          <span class="msg"><?php echo $msg; ?></span>
        </div>
      </div>
    <?php
  }

  /**
   * Widget backend
   *
   * @param array $instance
   * @return string|void
   */
  public function form( $instance )
  {
    /* Generate admin for fields */
    foreach($this->fields as $field_name => $field_data)
    {
      if($field_data['type'] === 'text'):
        ?>
        <p>
          <label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" type="text" value="<?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?>" />
        </p>
      <?php
      elseif($field_data['type'] == 'textarea'):
        ?>
        <p>
          <label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
          <textarea class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" ><?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?></textarea>
        </p>
        <?php
      else:
        echo __('Error - Field type not supported', $this->textdomain) . ': ' . $field_data['type'];
      endif;
    }
  }

  /**
   * Adds a text field to the widget
   *
   * @param $field_name
   * @param string $field_description
   * @param string $field_default_value
   * @param string $field_type
   */
  private function add_field($field_name, $field_description = '', $field_default_value = '', $field_type = 'text')
  {
    if(!is_array($this->fields))
      $this->fields = array();

    $this->fields[$field_name] = array('name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'type' => $field_type);
  }

  /**
   * Updating widget by replacing the old instance with new
   *
   * @param array $new_instance
   * @param array $old_instance
   * @return array
   */
  public function update($new_instance, $old_instance)
  {
    return $new_instance;
  }
}