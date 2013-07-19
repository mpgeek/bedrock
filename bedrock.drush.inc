<?php
/**
 * @file
 * Bedrock's Drush integration.
 *
 * Usage: drush bedrock "Foobar Theme" foobartheme
 */

/**
 * Implements hook_drush_command().
 */
function bedrock_drush_command() {
  $items = array();

  $items['bedrock'] = array(
    'description' => 'Create a theme using bedrock.',
    'arguments' => array(
      'name'         => 'A name for your theme.',
      'machine_name' => '[optional] A machine-readable name for your theme.',
    ),
    'options' => array(
      'name'         => 'A name for your theme.',
      'machine-name' => '[a-z, 0-9] A machine-readable name for your theme.',
      'description'  => 'A description of your theme.',
      'without-rtl'  => 'Remove all RTL stylesheets.',
      // @TODO potentially add settings defaults?
    ),
    'examples' => array(
      'drush bedrock "My theme name"' => 'Create a sub-theme, using the default options.',
      'drush bedrock "My theme name" my_theme' => 'Create a sub-theme with a specific machine name.',
    ),
  );

  return $items;
}

/**
 * Create a bedrock sub-theme using the starter kit.
 */
function drush_bedrock($name = NULL, $machine_name = NULL) {
  // Determine the theme name.
  if (!isset($name)) {
    if (!$name = drush_get_option('name')) {
      drush_print(dt("Please specify a name.\ne.g., drush bedrock 'My Cool Theme' cool_theme"));
      return;
    }
  }

  // Determine the machine name.
  if (!isset($machine_name)) {
    $machine_name = drush_get_option('machine-name');
  }
  if (!$machine_name) {
    $machine_name = $name;
  }
  $machine_name = str_replace(array(' ', '-'), '_', strtolower($machine_name));
  $search = array(
    '/[^a-z0-9_]/', // Remove characters not valid in function names.
    '/^[^a-z]+/',   // Functions must begin with an alpha character.
  );
  $machine_name = preg_replace($search, '', $machine_name);

  // Determine the path to the new subtheme by finding the path to bedrock.
  $bedrock_path = drupal_get_path('theme', 'bedrock');
  $subtheme_path = dirname($bedrock_path) . '/' . $machine_name;

  // Make a fresh copy of the original subtheme.
  drush_op('bedrock_copy', $bedrock_path . '/STARTERKIT', $subtheme_path);

  // Rename the .info file.
  $subtheme_info_file = $subtheme_path . '/' . $machine_name . '.info';
  drush_op('rename', $subtheme_path . '/STARTERKIT.info.txt', $subtheme_info_file);

  // Alter the contents of the .info file based on the command options.
  $alterations = array(
    '= Bedrock STARTERKIT' => '= ' . $name,
  );
  if ($description = drush_get_option('description')) {
    $alterations['Starter subtheme for Bedrock. Copy this subtheme to get started building your own Drupal theme.'] = $description;
  }
  drush_op('bedrock_file_str_replace', $subtheme_info_file, array_keys($alterations), $alterations);

  // Replace all occurrences of 'bedrock_subtheme' with the machine name of our sub theme.
  drush_op('bedrock_file_str_replace', $subtheme_path . '/template.php', 'Bedrock subtheme.', $name);
  drush_op('bedrock_file_str_replace', $subtheme_path . '/template.php', 'STARTERKIT', $machine_name);

  // Notify user of the newly created theme.
  drush_print(dt('New subtheme "!name" created in: !path', array(
    '!name' => $name,
    '!path' => $subtheme_path,
  )));

  //system_rebuild_theme_data(); // potentially execute this here, see http://drupal.org/node/1235942
}

/**
 * Copy a directory recursively.
 */
function bedrock_copy($source_dir, $target_dir, $ignore = '/^(\.(\.)?|CVS|\.svn|\.git|\.DS_Store)$/') {
  if (!is_dir($source_dir)) {
    drush_die(dt('The directory "!directory" was not found.', array('!directory' => $source_dir)));
  }
  $dir = opendir($source_dir);
  @mkdir($target_dir);
  while($file = readdir($dir)) {
    if (!preg_match($ignore, $file)) {
      if (is_dir($source_dir . '/' . $file)) {
        bedrock_copy($source_dir . '/' . $file, $target_dir . '/' . $file, $ignore);
      }
      else {
        copy($source_dir . '/' . $file, $target_dir . '/' . $file);
      }
    }
  }
  closedir($dir);
}

/**
 * Replace strings in a file.
 */
function bedrock_file_str_replace($file_path, $find, $replace) {
  $file_contents = file_get_contents($file_path);
  $file_contents = str_replace($find, $replace, $file_contents);
  file_put_contents($file_path, $file_contents);
}
