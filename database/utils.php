<?php
function generateUniqueFilename(string $extension)
{
  $newFilename = uniqid() . $extension;

  // Check if the filename already exists, if so, generate a new one until it's unique
  while (file_exists(dirname(__FILE__) . "/../database/files/$newFilename")) {
    $newFilename = uniqid() . $extension;
  }

  return $newFilename;
}
?>