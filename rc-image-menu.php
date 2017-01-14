<?php
/**
 * Plugin Name: RC Image menu
 * Description: Image menu for ico bar
 * Version: 0.1
 * Author: Maxim Tarasenko
 * Author URI: http://realcoder.ru
 */


add_action( 'widgets_init', create_function( '', 'register_widget("rc_image_menu");' ) );

class rc_image_menu extends WP_Widget
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'rc_image_menu',
            'description' => 'Simple image menu for icon bar'
        );

        parent::__construct( 'rc_image_menu', 'RC Image menu', $widget_ops );

        add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
    }

    /**
     * Upload the Javascripts for the media uploader
     */
    public function upload_scripts()
    {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('upload_media_widget', plugin_dir_url(__FILE__) . 'upload-media.js', array('jquery'));

        wp_enqueue_style('thickbox');
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    public function widget( $args, $instance )
    {
        // Add any html to output the image in the $instance array

    	$images_html = array();


        $ul_li = (isset($instance['ul_li']) && $instance['ul_li'] == 1) ? true : false;

        foreach ( $instance['images'] as $name => $value )
        {

        	if(trim($value['image']) != ''){


                  if($ul_li){
                    $img = '<li><a href="'.$value['href'].'" target="_blank"><img src="'.$value['image'].'" alt="" ></a></li>';
                  }else{
                      $img = '<a href="'.$value['href'].'" target="_blank"><img src="'.$value['image'].'" alt="" ></a>';
                  }

        		
        	}else{
        		$img = '';
        	}

        
        	$images_html []= $img;
        }

     


        if($ul_li){
            print '<ul id="'.$instance['css_id'].'"  class="'.$instance['css_class'].'">';
        }

        print join( '', $images_html );
        
        if($ul_li){
            print '</ul>';
        }

    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
     public function update( $new_instance, $old_instance )
    {
        $instance          = $old_instance;
        $instance['title'] = esc_html( $new_instance['title'] );


        $instance['max_count'] = esc_html( $new_instance['max_count'] );

        $instance['css_class'] = esc_html( $new_instance['css_class'] );


        $instance['css_id'] = esc_html( $new_instance['css_id'] );

        $instance['ul_li'] = esc_html( $new_instance['ul_li'] );

        $instance['images'] = array();

        if ( isset ( $new_instance['images'] ) )
        {
            foreach ( $new_instance['images'] as $value )
            {
                if(trim($value['image']) != ''){
                      $instance['images'][] = array('href' => $value['href'], 'image' => $value['image']);
                }
                
            }
        }

        return $instance;
    }


    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void
     **/
    public function form( $instance )
    {
        $title = '';
        if(isset($instance['title']))
        {
            $title = $instance['title'];
        }


        $css_class = '';
        if(isset($instance['css_class']))
        {
            $css_class = $instance['css_class'];
        }


        $css_id = '';
        if(isset($instance['css_id']))
        {
            $css_id = $instance['css_id'];
        }


        $ul_li = 0;

        if(isset($instance['ul_li']) && $instance['ul_li'] == 1)
        {
            $ul_li_checked = 'checked="checked"';
        }



        $max_count = 10;
        if(isset($instance['max_count']))
        {
            $max_count = $instance['max_count'];
        }





        if(isset( $instance['images'] )) {
        	$images = $instance['images'];
		}else{
				
			$images = array_fill(0, $max_count, array('href' => '', 'image' => ''));
		}
        	 
        
        $field_num = count( $images );
        //$images[ $field_num + 1 ] = array('href' => '', 'image' => '');
        $images_html = array();
        $images_counter = 0;

        foreach ( $images as $name => $value )
        {

        	if(trim($value['image']) != ''){
        		$img = '<img src="'.$value['image'].'" alt="" style="max-width: 28px; max-height: 28px;"><br>';
        		$btn_text = 'Change Image';
        		$delete_btn = '<button class="button rc-image-menu-delete_image" type="button">Delete</button>';
        	}else{
        		$img = '';
        		$btn_text = 'Upload Image';
        		$delete_btn = '';
        	}

        	

        	$images_html []= '<p>'.($images_counter + 1).'<br>
                	<label>Href</label>
	                <input type="text" name="'.$this->get_field_name('images').'['.$images_counter.'][href]" value="'.$value['href'].'" class="widefat"> 
	            	<label>Image</label><br>
	            	'.$img.'
	                <input type="hidden" name="'.$this->get_field_name('images').'['.$images_counter.'][image]" value="'.$value['image'].'" class="widefat"> 
	                <input class="button upload_image_button" type="button" value="'.$btn_text.'" />
	                '.$delete_btn.'
        		</p>';

           
            $images_counter += 1;
        }



        for($i = $images_counter; $i < $max_count; $i++){

            $images_html []= '<p>'.($i + 1).'<br>
                    <label>Href</label>
                    <input type="text" name="'.$this->get_field_name('images').'['.$i.'][href]" value="" class="widefat"> 
                    <label>Image</label><br>
                    <input type="hidden" name="'.$this->get_field_name('images').'['.$i.'][image]" value="" class="widefat"> 
                    <input class="button upload_image_button" type="button" value="Upload Image" />
                </p>';

        }


       

       
        ?>
        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>">Title (not displayed) </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

         <p>
            <label for="<?php echo $this->get_field_name( 'max_count' ); ?>">Max count </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'max_count' ); ?>" name="<?php echo $this->get_field_name( 'max_count' ); ?>" type="text" value="<?php echo esc_attr( $max_count ); ?>" />
        </p>

         <p>
            <label for="<?php echo $this->get_field_name( 'ul_li' ); ?>">ul li container </label>
            <input class="widefat" <?php echo $ul_li_checked ?> id="<?php echo $this->get_field_id( 'ul_li' ); ?>" name="<?php echo $this->get_field_name( 'ul_li' ); ?>" type="checkbox" value="1" />
        </p>


        <p>
            <label for="<?php echo $this->get_field_name( 'css_class' ); ?>">UL css class</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'css_class' ); ?>" name="<?php echo $this->get_field_name( 'css_class' ); ?>" type="text" value="<?php echo esc_attr( $css_class ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_name( 'css_id' ); ?>">UL css id</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'css_id' ); ?>" name="<?php echo $this->get_field_name( 'css_id' ); ?>" type="text" value="<?php echo esc_attr( $css_id ); ?>" />
        </p>


       

        <p><strong>Images:</strong></p>


        <?  print join( '<br />', $images_html ); ?>
    <?php
    }
}
?>