<?php
function generateUniqueFilename(string $extension)
{
  $new_filename = uniqid() . $extension;

  // Check if the filename already exists, if so, generate a new one until it's unique
  while (file_exists(dirname(__FILE__) . "/../database/files/$new_filename")) {
    $new_filename = uniqid() . $extension;
  }

  return $new_filename;
}
?>