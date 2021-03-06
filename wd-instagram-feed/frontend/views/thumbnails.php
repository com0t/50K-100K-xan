<?php

class WDI_Thumbnails_view {

  private $model;

  public function __construct($model) {
    $this->model = $model;
  }

  public function display() {
    global $user_feed_header_args;
    $this->pass_feed_data_to_js(); // @TODO. should be moved to shortcode.php
    $feed_row = $this->model->get_feed_row(); // @TODO. should be moved to shortcode.php
    $this->add_theme_styles(); // @TODO. should be moved to shortcode.php
    $this->generate_feed_styles($feed_row); // @TODO. should be moved to shortcode.php
    $style = $this->model->theme_row;
    $wdi_feed_counter = $this->model->wdi_feed_counter;
    $container_class = 'wdi_feed_theme_' . $style['id'] . ' wdi_feed_thumbnail_' . $style['id'];
    $wdi_data_ajax = defined('DOING_AJAX') && DOING_AJAX ? 'data-wdi_ajax=1' : '';
    ?>
    <div id="wdi_feed_<?php echo $wdi_feed_counter ?>" class="wdi_feed_main_container wdi_layout_th <?php echo $container_class; ?>" <?php echo $wdi_data_ajax; ?> >
      <?php wdi_feed_frontend_messages();?>
      <div id="wdi_spider_popup_loading_<?php echo $wdi_feed_counter ?>" class="wdi_spider_popup_loading"></div>
      <div id="wdi_spider_popup_overlay_<?php echo $wdi_feed_counter ?>" class="wdi_spider_popup_overlay" onclick="wdi_spider_destroypopup(1000)"></div>
      <div class="wdi_feed_container">
        <div class="wdi_feed_info">
          <div id="wdi_feed_<?php echo $wdi_feed_counter ?>_header" class='wdi_feed_header'></div>
          <div id="wdi_feed_<?php echo $wdi_feed_counter ?>_users" class='wdi_feed_users'>
            <?php
            if ( !empty($user_feed_header_args) ) {
              echo WDILibrary::user_feed_header_info( $user_feed_header_args );
            } ?>
          </div>
        </div>
        <?php
        if ($feed_row['feed_display_view'] === 'pagination' && $style['pagination_position_vert'] === 'top') {
          ?>
          <div id="wdi_pagination" class="wdi_pagination wdi_hidden">
            <div class="wdi_pagination_container"><i id="wdi_first_page"
                                                     title="<?php echo __('First Page', "wd-instagram-feed") ?>"
                                                     class="tenweb-i tenweb-i-step-backward wdi_pagination_ctrl wdi_disabled"></i><i
                id="wdi_prev" title="<?php echo __('Previous Page', "wd-instagram-feed") ?>"
                class="tenweb-i tenweb-i-arrow-left wdi_pagination_ctrl"></i><i id="wdi_current_page" class="wdi_pagination_ctrl"
                                                                                style="font-style:normal">1</i><i id="wdi_next"
                                                                                                                  title="<?php echo __('Next Page', "wd-instagram-feed") ?>"
                                                                                                                  class="tenweb-i tenweb-i-arrow-right wdi_pagination_ctrl"></i>
              <i id="wdi_last_page" title="<?php echo __('Last Page', "wd-instagram-feed") ?>"
                 class="tenweb-i tenweb-i-step-forward wdi_pagination_ctrl wdi_disabled"></i></div></div> <?php
        }
        ?>
        <div class="wdi_feed_wrapper <?php echo 'wdi_col_' . $feed_row['number_of_columns'] ?>" wdi-res='<?php echo 'wdi_col_' . $feed_row['number_of_columns'] ?>'></div>
        <div class="wdi_clear"></div>

        <?php switch ($feed_row['feed_display_view']) {
          case 'load_more_btn': {
            ?>
            <div class="wdi_load_more wdi_hidden">
              <div class="wdi_load_more_container">
                <div class="wdi_load_more_wrap">
                  <div class="wdi_load_more_wrap_inner">
                    <div class="wdi_load_more_text"><?php echo __('Load More', "wd-instagram-feed"); ?></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="wdi_spinner">
              <div class="wdi_spinner_container">
                <div class="wdi_spinner_wrap">
                  <div class="wdi_spinner_wrap_inner"><i class="wdi_load_more_spinner tenweb-i tenweb-i-spinner"></i></div>
                </div>
              </div>
            </div>
            <?php

            break;
          }
          case 'pagination': {
            if ($style['pagination_position_vert'] === 'bottom') {
              ?>
              <div id="wdi_pagination" class="wdi_pagination wdi_hidden">
                <div class="wdi_pagination_container"><i id="wdi_first_page"
                                                         title="<?php echo __('First Page', "wd-instagram-feed") ?>"
                                                         class="tenweb-i tenweb-i-step-backward wdi_disabled wdi_pagination_ctrl"></i><i
                    id="wdi_prev" title="<?php echo __('Previous Page', "wd-instagram-feed") ?>"
                    class="tenweb-i tenweb-i-arrow-left wdi_pagination_ctrl"></i><i id="wdi_current_page" class="wdi_pagination_ctrl"
                                                                                    style="font-style:normal">1</i><i id="wdi_next"
                                                                                                                      title="<?php echo __('Next Page', "wd-instagram-feed") ?>"
                                                                                                                      class="tenweb-i tenweb-i-arrow-right wdi_pagination_ctrl"></i>
                  <i id="wdi_last_page" title="<?php echo __('Last Page', "wd-instagram-feed") ?>"
                     class="tenweb-i tenweb-i-step-forward wdi_pagination_ctrl wdi_disabled"></i></div></div> <?php
            }

            break;
          }
          case 'infinite_scroll': {
            ?>
            <div id="wdi_infinite_scroll" class="wdi_infinite_scroll"></div> <?php
          }
        }

        if ($feed_row['feed_display_view'] === 'pagination') { ?>
          <div class="wdi_page_loading wdi_hidden"><div><div><img class="wdi_load_more_spinner" src="<?php echo WDI_URL ?>/images/ajax_loader.png"></div></div></div>
        <?php
        }
        ?>

      </div>
      <div class="wdi_front_overlay"></div>
    </div>
    <?php
  }

