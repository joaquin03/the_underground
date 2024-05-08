<?php

function loadEnvironmentVariables($envFilePath) {
  if (!file_exists($envFilePath)) {
    return; // or handle the error as you prefer
  }

  $variables = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($variables as $variable) {
    if (strpos(trim($variable), '#') === 0) {
      continue; // Skip comments
    }
    list($name, $value) = explode('=', $variable, 2);
    $name = trim($name);
    $value = trim($value);
    if (!empty($name)) {
      putenv(sprintf("%s=%s", $name, $value));
    }
  }
}
