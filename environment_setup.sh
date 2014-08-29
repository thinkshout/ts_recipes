#!/bin/bash
set -e

#
# Sets up the standard ThinkShout development environment.
# Work-in-progress.
# TODO: Update existing dev environment.
#

confirmvhosts () {
  read -r -p "Download example httpd-vhosts.conf file? [y/n]" response
  case $response in
    [yY])
      true
      ;;
    *)
      false
      ;;
  esac
}

confirmhosts () {
  read -r -p "Create example item in hosts file? [y/n]" response
  case $response in
    [yY])
      true
      ;;
    *)
      false
      ;;
  esac
}

# Homebrew

echo $'\n'
echo "Installing Homebrew"
echo $'\n'

ruby -e "$(curl -fsSL https://raw.github.com/Homebrew/homebrew/go/install)"

brew_result=`brew doctor`

if [ "$brew_result" != "Your system is ready to brew." ]; then
  echo "Homebrew was not successfully installed. See message:"
  echo "$brew_result"
  exit 1;
fi

brew tap homebrew/dupes
brew tap homebrew/homebrew-php

path_export='export PATH="#{HOMEBREW_PREFIX}/bin:$PATH"'

echo "Which shell?"
echo "1) Bash"
echo "2) Zsh"
echo "3) Other"
read -r -p "" shell
case $shell in
  [1])
    echo "Updated ~/.bashrc"
    echo "$path_export" >> ~/.bashrc
    ;;
  [2])
    echo "Updated ~/.zshrc"
    echo "$path_export" >> ~/.zshrc
	;;
  *)
  echo "Add the following to your shell profile:"
  echo path_export
esac

echo $'\n'
echo "Installing dev tools via Homebrew"
echo $'\n'

# LAMP

# TODO: Make PHP version optional (PHP 5.3 / PHP 5.4 at least)
brew install php55
brew install mysql

# Drush

brew install drush

# Composer

brew install composer

# TODO: Set up mysql to autostart
# TODO: Configure mysql for concurrent connections

# Xdebug
# TODO: Automate configuration in Apache config (might be done by brew.)

brew install php55-xdebug

# Drupal Code Sniffer

brew install drupal-code-sniffer

# Apache vhosts

if confirmvhosts; then
  wget https://github.com/thinkshout/ts_recipes/blob/master/brew-lamp-dev-envt/httpd-vhosts.conf
  mv httpd-vhosts.conf ~/Sites/
  sudo mv /private/etc/apache2/extra/httpd-vhosts.conf /private/etc/apache2/extra/httpd-vhosts.conf.bak
  sudo ln -s ~/Sites/httpd-vhosts.conf /private/etc/apache2/extra/httpd-vhosts.conf
else
  echo $'\n'
  echo "Make sure to symlink your httpd-vhosts.conf file to /private/etc/apache2/extra/httpd-vhosts.conf"
  echo $'\n'
fi

# Hosts

if confirmhosts; then
  sudo echo '127.0.0.1 site.local' >> /private/etc/hosts
fi

# Restart Apache

echo $'\n'
echo "Restarting Apache"
echo $'\n'

sudo apachectl restart

echo $'\n'
echo "Dev environment setup complete"
echo $'\n'

exit