  public function pass_feed_data_to_js() {
    global $wdi_options;
    $feed_row = $this->model->get_feed_row();

    $users = isset($feed_row['feed_users']) ? json_decode($feed_row['feed_users']) : null;
    if($users === null) {
      $users = array();
    }

    $wdi_feed_counter = $this->model->wdi_feed_counter;
    $feed_row['access_token'] = WDILibrary::get_user_access_token($users);
    $feed_row['wdi_feed_counter'] = $wdi_feed_counter;

    wp_localize_script("wdi_frontend", 'wdi_feed_' . $wdi_feed_counter, array('feed_row' => $feed_row, 'data' => array(), 'usersData' => array(), 'dataCount' => 0));
    wp_localize_script("wdi_frontend", 'wdi_theme_' . $this->model->theme_row['id'], $this->model->theme_row);
    wp_localize_script("wdi_frontend", 'wdi_front', array('feed_counter' => $wdi_feed_counter));

    if ( WDILibrary::is_ajax() || WDILibrary::elementor_is_active() ) {
      wdi_load_frontend_scripts_ajax();
      echo '<style id="generate_feed_styles-inline-css">' . $this->generate_feed_styles( $feed_row, TRUE ) .'</style>';
    }
  }

  private function add_theme_styles(){

    $theme = $this->model->theme_row;

    require_once WDI_DIR . '/framework/WDI_generate_styles.php';
    $generator = new WDI_generate_styles($theme['id'], $theme);

    if($this->load_theme_css_file($generator) === true) {
      return;
    }

    if($generator->all_views_styles(true, true) === true &&
      $this->load_theme_css_file($generator) === true) {
      return;
    }

    echo '<style>' . $generator->get_css() . '</style>';
  }

