Using Bedrock to create a new WordPress project.

## Installation

1. Create a new project in a new folder for your project:

  `composer create-project roots/bedrock your-project-folder-name`

2. Copy `.env.example` to `.env` and update environment variables:
  * `DB_NAME` - Database name
  * `DB_USER` - Database user
  * `DB_PASSWORD` - Database password
  * `DB_HOST` - Database host
  * `WP_HOME` - Full URL to WordPress home (https://example.cdev)

  Automatically generate the security keys:

      wp package install aaemnnosttv/wp-cli-dotenv-command
      wp dotenv salts regenerate

3. Add theme(s) in `web/app/themes` as you would for a normal WordPress site.

4. Set your site vhost document root to `~/Sites/project-name/web/`

5. Access WP admin at `http://example.dev/wp/wp-admin`
