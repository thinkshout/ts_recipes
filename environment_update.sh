#!/bin/bash
set -e

#
# Updates the ThinkShout development environment.
#

confirmupdate () {
  read -r -p "$1 [y/n]" response
  case $response in
    [yY])
      true
      ;;
    *)
      false
      ;;
  esac
}

if confirmupdate "Update Pantheon aliases?"; then
  drush pauth;
  drush paliases;
else
  echo "Skipped Pantheon aliases."
fi

if confirmupdate "Check Homebrew status? (recommended)"; then
  brew doctor;
else
  echo "Skipped Homebrew status."
fi

if confirmupdate "Update Homebrew?"; then
  brew update;
else
  echo "Skipped Homebrew update."
fi

exit