  /**
   * @param $generator WDI_generate_styles
   * @return boolean
   * */
  private function load_theme_css_file($generator){
    $file_url = $generator->get_file_url();
    if($file_url !== "") {
      $theme_path_parts = pathinfo($file_url);
      if(WDILibrary::is_ajax() || WDILibrary::elementor_is_active()) {
        $style_tag = "<link rel='stylesheet' id='%s'  href='%s' type='text/css' media='all' />";
        echo sprintf($style_tag,$theme_path_parts['basename'], $file_url . '?key=' . $generator->get_file_key());
      }else{
        wp_enqueue_style($theme_path_parts['basename'], $file_url . '?key=' . $generator->get_file_key());
      }
      return true;
    } else {
      return false;
    }
  }

  public function generate_feed_styles( $feed_row, $return = FALSE ) {
    $style = $this->model->theme_row;
    $wdi_feed_counter = $this->model->wdi_feed_counter;
    $colNum = (100 / $feed_row['number_of_columns']);
    ob_start();
    ?>
      #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_header {
        display: <?php echo ($feed_row['display_header']=='1')? 'block' : 'none'?>; /*if display-header is true display:block*/
      }
      <?php
      if($feed_row['display_user_post_follow_number'] == '1'){
        $header_text_padding =(intval($style['user_img_width']) - intval($style['users_text_font_size']))/4;
      }else{
        $header_text_padding =(intval($style['user_img_width']) - intval($style['users_text_font_size']))/2;
      }
      ?>
      #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_user_text {
        padding-top: <?php echo $header_text_padding; ?>px;
      }

      #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_header_user_text h3 {
        margin-top: <?php echo $header_text_padding ?>px;
      }

      #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_media_info {
        display: <?php echo ($feed_row['display_user_post_follow_number'] == '1') ? 'block' : 'none'; ?>
      }

      #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item {
        width: <?php echo $colNum.'%'?>; /*thumbnail_size*/
        line-height: 0;
      }

      <?php  if($feed_row['disable_mobile_layout']=="0") { ?>
      @media screen and (min-width: 800px) and (max-width: 1024px) {
        #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item {
          width: <?php echo ($colNum<33.33) ? '33.333333333333%' : $colNum.'%'?>; /*thumbnail_size*/
          margin: 0;
          display: inline-block;
          vertical-align: top;
          overflow: hidden;
        }

        #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_container {
          width: 100%;
          margin: 0 auto;
          background-color: <?php echo $style['feed_container_bg_color']?>; /*feed_container_bg_color*/
        }

      }

      @media screen and (min-width: 480px) and (max-width: 800px) {
        #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item {
          width: <?php echo ($colNum<50) ? '50%' : $colNum.'%'?>; /*thumbnail_size*/
          margin: 0;
          display: inline-block;
          vertical-align: top;
          overflow: hidden;
        }

        #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_container {
          width: 100%;
          margin: 0 auto;
          background-color: <?php echo $style['feed_container_bg_color']?>; /*feed_container_bg_color*/
        }
      }

      @media screen and (max-width: 480px) {
        #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_item {
          width: <?php echo ($colNum<100) ? '100%' : $colNum.'%'?>; /*thumbnail_size*/
          margin: 0;
          display: inline-block;
          vertical-align: top;
          overflow: hidden;
        }

        #wdi_feed_<?php echo $wdi_feed_counter?> .wdi_feed_container {
          width: 100%;
          margin: 0 auto;
          background-color: <?php echo $style['feed_container_bg_color']?>; /*feed_container_bg_color*/
        }
      }
    <?php }
    $css = ob_get_contents();
    ob_end_clean();
    if ( $return ) {
      return $css;
    }

    wp_register_style( 'generate_feed_styles', false );
    wp_enqueue_style( 'generate_feed_styles' );
    wp_add_inline_style( 'generate_feed_styles', $css );
  }
}