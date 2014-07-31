# Behat Installation and Use

## Part 1: Initial setup

### Composer

The easiest way to install behat is with composer, so make sure it's installed.

`brew install composer`

### Behat

1. copy composer.json to the place where you want to run it.
This could be a project-specific directory or it could be common scripts location.
Either /projects/PROJECTNAME/behat or ~/behat will work.

2. Run `composer install` in the directory that contains composer.json.

3. Confirm that it is installed by running `bin/behat`.

###  Selenium

1. Go to http://selenium-release.storage.googleapis.com/index.html and select the folder of the latest release.

2. Download selenium-server-standalone-LATEST-VERSION.jar.

3. Place it wherever you like. Probably makes sense be system level rather than project level.

4. Confirm that it works by running in a new terminal session: `java -jar PATH-TO-SELENIUM.jar`
There should be a message like "RemoteWebDriver instances should connect to: http://127.0.0.1:4444/wd/hub".
Confirm that that page loads. Quit by using CTRL-C in terminal.

## Part 2: Set up some tests and run them

1. Copy the features directory to your behat installation.
2. Copy behat.yml to your behat installation, and edit the base_url setting.
3. Start selenium. `java -jar PATH-TO-SELENIUM.jar`
4. Run any of the following:
    - `bin/behat features/contact_form.feature` (tests the one feature)
    - `bin/behat features` (runs all tests in the features directory)
    - `bin/behat` (runs all tests that behat can find.)
