* Download a theme using the all advanced fields (including Check Sassify!) from http://underscores.me/
* Install the theme into `/web/wp-content/themes/`
* Delete other themes
* Add the following to `wp-config`: `define( 'WP_DEFAULT_THEME', 'THEMENAME' );` (replace THEMENAME)
* Add Bourbon and Neat to the Sass folders, update the `/sass/style/scss` to include the . 
* Add a Rakefile:

```
desc 'Install dependencies'
task :install do
  system 'bundle install'
  system 'npm install -g browser-sync'
  system 'npm install -g watchify'
  system 'npm install -g uglify-js'
end

# Change basetheme.dev to your site path
desc 'Running Browsersync'
task :browsersync do
  system 'browser-sync start --proxy "basetheme.dev" --files "*.css" --no-inject-changes'
end

desc 'Watch sass'
task :sasswatch do
  system 'sass -r sass-globbing --watch sass/style.scss:style.css'
end

desc 'Serve'
task :serve do
  threads = []
  %w{sasswatch browsersync}.each do |task|
    threads << Thread.new(task) do |devtask|
      Rake::Task[devtask].invoke
    end
  end
  threads.each {|thread| thread.join}
  puts threads
end
```

Add a Gemfile:
```
source 'http://rubygems.org'

gem 'sass'
gem 'sass-globbing'
```

Go to the theme directory:  
`cd ~/Sites/SITENAME/web/wp-content/themes/THEMENAME`

Run `rake install` to install dependencies.

After that, all you ned to do is run `rake serve` from the theme directory like so:
`cd ~/Sites/SITENAME/web/wp-content/themes/THEMENAME;rake serve`
