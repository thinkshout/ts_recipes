<?php

$users = [
  'foo@bar.com' => 'Foo Bar',
  'baz@quux.com' => 'Baz Quux',
  'quuz@corge.org' => 'Quuz Corge',
  'grault@garply.net' => 'Grault Garply',
];

// Can be empty
$roles = [
  'admin',
];

// Can be empty
$env = '@pantheon.somesite.dev';

foreach ($users as $mail => $name) {
  // Create the user.
  exec(sprintf('drush %s ucrt "%s" --mail="%s"', $env, $name, $mail));
  // Get login link.
  echo "Login link for $name ($mail): ";
  system(sprintf('drush %s uli %s', $env, $mail));
}

foreach ($roles as $role) {
  $user_list = implode(',', array_keys($users));
  exec(sprintf('drush %s urol "%s" %s', $env, $role, $user_list));
}
