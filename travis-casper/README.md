## For local development
- `brew update`
- phantom
  - latest `brew install phantomjs`
  - Earlier versions are also available in homebrew
  - Home page http://phantomjs.org/
- casper `brew install casperjs --devel`
  - "Devel" is required. It loads Casper 1.1, which is the earliest version to include casper.test.
  - Installation http://docs.casperjs.org/en/latest/installation.html
  - Usage http://docs.casperjs.org/en/latest/quickstart.html
  - Full docs http://docs.casperjs.org/en/latest/modules/tester.html
  - Home page http://casperjs.org/

## On Travis
  - Example https://github.com/emberjs/ember.js/blob/master/.travis.yml
  - Docs http://docs.travis-ci.com/user/build-configuration/
  - Intro to travis + headless testing http://docs.travis-ci.com/user/gui-and-headless-browsers/
  - Intro to travis http://docs.travis-ci.com/user/getting-started/

## Next steps. (todo)
- Use Composer to set casper and phantom versions.
- Use behat + zombie
