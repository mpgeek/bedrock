<?php
/**
 * @file
 * Bedrock.
 */

/**
 * Implements hook_html_head_alter().
 */
function bedrock_html_head_alter(&$head_elements) {
  global $theme_key;
  $theme_name = $theme_key;

  // charset utf-8
  $head_elements['system_meta_content_type']['#attributes'] = array('charset' => 'utf-8');

  // Metatags for mobile
  // X-UA-Compatible
  if (bedrock_get_setting('chrome_edge', $theme_name)) {
    $head_elements['bedrock_meta_x_ua_compatible'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'http-equiv' => 'X-UA-Compatible',
        'content' => 'IE=edge, chrome=1',
      ),
      '#weight' => 1,
    );
  }

  // cleartype
  if (bedrock_get_setting('clear_type', $theme_name)) {
    $head_elements['bedrock_meta_cleartype'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'http-equiv' => 'cleartype',
        'content' => 'on',
      ),
      '#weight' => 2,
    );
  }

  // Viewport
  if ($bedrock_meta_viewport = bedrock_get_setting('bedrock_meta_viewport', $theme_name)) {
    $head_elements['bedrock_meta_viewport'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'viewport',
        'content' => check_plain($bedrock_meta_viewport),
      ),
      '#weight' => 3,
    );
  }

  // MobileOptimized
  if ($bedrock_meta_mobileoptimized = bedrock_get_setting('bedrock_meta_mobileoptimized', $theme_name)) {
    $head_elements['bedrock_meta_mobileoptimized'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'MobileOptimized',
        'content' => check_plain($bedrock_meta_mobileoptimized),
      ),
      '#weight' => 4,
    );
  }

  // HandheldFriendly
  if ($bedrock_meta_handheldfriendly = bedrock_get_setting('bedrock_meta_handheldfriendly', $theme_name)) {
    $head_elements['bedrock_meta_handheldfriendly'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'HandheldFriendly',
        'content' => check_plain($bedrock_meta_handheldfriendly),
      ),
      '#weight' => 5,
    );
  }

  // apple-mobile-web-app-capable
  if ($bedrock_meta_apple_mobile_web_app_capable = bedrock_get_setting('bedrock_meta_apple_mobile_web_app_capable', $theme_name)) {
    $head_elements['bedrock_meta_apple_mobile_web_app_capable'] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'name' => 'apple-mobile-web-app-capable',
        'content' => check_plain($bedrock_meta_apple_mobile_web_app_capable),
      ),
      '#weight' => 6,
    );
  }

  // Apple touch icons - low, medium and high (see the Apple docs on touch icons)
  if (bedrock_get_setting('enable_apple_touch_icons')) {
    $path_to_theme = drupal_get_path('theme', $theme_name);
    // low
    $apple_touch_icon_path_l = check_plain(bedrock_get_setting('apple_touch_icon_path_l', $theme_name));
    if (!empty($apple_touch_icon_path_l)) {
      $l = $path_to_theme . '/' . $apple_touch_icon_path_l;
      $touch_icon_l = file_create_url($l);
      $head_elements['bedrock_touch_icon_nokia'] = array(
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#weight' => -97,
        '#attributes' => array(
          'href' => $touch_icon_l,
          'rel' => 'shortcut icon',
        ),
      );
      $head_elements['bedrock_touch_icon_low'] = array(
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#weight' => -98,
        '#attributes' => array(
          'href' => $touch_icon_l,
          'rel' => 'apple-touch-icon-precomposed',
        ),
      );
    }
    // medium
    $apple_touch_icon_path_m = check_plain(bedrock_get_setting('apple_touch_icon_path_m', $theme_name));
    if (!empty($apple_touch_icon_path_m)) {
      $m = $path_to_theme . '/' . $apple_touch_icon_path_m;
      $touch_icon_m = file_create_url($m);
      $head_elements['bedrock_touch_icon_medium'] = array(
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#weight' => -99,
        '#attributes' => array(
          'href' => $touch_icon_m,
          'rel' => 'apple-touch-icon-precomposed',
          'sizes' => '72x72',
        ),
      );
    }
    // high
    $apple_touch_icon_path_h = check_plain(bedrock_get_setting('apple_touch_icon_path_h', $theme_name));
    if (!empty($apple_touch_icon_path_h)) {
      $h = $path_to_theme . '/' . $apple_touch_icon_path_h;
      $touch_icon_h = file_create_url($h);
      $head_elements['bedrock_touch_icon_high'] = array(
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#weight' => -100,
        '#attributes' => array(
          'href' => $touch_icon_h,
          'rel' => 'apple-touch-icon-precomposed',
          'sizes' => '114x114',
        ),
      );
    }
  }
}

/**
 * Implements hook_js_alter().
 */
function bedrock_js_alter(&$javascript) {
  // Use our own vesion of vertical-tabs.js for better error handling
  // @see http://drupal.org/node/607752
  if (isset($javascript['misc/vertical-tabs.js'])) {
    $file = drupal_get_path('theme', 'bedrock') . '/scripts/vertical-tabs.js';
    $javascript['misc/vertical-tabs.js'] = drupal_js_defaults($file);
  }
}

/**
 * Implements hook_page_alter().
 */
function bedrock_page_alter(&$page) {
  global $theme_key;
  $theme_name = $theme_key;

  // Get the menu item
  $menu_item = menu_get_item();

  // Theme taxonomy term pages sensibly, remove redundant and potentially empty
  // markup and wrap the node list in section elements with a class for theming.
  if ($menu_item['tab_root'] == 'taxonomy/term/%') {
    unset($page['content']['system_main']['term_heading']['#prefix']);
    unset($page['content']['system_main']['term_heading']['#suffix']);
    $page['content']['system_main']['nodes']['#prefix'] = '<section class="nodes">';
    $page['content']['system_main']['nodes']['#suffix'] = '</section>';
  }
}

/**
 * Implements hook_form_FORM_alter().
 */
function bedrock_form_alter(&$form, &$form_state, $form_id) {
  // Collapse Noggin fieldset by default
  if ($form_id == 'system_theme_settings') {
    if (module_exists('noggin')) {
      $form['noggin']['#collapsible'] = TRUE;
      $form['noggin']['#collapsed'] = TRUE;
    }
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * Modifies the advanced search form.
 */
function bedrock_form_search_form_alter(&$form, $form_state) {
  // The problem with Drupals standard Advanced search form is that each
  // criterion group is wrapped in a DIV, whereas it should be a fieldset with
  // a legend, this is better semantics and improves accessibility by
  // logically grouping field items.
  if (isset($form['module']) && $form['module']['#value'] == 'node' && user_access('use advanced search')) {
    // Keyword boxes:
    $form['advanced'] = array(
      '#type' => 'fieldset',
      '#title' => t('Advanced search'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#attributes' => array('class' => array('search-advanced')),
    );
    $form['advanced']['keywords-fieldset'] = array(
      '#type' => 'fieldset',
      '#title' => t('Keywords'),
      '#collapsible' => FALSE,
    );
    $form['advanced']['keywords-fieldset']['keywords'] = array(
      '#prefix' => '<div class="criterion">',
      '#suffix' => '</div>',
    );
    $form['advanced']['keywords-fieldset']['keywords']['or'] = array(
      '#type' => 'textfield',
      '#title' => t('Containing any of the words'),
      '#size' => 30,
      '#maxlength' => 255,
    );
    $form['advanced']['keywords-fieldset']['keywords']['phrase'] = array(
      '#type' => 'textfield',
      '#title' => t('Containing the phrase'),
      '#size' => 30,
      '#maxlength' => 255,
    );
    $form['advanced']['keywords-fieldset']['keywords']['negative'] = array(
      '#type' => 'textfield',
      '#title' => t('Containing none of the words'),
      '#size' => 30,
      '#maxlength' => 255,
    );
    // Node types:
    $types = array_map('check_plain', node_type_get_names());
    $form['advanced']['types-fieldset'] = array(
      '#type' => 'fieldset',
      '#title' => t('Types'),
      '#collapsible' => FALSE,
    );
    $form['advanced']['types-fieldset']['type'] = array(
      '#type' => 'checkboxes',
      '#prefix' => '<div class="criterion">',
      '#suffix' => '</div>',
      '#options' => $types,
    );
    $form['advanced']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Advanced search'),
      '#prefix' => '<div class="action advanced-search-submit">',
      '#suffix' => '</div>',
      '#weight' => 99,
    );
    // Languages:
    $language_options = array();
    foreach (language_list('language') as $key => $entity) {
      $language_options[$key] = $entity->name;
    }
    if (count($language_options) > 1) {
      $form['advanced']['lang-fieldset'] = array(
        '#type' => 'fieldset',
        '#title' => t('Languages'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      );
      $form['advanced']['lang-fieldset']['language'] = array(
        '#type' => 'checkboxes',
        '#prefix' => '<div class="criterion">',
        '#suffix' => '</div>',
        '#options' => $language_options,
      );
    }
    $form['#validate'][] = 'node_search_validate';
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 * Modify the User Login Block Form
 */
function bedrock_form_user_login_block_alter(&$form, &$form_state, $form_id) {
  global $theme_key;
  $theme_name = $theme_key;
  if (bedrock_get_setting('enable_markup_overrides', $theme_name)) {
    if (bedrock_get_setting('login_block_remove_links', $theme_name)) {
      // Remove the links
      if (isset($form['links'])) {
        unset($form['links']);
      }
    }
    if (bedrock_get_setting('login_block_remove_openid', $theme_name)) {
      if (module_exists('openid')) {
        // Remove OpenID elements
        unset($form['openid_links']);
        unset($form['openid_identifier']);
        unset($form['openid.return_to']);
      }
    }
    if (bedrock_get_setting('horizontal_login_block', $theme_name)) {
      // Move the links to the end of the form, so they are after the submit,
      // OpenID really messes up the weight and I can't be bothered fighting
      // Drupal to deal with it.
      if (isset($form['links'])) {
        $form['links']['#weight'] = 100;
      }
    }
  }
}

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 *
 * Modify field classes on node forms.
 */
function bedrock_form_node_form_alter(&$form, &$form_state, $form_id) {
  // Remove if #1245218 is backported to D7 core.
  foreach (array_keys($form) as $item) {
    if (strpos($item, 'field_') === 0) {
      if (!empty($form[$item]['#attributes']['class'])) {
        foreach ($form[$item]['#attributes']['class'] as &$class) {
          if (strpos($class, 'field-type-') === 0 || strpos($class, 'field-name-') === 0) {
            // Make the class different from that used in theme_field().
            $class = $class . '-form';
          }
        }
      }
    }
  }
}

/**
 * Set a class on the iframe body element for WYSIWYG editors.
 *
 * This allows you to easily override the background for the iframe body
 * element. This only works for the WYSIWYG module:
 * http://drupal.org/project/wysiwyg
 */
function bedrock_wysiwyg_editor_settings_alter(&$settings, &$context) {
  $settings['bodyClass'] = 'wysiwygeditor';
}

/**
 * Generate or get classes, mainly classes used on the body element and called
 * via themename_preprocess_html().
 */

/**
 * Return an array of classes to be used for body classes in html.tpl.php
 *
 * @param array $vars
 *   Passed in the bedrock_preprocess_html().
 * @param string $theme_name
 *   The active theme.
 */
function bedrock_generate_html_classes(&$vars, $theme_name) {
  $classes_array = &drupal_static(__FUNCTION__, array());
  if (empty($classes_array)) {
    // Extra classes?
    if (bedrock_get_setting('extra_page_classes', $theme_name)) {
      // Set a class based on the language
      if (function_exists('locale')) {
        $classes_array[] = 'lang-' . $vars['language']->language;
      }

      // Site name class, for multi site installs that need to target styles
      // at each site seperately (mitigates things like block-id clashes)
      if (!empty($vars['head_title_array']['name'])) {
        $head_title = check_plain($vars['head_title_array']['name']);
        $classes_array[] = 'site-name-' . drupal_html_class($head_title);
      }

      // Classes for theming based on context.
      if (!$vars['is_front']) {
        // Add unique class for each page.
        $path = drupal_get_path_alias($_GET['q']);
        // Add unique class for each website section.
        list($section, ) = explode('/', $path, 2);
        $arg = explode('/', $_GET['q']);
        if ($arg[0] === 'node') {
          if (isset($arg[1]) && $arg[1] === 'add') {
            $section = 'node-add';
          }
          elseif (isset($arg[2]) && is_numeric($arg[1]) && ($arg[2] === 'edit' || $arg[2] === 'delete')) {
            $section = 'node-' . $arg[2];
          }
        }
        $section = check_plain($section);
        $classes_array[] = drupal_html_class('section-' . $section);
      }
    }

    // Set class indicating whether this is a Views page.
    $vars['menu_item'] = menu_get_item();
    $is_views = $vars['menu_item']['page_callback'] === 'views_page';
    $classes_array[] = 'page-' . ($is_views ? '' : 'not-') . 'views';

    // Set class indicating whether this is a Panels page.
    $is_panels = FALSE;
    if (module_exists('panels')) {
      $is_panels = is_object(panels_get_current_page_display());
    }
    $classes_array[] = 'page-' . ($is_panels ? '' : 'not-') . 'panels';

    // Set class indicating whether tabs are displayed.
    $tasks = menu_local_tasks();
    $with_tabs = $tasks['tabs']['count'] > 1;
    $classes_array[] = 'with' . ($with_tabs ? '' : 'out') . '-tabs';
  }

  return $classes_array;
}

/**
 * Provides frequently used functions that get theme info, settings and
 * other data.
 */

/**
 * Retrieve a setting for the current theme or for a given theme.
 *
 * Bedrock's stripped down optimized version of theme_get_setting().
 *
 * @param $setting_name
 * @param null $theme
 * @see http://api.drupal.org/api/drupal/includes!theme.inc/function/theme_get_setting/7
 */
function bedrock_get_setting($setting_name, $theme = NULL) {
  $cache = &drupal_static(__FUNCTION__, array());

  // If no key is given, use the current theme if we can determine it.
  if (!isset($theme)) {
    $theme = !empty($GLOBALS['theme_key']) ? $GLOBALS['theme_key'] : '';
  }

  if (empty($cache[$theme])) {

    // Get the values for the theme-specific settings from the .info files
    if ($theme) {
      $themes = list_themes();
      $theme_object = $themes[$theme];

      // Create a list which includes the current theme and all its base themes.
      if (isset($theme_object->base_themes)) {
        $theme_keys = array_keys($theme_object->base_themes);
        $theme_keys[] = $theme;
      }
      else {
        $theme_keys = array($theme);
      }

      $cache[$theme] = array();
      foreach ($theme_keys as $theme_key) {
        if (!empty($themes[$theme_key]->info['settings'])) {
          $cache[$theme] = $themes[$theme_key]->info['settings'];
        }
      }

      // Get the saved theme-specific settings from the database.
      $cache[$theme] = array_merge($cache[$theme], variable_get('theme_' . $theme . '_settings', array()));
    }
  }

  return isset($cache[$theme][$setting_name]) ? $cache[$theme][$setting_name] : NULL;
}

/**
 * Return the info file array for a particular theme, usually the active theme.
 * Simple wrapper function for list_themes().
 *
 * @param string $theme_name
 */
function bedrock_get_info($theme_name) {
  $info = drupal_static(__FUNCTION__, array());
  if (empty($info)) {
    $lt = list_themes();
    foreach ($lt as $key => $value) {
      if ($theme_name == $key) {
        $info = $lt[$theme_name]->info;
      }
    }
  }

  return $info;
}

/**
 * Returns an array keyed by theme name.
 *
 * Return the all the info file data for a particular theme including base
 * themes. Parts of this function are shamelessly ripped from Drupal core's
 * _drupal_theme_initialize().
 *
 * @param string $theme_name
 *   The theme of interest; usually the active theme.
 */
function bedrock_get_info_trail($theme_name) {
  $info_trail = drupal_static(__FUNCTION__, array());
  if (empty($info_trail)) {
    $lt = list_themes();

    // First check for base themes and get info
    $base_theme = array();
    $ancestor = $theme_name;
    while ($ancestor && isset($lt[$ancestor]->base_theme)) {
      $ancestor = $lt[$ancestor]->base_theme;
      $base_theme[] = $lt[$ancestor];
    }
    foreach ($base_theme as $base) {
      $info_trail[$base->name]['info'] = $base->info;
    }

    // Now the active theme
    $info_trail[$theme_name]['info'] = $lt[$theme_name]->info;
  }

  return $info_trail;
}

/**
 * All preprocess functions for templates.
 * If you need to add or modify preprocess functions, do it in your sub-theme.
 */

/**
 * Preprocess variables for html.tpl.php
 */
function bedrock_preprocess_html(&$vars) {
  global $theme_key, $language;
  $theme_name = $theme_key;

  // Set variable for the base path
  $vars['base_path'] = base_path();

  // Get the info file data
  $info = bedrock_get_info($theme_name);

  // Use a proper attributes array for the html attributes
  $vars['html_attributes_array']['lang'][] = $language->language;
  $vars['html_attributes_array']['dir'][] = $language->dir;

  // Convert RDF Namespaces into structured data using drupal_attributes.
  $vars['rdf_namespaces'] = array();
  if (function_exists('rdf_get_namespaces')) {
    foreach (rdf_get_namespaces() as $prefix => $uri) {
      $prefixes[] = $prefix . ': ' . $uri;
    }
    $vars['rdf_namespaces_array']['prefix'] = implode(' ', $prefixes);
  }

  // Set the skip link target id
  $vars['skip_link_target'] = '#main-content';
  if (bedrock_get_setting('skip_link_target', $theme_name)) {
    $skip_link_target = bedrock_get_setting('skip_link_target', $theme_name);
    $vars['skip_link_target'] = check_plain($skip_link_target);
  }

  // Generate body classes
  if ($html_classes = bedrock_generate_html_classes($vars, $theme_name)) {
    foreach ($html_classes as $class_name) {
      $vars['classes_array'][] = $class_name;
    }
  }

  $vars['polyfills'] = bedrock_load_polyfills($theme_name);
}

/**
 * Preprocess variables for page.tpl.php
 */
function bedrock_preprocess_page(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Set up logo element
  if (bedrock_get_setting('toggle_logo', $theme_name)) {
    $vars['site_logo'] = drupal_static('bedrock_preprocess_page_site_logo');
    if (empty($vars['site_logo'])) {
      $logo_path = check_url($vars['logo']);
      $logo_alt = check_plain(variable_get('site_name', t('Site logo')));
      $logo_vars = array('path' => $logo_path, 'alt' => $logo_alt, 'attributes' => array('class' => 'site-logo'));
      $vars['logo_img'] = theme('image', $logo_vars);
      $vars['site_logo'] = $vars['logo_img'] ? l($vars['logo_img'], '<front>', array('attributes' => array('title' => t('Home page')), 'html' => TRUE)) : '';
    }
  }
  else {
    $vars['site_logo'] = '';
    $vars['logo_img'] = '';
    $vars['linked_site_logo'] = '';
  }

  // Site name
  $vars['site_name'] = &drupal_static('bedrock_preprocess_page_site_name');
  if (empty($vars['site_name'])) {
    $vars['site_name_title'] = variable_get('site_name', 'Drupal');
    $vars['site_name'] = l($vars['site_name_title'], '<front>', array('attributes' => array('title' => t('Home page'))));
    $vars['site_name_unlinked'] = $vars['site_name_title'];
  }

  // Site name visibility and other classes and variables
  $vars['site_name_attributes_array'] = array();
  $vars['visibility'] = '';

  // Build a variable for the main menu
  if (isset($vars['main_menu'])) {
    $vars['primary_navigation'] = theme('links', array(
      'links' => $vars['main_menu'],
      'attributes' => array(
        'class' => array('menu', 'primary-menu', 'clearfix'),
       ),
      'heading' => array(
        'text' => t('Main menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      )
    ));
  }

  // Build a variable for the secondary menu
  if (isset($vars['secondary_menu'])) {
    $vars['secondary_navigation'] = theme('links', array(
      'links' => $vars['secondary_menu'],
      'attributes' => array(
        'class' => array('menu', 'secondary-menu', 'clearfix'),
      ),
      'heading' => array(
        'text' => t('Secondary navigation'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      )
    ));
  }

  // Build variables for Primary and Secondary local tasks
  $vars['primary_local_tasks'] = menu_primary_local_tasks();
  $vars['secondary_local_tasks'] = menu_secondary_local_tasks();

  // Add back the $search_box var to D7
  if (module_exists('search')) {
    $search_box = drupal_get_form('search_form');
    $vars['search_box'] = '<div id="search-box">' . drupal_render($search_box) . '</div>';
  }

  // Remove the infernal feed icons
  if (bedrock_get_setting('feed_icons_hide', $theme_name)) {
    $vars['feed_icons'] = '';
  }

  // Strip stupid contextual-links-region class, wtf?
  $vars['classes_array'] = array_values(array_diff($vars['classes_array'], array('contextual-links-region')));

  // page attributes
  $vars['page_attributes_array']['id'][] = 'page';
  $vars['page_attributes_array']['class'] = $vars['classes_array'];

  // header attributes
  $vars['header_attributes_array']['id'][] = 'header';
  $vars['header_attributes_array']['class'][] = 'clearfix';
  $vars['header_attributes_array']['role'][] = 'banner';

  // branding attributes
  $vars['branding_attributes_array']['id'][] = 'branding';
  $vars['branding_attributes_array']['class'][] = 'branding-elements';
  $vars['branding_attributes_array']['class'][] = 'clearfix';

  // hgroup attributes
  $vars['hgroup_attributes_array']['id'][] = 'name-and-slogan';

  // site name attributes
  $vars['site_name_attributes_array']['id'][] = 'site-name';

  // site slogan attributes
  $vars['site_slogan_attributes_array']['id'][] = 'site-slogan';

  // main content header attributes
  $vars['content_header_attributes_array']['id'][] = 'main-content-header';
  $vars['content_header_attributes_array']['class'][] = 'clearfix';

  // footer attributes
  $vars['footer_attributes_array']['id'][] = 'footer';
  $vars['footer_attributes_array']['class'][] = 'clearfix';
  $vars['footer_attributes_array']['role'][] = 'contentinfo';

  // Work around a peculiar bug in Drupal 7 which incorrectly sets the page
  // title to "User account" for all three of these pages.
  if (arg(0) === 'user') {
    if (arg(1) === 'login' || arg(1) == '') {
      drupal_set_title(t('User login'));
    }
    if (arg(1) === 'password') {
      drupal_set_title(t('Request new password'));
    }
    if (arg(1) === 'register') {
      drupal_set_title(t('Create new account'));
    }
  }
}

/**
 * Preprocess variables for region.tpl.php
 */
function bedrock_preprocess_region(&$vars) {
}

/**
 * Preprocess variables for block.tpl.php
 */
function bedrock_preprocess_block(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Block subject, under certain conditions, is not set
  $vars['tag'] = 'div';
  $vars['title'] = '';

  if (isset($vars['block']->subject)) {
    if (!empty($vars['block']->subject)) {
      // Generate the wrapper element, if there's a title use section
      $vars['tag'] = 'section';

      // Use a $title variable instead of $block->subject
      $vars['title'] = $vars['block']->subject;
    }
    // subject can be set and empty, i.e. using <none>
    else {
      $vars['classes_array'][] = 'no-title';
    }
  }
  // sometimes subject is not set at all
  else {
    $vars['classes_array'][] = 'no-title';
  }

  // Search is never a section, its just a div
  if ($vars['block_html_id'] === 'block-search-form') {
    $vars['tag'] = 'div';
  }

  // Initialize and populate the inner wrapper variables
  $vars['inner_prefix'] = '<div class="block-inner clearfix">';
  $vars['inner_suffix'] = '</div>';

  // Use nav element for menu blocks and provide a suggestion for all of them
  $nav_blocks = array('navigation', 'main-menu', 'management', 'user-menu');
  if (in_array($vars['block']->delta, $nav_blocks)) {
    $vars['tag'] = 'nav';
    array_unshift($vars['theme_hook_suggestions'], 'block__menu');
  }
  $nav_modules = array('superfish', 'nice_menus', 'menu_block');
  if (in_array($vars['block']->module, $nav_modules)) {
    $vars['tag'] = 'nav';
    array_unshift($vars['theme_hook_suggestions'], 'block__menu');
  }

  // The menu bar region gets special treatment for the block template
  if ($vars['block']->region === 'menu_bar') {
    // They are always menu blocks, right?
    $vars['tag'] = 'nav';
  }

  // The menu bar region gets special treatment for the block template
  if ($vars['block']->region === 'menu_bar') {
    // Hide titles, very unlikey to want it show, ever
    $vars['title_attributes_array']['class'][] = 'element-invisible';
    $vars['classes_array'][] = 'menu-wrapper menu-bar-wrapper clearfix';
  }

  // Add extra classes if required
  if (bedrock_get_setting('extra_block_classes', $theme_name)) {

    // Zebra
    $vars['classes_array'][] = $vars['block_zebra'];

    // Position?
    if ($vars['block_id']) {
      $vars['classes_array'][] = 'first';
    }
    if (isset($vars['block']->last_in_region)) {
      $vars['classes_array'][] = 'last';
    }

    // Count
    $vars['classes_array'][] = 'block-count-' . $vars['id'];

    // Region
    $vars['classes_array'][] = drupal_html_class('block-region-' . $vars['block']->region);

    // Delta
    $vars['classes_array'][] = drupal_html_class('block-' . $vars['block']->delta);
  }

  // Add classes to theme the horizontal block option
  if (bedrock_get_setting('enable_markup_overrides', $theme_name)) {
    if (bedrock_get_setting('horizontal_login_block', $theme_name) && $vars['block']->module === 'user' && $vars['block']->delta === 'login') {
      $vars['classes_array'][] = 'lb-h';
      $vars['title_attributes_array']['class'][] = 'element-invisible';
    }
    if (bedrock_get_setting('slider_login_block', $theme_name) && $vars['block']->module === 'user' && $vars['block']->delta === 'login') {
      $vars['classes_array'][] = 'lb-s';
    }
  }

  // Give our block titles and content some additional class
  $vars['title_attributes_array']['class'][] = 'block-title';
  $vars['content_attributes_array']['class'][] = 'block-content';
  $vars['content_attributes_array']['class'][] = 'content';

  // Add Aria Roles via attributes
  switch ($vars['block']->module) {
    case 'system':
      switch ($vars['block']->delta) {
        case 'main':
          // Note: the "main" role goes in the page.tpl, not here.
          break;
        case 'help':
        case 'powered-by':
          $vars['attributes_array']['role'] = 'complementary';
          break;
        default:
          // Any other "system" block is a menu block.
          $vars['attributes_array']['role'] = 'navigation';
          break;
      }
      break;
    case 'menu':
    case 'menu_block':
    case 'blog':
    case 'book':
    case 'comment':
    case 'forum':
    case 'shortcut':
    case 'statistics':
      $vars['attributes_array']['role'] = 'navigation';
      break;
    case 'search':
      $vars['attributes_array']['role'] = 'search';
      break;
    case 'help':
    case 'aggregator':
    case 'locale':
    case 'poll':
    case 'profile':
      $vars['attributes_array']['role'] = 'complementary';
      break;
    case 'node':
      switch ($vars['block']->delta) {
        case 'syndicate':
          $vars['attributes_array']['role'] = 'complementary';
          break;
        case 'recent':
          $vars['attributes_array']['role'] = 'navigation';
          break;
      }
      break;
    case 'user':
      switch ($vars['block']->delta) {
        case 'login':
          $vars['attributes_array']['role'] = 'form';
          break;
        case 'new':
        case 'online':
          $vars['attributes_array']['role'] = 'complementary';
          break;
      }
      break;
  }
}

/**
 * Preprocess variables for panels_pane.tpl.php
 */
function bedrock_preprocess_panels_pane(&$vars) {
  // Top level wrapper
  $vars['tag'] = $vars['title'] ? 'section' : 'div';

  // Add the same classes as blocks
  if (empty($vars['title'])) {
    $vars['classes_array'][] = 'no-title';
  }
  $vars['classes_array'][] = 'block';

  // Use the attributes arrays to add classes.
  $vars['title_attributes_array']['class'][] = 'block-title';
  $vars['attributes_array']['class'] = $vars['classes_array'];
  $vars['content_attributes_array']['class'][] = 'block-content';
}

/**
 * Preprocess variables for field.tpl.php
 */
function bedrock_preprocess_field(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  $element = $vars['element'];

 // Set the top level element as either <section> (if the field label is showing),
 // or <div> if the field label is hidden.
  $vars['tag'] = 'section';
  if (isset($vars['label_hidden']) && $vars['label_hidden']) {
    $vars['tag'] = 'div';
  }

  $vars['field_view_mode'] = '';
  if (isset($element['#view_mode'])) {
    // add a view mode class to fields
    $vars['classes_array'][] = 'view-mode-' . $element['#view_mode'];
    // Set variable for view mode, appears to be not gettable in a template
    $vars['field_view_mode'] = $element['#view_mode'];
  }

  // Image fields
  if ($element['#field_type'] === 'image') {

    // Set some vars for image captioning, these always need to be initialized
    $vars['image_caption_teaser'] = FALSE;
    $vars['image_caption_full'] = FALSE;

    // Dont run anything if extensions or image settings are disabled
    if (bedrock_get_setting('enable_image_settings', $theme_name)) {

      // Reduce number of images in teaser view mode to single image
      if (bedrock_get_setting('one_image_teasers', $theme_name)) {
        if ($element['#view_mode'] == 'teaser') {
          $item = reset($vars['items']);
          $vars['items'] = array($item);
        }
      }

      // Captions
      if (bedrock_get_setting('image_caption_teaser', $theme_name)) {
        $vars['image_caption_teaser'] = TRUE;
      }
      if (bedrock_get_setting('image_caption_full', $theme_name)) {
        $vars['image_caption_full'] = TRUE;
      }
    }
  }
}

/**
 * Preprocess variables for node.tpl.php
 */
function bedrock_preprocess_node(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // article class to attempt backwards compatibility
  $vars['classes_array'][] = 'article';

  // Extra classes if required
  if (bedrock_get_setting('extra_article_classes', $theme_name)) {

    // Zebra
    $vars['classes_array'][] = $vars['zebra'];

    // Langauge
    if (module_exists('translation')) {
      if ($vars['node']->language) {
        $vars['classes_array'][] = 'node-lang-' . $vars['node']->language;
      }
    }

    // User picture?
    if (bedrock_get_setting('toggle_node_user_picture', $theme_name)) {
      if ($vars['display_submitted'] && !empty($vars['picture'])) {
        $vars['classes_array'][] = 'node-with-picture';
      }
    }

    // Class for each view mode, core assumes we only need to target teasers but neglects custom view modes or full
    if ($vars['view_mode'] !== 'teaser') {
      $vars['classes_array'][] = drupal_html_class('node-' . $vars['view_mode']);
    }
  }

  // Image alignment and caption classes
  if (bedrock_get_setting('enable_extensions', $theme_name)) {
    if (bedrock_get_setting('enable_image_settings', $theme_name)) {
      if ($vars['view_mode'] !== 'teaser') {
        if ($image_caption_full = bedrock_get_setting('image_caption_full', $theme_name)) {
          $vars['classes_array'][] = $image_caption_full;
        }
        if ($image_alignment_classes = bedrock_get_setting('image_alignment_classes', $theme_name)) {
          $vars['classes_array'][] = $image_alignment_classes;
        }
      }
      if ($vars['view_mode'] == 'teaser') {
        if ($image_caption_teaser = bedrock_get_setting('image_caption_teaser', $theme_name)) {
          $vars['classes_array'][] = $image_caption_teaser;
        }
        if ($image_alignment_teaser = bedrock_get_setting('image_alignment_teaser', $theme_name)) {
          $vars['classes_array'][] = $image_alignment_teaser;
        }
      }
    }
  }

  // ARIA Role
  $vars['attributes_array']['role'][] = 'article';

  // Classes and attributes
  $vars['title_attributes_array']['class'][] = 'node-title';
  $vars['content_attributes_array']['class'][] = 'node-content';
  $vars['title_attributes_array']['rel'][] = 'nofollow';

  // header, submitted and links wrappers have their own attributes
  $vars['header_attributes_array']['class'][] = 'node-header';
  $vars['footer_attributes_array']['class'][] = 'submitted';
  if ($vars['user_picture']) {
    $vars['footer_attributes_array']['class'][] = 'with-user-picture';
  }
  $vars['links_attributes_array']['class'][] = 'clearfix';

  //
  // bedrock Core builds additional time and date variables for use in templates
  //
  // datetime stamp formatted correctly to ISO8601
  $vars['datetime'] = format_date($vars['created'], 'custom', 'Y-m-d\TH:i:sO'); // PHP 'c' format is not proper ISO8601!

  // Publication date, formatted with time element
  $vars['publication_date'] = '<time datetime="' . $vars['datetime'] . '" pubdate="pubdate">' . $vars['date'] . '</time>';

  // Last update variables
  $vars['datetime_updated'] = format_date($vars['node']->changed, 'custom', 'Y-m-d\TH:i:sO');
  $vars['custom_date_and_time'] = date('jS F, Y - g:ia', $vars['node']->changed);

  // Last updated formatted in time element
  $vars['last_update'] = '<time datetime="' . $vars['datetime_updated'] . '" pubdate="pubdate">' . $vars['custom_date_and_time'] . '</time>';

  // Build the submitted variable used by default in node templates
  if (variable_get('node_submitted_' . $vars['node']->type, TRUE)) {
    $vars['submitted'] = t('Submitted by !username on !datetime',
      array(
        '!username' => $vars['name'],
        '!datetime' => $vars['publication_date'],
      )
    );
  }
  else {
    $vars['submitted'] = '';
  }

  // Unpublished?
  $vars['unpublished'] = ''; // Initialize for backwards compatibility
  if (!$vars['status']) {
    // Use the title prefix to render the unpublished message
    $vars['title_prefix']['unpublished']['#markup'] = '<p class="unpublished">' . t('Unpublished') . '</p>';
  }

  // Add nofollow to Book module print/export links
  if (isset($vars['content']['links']['book']['#links']['book_printer'])) {
    $vars['content']['links']['book']['#links']['book_printer']['attributes'] = array('rel' => array('nofollow'));
  }
}

/**
 * Preprocess variables for comment.tpl.php
 */
function bedrock_preprocess_comment(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Extra comment classes if required
  if (bedrock_get_setting('extra_comment_classes', $theme_name)) {

    // Zebra
    $vars['classes_array'][] = $vars['zebra'];

    // Position?
    if ($vars['id']) {
      $vars['classes_array'][] = 'first';
    }
    if ($vars['id'] === $vars['node']->comment_count) {
      $vars['classes_array'][] = 'last';
    }

    // Title hidden?
    if (bedrock_get_setting('comments_hide_title', $theme_name)) {
      $vars['classes_array'][] = 'comment-title-hidden';
    }

    // User picture?
    if (bedrock_get_setting('toggle_comment_user_picture', $theme_name)) {
      if (!empty($vars['picture'])) {
        $vars['classes_array'][] = 'comment-with-picture';
      }
    }

    // Signature?
    if (!empty($vars['signature'])) {
      $vars['classes_array'][] = 'comment-with-signature';
    }
  }

  // Classes for comment title
  $vars['title_attributes_array']['class'][] = 'comment-title';

  // Title hidden?
  if (bedrock_get_setting('enable_extensions', $theme_name)) {
    if (bedrock_get_setting('enable_markup_overrides', $theme_name)) {
      if (bedrock_get_setting('comments_hide_title', $theme_name)) {
        $vars['title_attributes_array']['class'][] = 'element-invisible';
      }
    }
  }

  // Classes for comment content
  $vars['content_attributes_array']['class'][] = 'comment-content';

  // header, submitted and links wrappers have their own attributes
  $vars['header_attributes_array']['class'][] = 'comment-header';
  $vars['footer_attributes_array']['class'][] = 'submitted';
  if ($vars['picture']) {
    $vars['footer_attributes_array']['class'][] = 'with-user-picture';
  }
  $vars['links_attributes_array']['class'][] = 'clearfix';

  // Build the submitted by and time elements
  $uri = entity_uri('comment', $vars['comment']);
  $uri['options'] += array('attributes' => array('rel' => 'bookmark'));
  $vars['title'] = l($vars['comment']->subject, $uri['path'], $uri['options']);
  $vars['permalink'] = l(t('Permalink'), $uri['path'], $uri['options']); // Permalinks are embedded in the time element, aka Wordpress
  $vars['created'] = '<span class="date-time permalink">' . l($vars['created'], $uri['path'], $uri['options']) . '</span>';
  $vars['datetime'] = format_date($vars['comment']->created, 'custom', 'Y-m-d\TH:i:s\Z'); // Generate the timestamp, PHP "c" format is wrong
  $vars['submitted'] = t('Submitted by !username on !datetime',
    array(
      '!username' => $vars['author'],
      '!datetime' => '<time datetime="' . $vars['datetime'] . '" pubdate="pubdate">' . $vars['created'] . '</time>',
    )
  );

  // Unpublished?
  $vars['unpublished'] = ''; // Initialize for backwards compatibility
  if ($vars['status'] === 'comment-unpublished') {
    // Use the title prefix to render the unpublished message
    $vars['title_prefix']['unpublished']['#markup'] = '<p class="unpublished">' . t('Unpublished') . '</p>';
  }
}

/**
 * Preprocess variables for the search block form.
 */
function bedrock_preprocess_search_block_form(&$vars) {
  // Changes the search form to use the "search" input element attribute (HTML5)
  // We have to replace the string because FAPI don't know what type=search is, i.e.
  // no way we can do this in a form alter hook.
  $vars['search_form'] = str_replace('type="text"', 'type="search"', $vars['search_form']);
}

/**
 * Preprocess variables for aggregator-item.tpl.php
 */
function bedrock_preprocess_aggregator_item(&$vars) {
  $item = $vars['item'];
  // We want the same highly accurate time stamp feature as nodes and comments
  $vars['datetime'] = format_date($item->timestamp, 'custom', 'Y-m-d\TH:i:sO');
  // Give our aggregator items some class
  $vars['classes_array'][] = 'feed-item clearfix';
  $vars['title_attributes_array']['class'][] = 'title feed-item-title';
  $vars['content_attributes_array']['class'][] = 'content feed-item-content';
}

/**
 * Preprocess variables for bedrock_menubar().
 */
function bedrock_preprocess_menubar(&$vars) {
  $type = $vars['type'];
  $vars['menubar_id'] = $type . '-menu-bar';
  $vars['classes_array'][] = 'nav clearfix';
  $vars['content_attributes_array']['role'][] = 'navigation';
  $vars['content_attributes_array']['class'][] = $type . '-menu-wrapper menu-wrapper clearfix';
  // Add suggstions per menu type
  $vars['theme_hook_suggestions'][] = 'menubar__' . $type;
}

/**
 * Preprocess variables for the username.
 */
function bedrock_preprocess_username(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;
  // Add rel=author for SEO and supporting search engines
  if (bedrock_get_setting('enable_extensions', $theme_name)) {
    if (bedrock_get_setting('enable_markup_overrides', $theme_name)) {
      if (bedrock_get_setting('rel_author', $theme_name)) {
        if (isset($vars['link_path'])) {
          $vars['link_attributes']['rel'][] = 'author';
        }
        else {
          $vars['attributes_array']['rel'][] = 'author';
        }
      }
    }
  }
}

/**
 * Preprocess variables for theme_image().
 */
function bedrock_preprocess_image(&$vars) {
  // Initialize the variable if there isn't one
  if (!isset($vars['attributes']['class'])) {
    $vars['attributes']['class'] = array();
  }

  // Some modules set the style name even when its empty, such as User Badges
  // module, so initialize the class name variable with a "none" class
  $style_name_class = 'none';

  // If there really is a style name use it for the class
  if (isset($vars['style_name']) && !empty($vars['style_name'])) {
    $style_name_class = drupal_html_class($vars['style_name']);
  }

  // In the first instance we assume class attributes is an array, as it should be,
  // and add the image style class
  if (is_array($vars['attributes']['class'])) {
    $vars['attributes']['class'][] = 'image-style-' . $style_name_class;
  }
  else {
    // Else it's a string, workaround for Media module bug: http://drupal.org/node/1722146
    // and User Badges bug: http://drupal.org/node/1748394
    $vars['attributes']['class'] .= ' image-style-' . $style_name_class;
  }
}

/**
 * Preprocess variables for maintenance-page.tpl.php
 */
function bedrock_preprocess_maintenance_page(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  $vars['polyfills'] = bedrock_load_polyfills($theme_name);

  // Load the colors stylesheet for the active color scheme. This only works
  // for maintenance mode, when there is a database error the default color
  // scheme will be used.
  if (module_exists('color')) {
    $color_file = variable_get('color_' . $theme_name . '_stylesheets', NULL);
    if (file_exists($color_file[0])) {
      drupal_add_css($color_file[0], array(
        'group' => CSS_THEME,
        'weight' => 99,
        )
      );
    }
  }
}

/**
 * All Process functions for templates and theme fucntions.
 *
 * If you need to add or modify process functions do it in your sub-theme.
 */

/**
 * Process variables for html.tpl.php
 */
function bedrock_process_html(&$vars) {
  // Flatten attributes arrays
  $vars['html_attributes'] = empty($vars['html_attributes_array']) ? '' : drupal_attributes($vars['html_attributes_array']);

  // $rdf_namespaces is kept to maintain backwards compatibility, and because we
  // only want this to print once in html.tpl.php, and not in every conditional
  // comment for IE.
  $vars['rdf_namespaces'] = empty($vars['rdf_namespaces_array']) ? '' : drupal_attributes($vars['rdf_namespaces_array']);
}

/**
 * Process variables for the html tag
 */
function bedrock_process_html_tag(&$vars) {
  $tag = &$vars['element'];
  if ($tag['#tag'] === 'style' || $tag['#tag'] === 'script') {
    // Remove redundant type attribute and CDATA comments.
    unset($tag['#attributes']['type'], $tag['#value_prefix'], $tag['#value_suffix']);
    // Remove media="all" but leave others unaffected.
    if (isset($tag['#attributes']['media']) && $tag['#attributes']['media'] === 'all') {
      unset($tag['#attributes']['media']);
    }
  }
}

/**
 * Process variables for page.tpl.php
 */
function bedrock_process_page(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Attributes
  // @todo: Make this a loop to avoid so much repetition.
  $vars['page_attributes'] = empty($vars['page_attributes_array']) ? '' : drupal_attributes($vars['page_attributes_array']);
  $vars['header_attributes'] = empty($vars['header_attributes_array']) ? '' : drupal_attributes($vars['header_attributes_array']);
  $vars['branding_attributes'] = empty($vars['branding_attributes_array']) ? '' : drupal_attributes($vars['branding_attributes_array']);
  $vars['hgroup_attributes'] = empty($vars['hgroup_attributes_array']) ? '' : drupal_attributes($vars['hgroup_attributes_array']);
  $vars['site_name_attributes'] = empty($vars['site_name_attributes_array']) ? '' : drupal_attributes($vars['site_name_attributes_array']);
  $vars['site_slogan_attributes'] = empty($vars['site_slogan_attributes_array']) ? '' : drupal_attributes($vars['site_slogan_attributes_array']);
  $vars['content_header_attributes'] = empty($vars['content_header_attributes_array']) ? '' : drupal_attributes($vars['content_header_attributes_array']);
  $vars['footer_attributes'] = empty($vars['footer_attributes_array']) ? '' : drupal_attributes($vars['footer_attributes_array']);

  // Theme the menu bars
  if (!empty($vars['primary_navigation'])) {
    $vars['primary_navigation'] = theme('menubar', array('menu' => $vars['primary_navigation'], 'type' => 'primary'));
  }
  if (!empty($vars['secondary_navigation'])) {
    $vars['secondary_navigation'] = theme('menubar', array('menu' => $vars['secondary_navigation'], 'type' => 'secondary'));
  }

  // Generate the wrapper element for the main content
  $vars['tag'] = $vars['title'] ? 'section' : 'div';

  // Remove the frontpage title if set in theme settings
  if (bedrock_get_setting('frontpage_remove_title') && $vars['is_front']) {
    $vars['title'] = '';
  }

  // Page template suggestions for Panels pages
  if (module_exists('page_manager')) {
    if ($panel_page = page_manager_get_current_page()) {
      // add a generic suggestion for all panel pages
      $suggestions[] = 'page__panels';
      // add the panel page machine name to the template suggestions
      $suggestions[] = 'page__' . $panel_page['name'];
      // merge the suggestions in to the existing suggestions (as more specific than the existing suggestions)
      $vars['theme_hook_suggestions'] = array_merge($vars['theme_hook_suggestions'], $suggestions);
    }
  }
}

/**
 * Process variables for region.tpl.php
 */
function bedrock_process_region(&$vars) {
  // Initialize and populate the outer wrapper variables
  $vars['outer_prefix'] = '<div class="' . $vars['classes'] . '">';
  $vars['outer_suffix'] = '</div>';

  // Initialize and populate the inner wrapper variables
  $vars['inner_prefix'] = '<div class="region-inner clearfix">';
  $vars['inner_suffix'] = '</div>';

  // Some regions need different or no markup
  // Use a region template with no wrappers for the main content
  if ($vars['region'] === 'content') {
    $vars['outer_prefix'] = '';
    $vars['outer_suffix'] = '';
    $vars['inner_prefix'] = '';
    $vars['inner_suffix'] = '';
  }
  // Menu bar needs an id, nav class and no inner wrapper
  if ($vars['region'] === 'menu_bar') {
    $vars['outer_prefix'] = '<div id="menu-bar" class="nav clearfix">';
    $vars['inner_prefix'] = '';
    $vars['inner_suffix'] = '';
  }
}

/**
 * Process variables for block.tpl.php
 */
function bedrock_process_block(&$vars) {
  // Now we know all the block $tag's, we can generate our wrapper, $tag is
  // set in preprocess. We cant introduce these in preprocess due to attributes
  // and classes not being flattened untill we hit process.
  $vars['outer_prefix'] = '<' . $vars['tag'] . ' id="' . $vars['block_html_id'] . '" class="' . $vars['classes'] . '" ' . $vars['attributes'] . '>';
  $vars['outer_suffix'] = '</' . $vars['tag'] . '>';

  // Wrap the content variable in a div with attributes
  $vars['content_processed'] = '<div' . $vars['content_attributes'] . '>' . $vars['content'] . '</div>';

  // The menu bar region gets special treatment for the block template
  if ($vars['block']->region === 'menu_bar') {
    $vars['inner_prefix'] = '';
    $vars['inner_suffix'] = '';
    $vars['content_processed'] = $vars['content']; // remove the default wrapper
  }

  // Some blocks look bad with wrappers so we strip them
  if ($vars['block']->region === 'content') {
    $vars['inner_prefix'] = '';
    $vars['inner_suffix'] = '';
    $vars['content_processed'] = $vars['content'];
  }
  if ($vars['block']->module === 'panels_mini') {
    $vars['inner_prefix'] = '';
    $vars['inner_suffix'] = '';
  }

  // Provide additional suggestions so the block__menu suggestion can be overridden easily
  $vars['theme_hook_suggestions'][] = 'block__' . $vars['block']->region . '__' . $vars['block']->module;
  $vars['theme_hook_suggestions'][] = 'block__' . $vars['block']->region . '__' . $vars['block']->delta;
}

/**
 * Process variables for node.tpl.php
 */
function bedrock_process_node(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Strip default drupal classes if not required
  if (!bedrock_get_setting('extra_article_classes', $theme_name)) {
    $classes = explode(' ', $vars['classes']);
    $striped_classes = array('node-sticky', 'node-promoted', 'node-teaser', 'node-preview');
    foreach ($striped_classes as $class) {
      if (in_array($class, $classes)) {
        $classes = str_replace($class, '', $classes);
      }
    }
    $vars['classes'] = trim(implode(' ', $classes));
  }

  // Flatten the additional wrapper attributes array
  $vars['header_attributes'] = empty($vars['header_attributes_array']) ? '' : drupal_attributes($vars['header_attributes_array']);
  $vars['footer_attributes'] = empty($vars['footer_attributes_array']) ? '' : drupal_attributes($vars['footer_attributes_array']);
  $vars['links_attributes'] = empty($vars['links_attributes_array']) ? '' : drupal_attributes($vars['links_attributes_array']);
}

/**
 * Process variables for comment.tpl.php
 */
function bedrock_process_comment(&$vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Strip default drupal classes if not required.
  if (!bedrock_get_setting('extra_comment_classes', $theme_name)) {
    $classes = explode(' ', $vars['classes']);
    $striped_classes = array('comment-by-anonymous', 'comment-by-node-autho', 'comment-by-viewer', 'comment-new');
    foreach ($striped_classes as $class) {
      if (in_array($class, $classes)) {
        $classes = str_replace($class, '', $classes);
      }
    }
    $vars['classes'] = trim(implode(' ', $classes));
  }

  // Flatten the additional wrapper attributes array
  $vars['header_attributes'] = empty($vars['header_attributes_array']) ? '' : drupal_attributes($vars['header_attributes_array']);
  $vars['footer_attributes'] = empty($vars['footer_attributes_array']) ? '' : drupal_attributes($vars['footer_attributes_array']);
  $vars['links_attributes'] = empty($vars['links_attributes_array']) ? '' : drupal_attributes($vars['links_attributes_array']);
}

/**
 * Process variables for bedrock_menubar().
 */
function bedrock_process_menubar(&$vars) {
  // The default theme implementation is a function, so template_process() does
  // not automatically run, so we need to flatten the classes and attributes
  // here. For best performance, only call drupal_attributes() when needed, and
  // note that template_preprocess_menubar() does not initialize the
  // *_attributes_array variables.
  $vars['classes'] = implode(' ', $vars['classes_array']);
  $vars['attributes'] = empty($vars['attributes_array']) ? '' : drupal_attributes($vars['attributes_array']);
  $vars['content_attributes'] = empty($vars['content_attributes_array']) ? '' : drupal_attributes($vars['content_attributes_array']);
}

/**
 * Custom theme functions and theme function overrides.
 *
 * If you need to add or modify theme functions do it in your sub-theme.
 */

/**
 * Implements hook_theme().
 *
 * @param $existing
 * @param $type
 * @param $theme
 * @param $path
 *
 * @see http://api.drupal.org/api/drupal/modules!system!system.api.php/function/hook_theme/7
 */
function bedrock_theme($existing, $type, $theme, $path) {
  return array(
    'menubar' => array(
      'render element' => 'element',
    ),
  );
}

/**
 * Returns HTML for a menubar.
 *
 * The contents is normally one of Drupals magic menu variables, such as the
 * Main menu or User menu (secondary menu), but could be any menu you wish to
 * wrap in navigation menu type markup and classes.
 *
 * @param $vars
 * An array containing:
 *  - $menubar_id: CSS id for theming the menubar
 *  - $menu: Holds the themed menu (normally using theme_links())
 *  - the usual $classes, $attributes, $content attributes etc
 *
 * @see bedrock_preprocess_menubar()
 * @see bedrock_process_menubar()
 */
function bedrock_menubar($vars) {
  $output = '';
  $output .= '<div id="' . $vars['menubar_id'] . '" class="' . $vars['classes'] . '"' . $vars['attributes'] . '>';
  $output .= '<nav ' . $vars['content_attributes'] . '>';
  $output .= $vars['menu'];
  $output .= '</nav></div>';
  return $output;
}

/**
 * Returns HTML for a breadcrumb trail.
 *
 * bedrock impliments breadcrumb trails as an ordered list, wrapping each
 * crumb in li elements and the seperators in span elements to make life easier
 * for themers. Additionally .crumb, .crumb-first and .crumb-last classes are
 * printed on the li elements.
 *
 * @param $vars
 *   An associative array containing:
 *   - breadcrumb: An array containing the breadcrumb links.
 */
function bedrock_breadcrumb($vars) {
  global $theme_key;
  $theme_name = $theme_key;

  $breadcrumb = $vars['breadcrumb'];

  if (bedrock_get_setting('breadcrumb_display', $theme_name)) {

    if (!bedrock_get_setting('breadcrumb_home', $theme_name)) {
      array_shift($breadcrumb);
    }

    // Remove the rather pointless breadcrumbs for reset password and user
    // register pages, they link to the page you are on.
    if (arg(0) === 'user' && (arg(1) === 'password' || arg(1) === 'register')) {
      array_pop($breadcrumb);
    }

    if (!empty($breadcrumb)) {
      $separator = filter_xss_admin(bedrock_get_setting('breadcrumb_separator', $theme_name));

      // Push the page title onto the end of the breadcrumb array
      if (bedrock_get_setting('breadcrumb_title', $theme_name)) {
        $breadcrumb[] = '<span class="crumb-title">' . drupal_get_title() . '</span>';
      }

      $class = 'crumb';
      end($breadcrumb);
      $last = key($breadcrumb);

      $output = '';
      if (bedrock_get_setting('breadcrumb_label', $theme_name)) {
        $output = '<div id="breadcrumb" class="clearfix"><nav class="breadcrumb-wrapper with-breadcrumb-label clearfix" role="navigation">';
        $output .= '<h2 class="breadcrumb-label">' . t('You are here') . '</h2>';
      }
      else {
        $output = '<div id="breadcrumb" class="clearfix"><nav class="breadcrumb-wrapper clearfix" role="navigation">';
        $output .= '<h2 class="element-invisible">' . t('You are here') . '</h2>';
      }
      $output .= '<ol id="crumbs" class="clearfix">';
      foreach ($breadcrumb as $key => $val) {
        if ($key == $last && count($breadcrumb) != 1) {
          $class = 'crumb crumb-last';
        }
        if ($key == 0) {
          $output .= '<li class="' . $class . ' crumb-first">' . $val . '</li>';
        }
        else {
          $output .= '<li class="' . $class . '"><span class="crumb-separator">' . $separator . '</span>' . $val . '</li>';
        }
      }
      $output .= '</ol></nav></div>';

      return $output;
    }
  }
}

/**
 * Returns HTML for status and/or error messages, grouped by type.
 *
 * bedrock adds a div wrapper with CSS id.
 *
 * An invisible heading identifies the messages for assistive technology.
 * Sighted users see a colored box. See http://www.w3.org/TR/WCAG-TECHS/H69.html
 * for info.
 *
 * @param $vars
 *   An associative array containing:
 *   - display: (optional) Set to 'status' or 'error' to display only messages
 *     of that type.
 */
function bedrock_status_messages($vars) {
  $display = $vars['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );
  foreach (drupal_get_messages($display) as $type => $messages) {
    $output .= "<div id=\"messages\"><div class=\"messages $type\">";
    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="element-invisible">' . $status_heading[$type] . "</h2>";
    }
    if (count($messages) > 1) {
      $output .= " <ul>";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>";
      }
      $output .= " </ul>";
    }
    else {
      $output .= $messages[0];
    }
    $output .= "</div></div>";
  }
  return $output;
}

/**
 * Returns HTML for a list or nested list of items.
 *
 * bedrock overrides this in order to insert extra classes into list
 * items, including first, last and odd/even zebra classes.
 *
 * @param array $vars
 *   An associative array containing:
 *   - items: An array of items to be displayed in the list. If an item is a
 *     string, then it is used as is. If an item is an array, then the "data"
 *     element of the array is used as the contents of the list item. If an item
 *     is an array with a "children" element, those children are displayed in a
 *     nested list. All other elements are treated as attributes of the list
 *     item element.
 *   - title: The title of the list.
 *   - type: The type of list to return (e.g. "ul", "ol").
 *   - attributes: The attributes applied to the list element.
 */
function bedrock_item_list($vars) {
  global $theme_key;
  $theme_name = $theme_key;

  $items = $vars['items'];
  $title = $vars['title'];
  $type = $vars['type'];
  $attributes = $vars['attributes'];

  $output = '<div class="item-list">';

  if (isset($title) && $title !== '') {
    $output .= '<h3>' . $title . '</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    foreach ($items as $i => $item) {
      $attributes = array();
      $children = array();
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }

      if (count($children) > 0) {
        // Render nested list.
        $data .= theme_item_list(array('items' => $children, 'title' => NULL, 'type' => $type, 'attributes' => $attributes));
      }

      if (bedrock_get_setting('extra_item_list_classes', $theme_name)) {
        if ($i & 1) {
          $attributes['class'][] = 'odd';
        }
        else {
          $attributes['class'][] = 'even';
        }
        if ($i == 0) {
          $attributes['class'][] = 'first';
        }
        if ($i == $num_items - 1) {
          $attributes['class'][] = 'last';
        }
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>"; // no new line!
    }
    $output .= "</$type>";
  }
  $output .= '</div>';

  return $output;
}

/**
 * Returns HTML for a wrapper for a menu sub-tree.
 *
 * bedrock overrides this to insert the clearfix class.
 *
 * @param $vars
 *   An associative array containing:
 *   - tree: An HTML string containing the tree's items.
 *
 * @see template_preprocess_menu_tree()
 */
function bedrock_menu_tree($vars) {
  return '<ul class="menu clearfix">' . $vars['tree'] . '</ul>';
}

/**
 * Returns HTML for a menu link and submenu.
 *
 * bedrock overrides this to insert extra classes including a depth
 * class and a menu id class. It can also wrap menu items in span elements.
 *
 * @param $vars
 *   An associative array containing:
 *   - element: Structured array data for a menu link.
 */
function bedrock_menu_link(array $vars) {
  global $theme_key;
  $theme_name = $theme_key;

  $element = $vars['element'];
  $sub_menu = '';

  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }

  if (bedrock_get_setting('extra_menu_classes', $theme_name) && !empty($element['#original_link'])) {
    if (!empty($element['#original_link']['depth'])) {
      $element['#attributes']['class'][] = 'menu-depth-' . $element['#original_link']['depth'];
    }
    if (!empty($element['#original_link']['mlid'])) {
      $element['#attributes']['class'][] = 'menu-item-' . $element['#original_link']['mlid'];
    }
  }

  if (bedrock_get_setting('menu_item_span_elements', $theme_name) && !empty($element['#title'])) {
    $element['#title'] = '<span>' . $element['#title'] . '</span>';
    $element['#localized_options']['html'] = TRUE;
  }

  if (bedrock_get_setting('unset_menu_titles', $theme_name) && !empty($element['#localized_options']['attributes']['title'])) {
    unset($element['#localized_options']['attributes']['title']);
  }

  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>";
}

/**
 * Returns HTML for a set of links.
 *
 * @param $vars
 *   An associative array containing:
 *   - links: An associative array of links to be themed. The key for each link
 *     is used as its CSS class. Each link should be itself an array, with the
 *     following elements:
 *     - title: The link text.
 *     - href: The link URL. If omitted, the 'title' is shown as a plain text
 *       item in the links list.
 *     - html: (optional) Whether or not 'title' is HTML. If set, the title
 *       will not be passed through check_plain().
 *     - attributes: (optional) Attributes for the anchor, or for the <span> tag
 *       used in its place if no 'href' is supplied. If element 'class' is
 *       included, it must be an array of one or more class names.
 *     If the 'href' element is supplied, the entire link array is passed to l()
 *     as its $options parameter.
 *   - attributes: A keyed array of attributes for the UL containing the
 *     list of links.
 *   - heading: (optional) A heading to precede the links. May be an associative
 *     array or a string. If it's an array, it can have the following elements:
 *     - text: The heading text.
 *     - level: The heading level (e.g. 'h2', 'h3').
 *     - class: (optional) An array of the CSS classes for the heading.
 *     When using a string it will be used as the text of the heading and the
 *     level will default to 'h2'. Headings should be used on navigation menus
 *     and any list of links that consistently appears on multiple pages. To
 *     make the heading invisible use the 'element-invisible' CSS class. Do not
 *     use 'display:none', which removes it from screen-readers and assistive
 *     technology. Headings allow screen-reader and keyboard only users to
 *     navigate to or skip the links. See
 *     http://juicystudio.com/article/screen-readers-display-none.php and
 *     http://www.w3.org/TR/WCAG-TECHS/H42.html for more information.
 */
function bedrock_links($vars) {
  $links = $vars['links'];
  $attributes = $vars['attributes'];
  $heading = $vars['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';

    if (!empty($heading)) {
      if (is_string($heading)) {
        $heading = array(
          'text' => $heading,
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $output .= '<ul' . drupal_attributes($attributes) . '>';
    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      if (bedrock_get_setting('extra_menu_classes')) {
        $class = array($key);
      }
      if (bedrock_get_setting('extra_menu_classes')) {
        if ($i == 1) {
          $class[] = 'first';
        }
        if ($i == $num_links) {
          $class[] = 'last';
        }
      }
      if (bedrock_get_setting('extra_menu_classes')) {
        if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
            && (empty($link['language']) || $link['language']->language == $language_url->language)) {
          $class[] = 'active';
        }
      }
      if (bedrock_get_setting('extra_menu_classes')) {
        $output .= '<li' . drupal_attributes(array('class' => $class)) . '>';
      }
      else {
        $output .= '<li>';
      }
      if (isset($link['href'])) {
        if (bedrock_get_setting('menu_item_span_elements')) {
          $link['title'] = '<span>' . $link['title'] . '</span>';
          $link['html'] = TRUE;
        }
        $output .= l($link['title'], $link['href'], $link);
      }
      elseif (!empty($link['title'])) {
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      $output .= "</li>";
    }

    $output .= '</ul>';
  }

  return $output;
}

/**
 * Returns HTML for a field.
 *
 * bedrock overrides this in order to better support HTML5 by setting the
 * wrapper as section or div element depending on whether a title is used or not.
 * Fields have no title, instead it treats the field lable as if it were a title
 * and wraps it in h2 elements.
 *
 * This is the default theme implementation to display the value of a field.
 * Theme developers who are comfortable with overriding theme functions may do
 * so in order to customize this markup. This function can be overridden with
 * varying levels of specificity. For example, for a field named 'body'
 * displayed on the 'article' content type, any of the following functions will
 * override this default implementation. The first of these functions that
 * exists is used:
 * - THEMENAME_field__body__article()
 * - THEMENAME_field__article()
 * - THEMENAME_field__body()
 * - THEMENAME_field()
 *
 * Theme developers who prefer to customize templates instead of overriding
 * functions may copy the "field.tpl.php" from the "modules/field/theme" folder
 * of the Drupal installation to somewhere within the theme's folder and
 * customize it, just like customizing other Drupal templates such as
 * page.tpl.php or node.tpl.php. However, it takes longer for the server to
 * process templates than to call a function, so for websites with many fields
 * displayed on a page, this can result in a noticeable slowdown of the website.
 * For these websites, developers are discouraged from placing a field.tpl.php
 * file into the theme's folder, but may customize templates for specific
 * fields. For example, for a field named 'body' displayed on the 'article'
 * content type, any of the following templates will override this default
 * implementation. The first of these templates that exists is used:
 * - field--body--article.tpl.php
 * - field--article.tpl.php
 * - field--body.tpl.php
 * - field.tpl.php
 * So, if the body field on the article content type needs customization, a
 * field--body--article.tpl.php file can be added within the theme's folder.
 * Because it's a template, it will result in slightly more time needed to
 * display that field, but it will not impact other fields, and therefore,
 * is unlikely to cause a noticeable change in website performance. A very rough
 * guideline is that if a page is being displayed with more than 100 fields and
 * they are all themed with a template instead of a function, it can add up to
 * 5% to the time it takes to display that page. This is a guideline only and
 * the exact performance impact depends on the server configuration and the
 * details of the website.
 *
 * @param $vars
 *   An associative array containing:
 *   - label_hidden: A boolean indicating to show or hide the field label.
 *   - title_attributes: A string containing the attributes for the title.
 *   - label: The label for the field.
 *   - content_attributes: A string containing the attributes for the content's
 *     div.
 *   - items: An array of field items.
 *   - item_attributes: An array of attributes for each item.
 *   - classes: A string containing the classes for the wrapping div.
 *   - attributes: A string containing the attributes for the wrapping div.
 *
 * @see template_preprocess_field()
 * @see template_process_field()
 * @see field.tpl.php
 */
function bedrock_field($vars) {
  $output = '';

  // Render the label, if it's not hidden.
  if (!$vars['label_hidden']) {
    $output .= '<h2 class="field-label"' . $vars['title_attributes'] . '>' . $vars['label'] . ':&nbsp;</h2>';
  }

  // Render the items.
  $output .= '<div class="field-items"' . $vars['content_attributes'] . '>';
  foreach ($vars['items'] as $delta => $item) {
    $classes = 'field-item ' . ($delta % 2 ? 'odd' : 'even');
    $output .= '<div class="' . $classes . '"' . $vars['item_attributes'][$delta] . '>' . drupal_render($item) . '</div>';
  }
  $output .= '</div>';

  // Render the top-level wrapper element.
  $tag = $vars['tag'];
  $output = "<$tag class=\"" . $vars['classes'] . '"' . $vars['attributes'] . '>' . $output . "</$tag>";

  return $output;
}

/**
 * Returns HTML for a taxonomy field.
 *
 * Output taxonomy term fields as unordered lists.
 */
function bedrock_field__taxonomy_term_reference($vars) {
  $output = '';

  // Render the label, if it's not hidden.
  if (!$vars['label_hidden']) {
    $output .= '<h2 class="field-label"' . $vars['title_attributes'] . '>' . $vars['label'] . ':&nbsp;</h2>';
  }

  // Render the items.
  $output .= '<ul class="field-items"' . $vars['content_attributes'] . '>';
  foreach ($vars['items'] as $delta => $item) {
    $classes = 'field-item ' . ($delta % 2 ? 'odd' : 'even');
    $output .= '<li class="' . $classes . '"' . $vars['item_attributes'][$delta] . '>' . drupal_render($item) . '</li>';
  }

  $output .= '</ul>';

  // Render the top-level wrapper element.
  $tag = $vars['tag'];
  $output = "<$tag class=\"" . $vars['classes'] . '"' . $vars['attributes'] . '>' . $output . "</$tag>";

  return $output;
}

/**
 * Returns HTML for an image field.
 *
 * Output image fields as figure with figcaption for captioning.
 */
function bedrock_field__image($vars) {
  global $theme_key;
  $theme_name = $theme_key;

  // Check if image settings are enabled
  $image_settings_enabled = bedrock_get_setting('enable_image_settings', $theme_name);

  // Check if captions are enabled for full and/or teaser view modes
  if ($image_settings_enabled) {
    $caption_full = bedrock_get_setting('image_caption_full', $theme_name);
    $caption_teaser = bedrock_get_setting('image_caption_teaser', $theme_name);
  }

  $output = '';

  // Render the label, if it's not hidden.
  if (!$vars['label_hidden']) {
    $output .= '<h2 class="field-label"' . $vars['title_attributes'] . '>' . $vars['label'] . ':&nbsp;</h2>';
  }

  // Render the items.
  $output .= '<div class="field-items"' . $vars['content_attributes'] . '>';

  foreach ($vars['items'] as $delta => $item) {

    $classes = 'field-item ' . ($delta % 2 ? 'odd' : 'even');
    $output .= '<figure class="clearfix ' . $classes . '"' . $vars['item_attributes'][$delta] .'>';
    $output .= drupal_render($item);

    // Captions
    if (isset($item['#item']['title']) && !empty($item['#item']['title']) && $image_settings_enabled) {

      // Ouch this is ugly, please tell me how to get the actual width of the image.
      // image_style_load($item['#image_style']); will return the image style dimensions,
      // but not the actual image width, which can be different, say when images
      // scale, but I cannot decipher where these dimensions come from when
      // the item is rendered.
      preg_match('/< *img[^>]*width *= *["\']?([^"\']*)/i', $item['#children'], $matches);
      $width = isset($matches[1]) ? $matches[1] . 'px' : 'auto';
      $styles = 'style="width:' . $width . ';"';

      if ($vars['field_view_mode'] == 'full') {
        if ($caption_full) {
          $output .= '<figcaption class="caption full-caption"' . $styles .'>' . $item['#item']['title'] . '</figcaption>';
        }
      }
      if ($vars['field_view_mode'] == 'teaser') {
        if ($caption_teaser) {
          $output .= '<figcaption class="caption teaser-caption"' . $styles .'>' . $item['#item']['title'] . '</figcaption>';
        }
      }
    }

    $output .= '</figure>';
  }

  $output .= '</div>';

  // Render the top-level wrapper element.
  $tag = $vars['tag'];
  $output = "<$tag class=\"" . $vars['classes'] . '"' . $vars['attributes'] . '>' . $output . "</$tag>";

  return $output;
}

/**
 * Load polyfill scripts.
 *
 * Conditional scripts are returned to the preprocess function - in Bedrock that
 * means bedrock_preprocess_html() and bedrock_preprocess_maintenance_page().
 * There are two sources for conditional scripts - the subtheme info file and
 * Bedrock itself specifies load html5.js and respond.js.
 *
 * Unconditional scripts (those not printed within an IE conditional comment)
 * load directly via drupal_add_js in this function, while the conditional
 * scripts are returned as an array to preprocess, then rendered in process.
 * This is done to allow themers to manipulate the data structure in preprocess
 * if they have the need.
 *
 * @param string $theme_name
 *   Name of the Bedrock sub-theme.
 *
 * @return
 *   Render array of conditional-comment scripts to be rendered in <head>.
 */
function bedrock_load_polyfills($theme_name) {
  $bedrock_path = drupal_get_path('theme', 'bedrock');

  // Get the info file data
  $info = bedrock_get_info($theme_name);

  // Build an array of polyfilling scripts
  $polyfills = drupal_static('bedrock_preprocess_html_polyfills_array');
  if (empty($polyfills)) {

    $theme_path = drupal_get_path('theme', $theme_name);
    $polyfills_array = array();

    // Info file loaded conditional scripts
    if (array_key_exists('ie_scripts', $info)) {
      foreach ($info['ie_scripts'] as $condition => $ie_scripts_path) {
        foreach ($ie_scripts_path as $key => $value) {
          $filepath = $theme_path . '/' . $value;
          $polyfills_array['ie'][$condition][] = bedrock_theme_script($filepath);
        }
      }
    }

    // Conditional scripts.
    if (bedrock_get_setting('load_html5js')) {
      $polyfills_array['ie']['lt IE 9'][] = bedrock_theme_script($bedrock . '/scripts/vendor/html5.js');
    }
    if (bedrock_get_setting('load_respondjs')) {
      $polyfills_array['ie']['lt IE 9'][] = bedrock_theme_script($bedrock . '/scripts/vendor/respond.js');
    }

    // Unconditional scripts.
    if (bedrock_get_setting('load_scalefixjs')) {
      $polyfills_array['all'][] = 'scripts/vendor/scalefix.js';
    }
    if (bedrock_get_setting('load_onmediaqueryjs')) {
      $polyfills_array['all'][] = 'scripts/vendor/onmediaquery.js';
    }
    if (bedrock_get_setting('load_matchmediajs')) {
      $polyfills_array['all'][] = 'scripts/vendor/matchMedia.js';
      $polyfills_array['all'][] = 'scripts/vendor/matchMedia.addListener.js';
    }

    // Load polyfills.
    if (!empty($polyfills_array)) {
      // "all" - no conditional comment needed, use drupal_add_js()
      if (isset($polyfills_array['all'])) {
        foreach ($polyfills_array['all'] as $script) {
          drupal_add_js($bedrock_path . '/' . $script, array(
            'type' => 'file',
            'scope' => 'header',
            'group' => JS_THEME,
            'preprocess' => TRUE,
            'cache' => TRUE,
            )
          );
        }
      }

      // Build render array for IE conditional scripts.
      if (isset($polyfills_array['ie'])) {
        $polyfills = array();
        foreach ($polyfills_array['ie'] as $conditional_comment => $scripts) {
          $ie_script = array(
            '#type' => 'markup',
            '#markup' => implode("\n", $scripts),
            '#prefix' => "<!--[if " . $conditional_comment . "]>\n",
            '#suffix' => "\n<![endif]-->\n",
          );
          $polyfills[$conditional_comment] = $ie_script;
        }
      }
    }
    else {
      $polyfills = '';
    }
  }

  return $polyfills;
}

/**
 * Returns a script tag.
 *
 * Wraps a file in script element and returns a string.
 *
 * @param string $filepath
 *   Path to the file.
 */
function bedrock_theme_script($filepath) {
  $script = '';

  if (file_exists($filepath)) {
    // Use the default query string for cache control fingerprinting.
    $query_string = variable_get('css_js_query_string', '0');

    $file = file_create_url($filepath);
    $script = '<script src="' . $file . '?' . $query_string . '"></script>';
  }
  return $script;
}
