<?php

//use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Commands\Site\SiteCommand;

class SecurityUpdatesCommand extends SiteCommand {

  /**
   * Print the classic message to the log.
   *
   * @command hello
   */
  public function sayHello() {

    $ssl_options=array(
      "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
      ),
    );

    $all_sites_array = [];

    $site_array = [];

    $site['name'] = 'givingforum';
    $site_array['name'] = $site['name'];

    $command = "terminus remote:drush {$site['name']}.dev -- pm-updatecode --security-only -n";
    $response = $this->pipe_exec($command);
//    print_r($response[1]);

    if (strpos($response[1], "Command pm-updatecode needs the following modules installed/enabled") !== false) {
      $this->log()->notice("- update module not enabled on this site");
      $site_array['message'] = "update module not enabled on this site";
    }
    if (strpos($response[1], "This codebase is assembled with Composer instead of Drush.") !== false) {
      $this->log()->notice("- site uses Composer instead of Drush");
      $site_array['message'] = "site uses Composer instead of Drush";
    }
    if (strpos($response[1], "No security updates available.") !== false) {
      $this->log()->notice("- no security updates available");
      $site_array['message'] = "no security updates available";
    }
    if (strpos($response[1], "SECURITY UPDATE available") !== false) {
      $this->log()->notice("security update found");

      // Find column dimensions
      preg_match('/.*(\bName +Installed +Proposed +Message\b).*/', $response[1], $match);
      if (isset($match[0])) {

        $name_start = strpos($match[0],'Name');
        $installed_version_start = strpos($match[0],'Installed');
        $proposed_version_start = strpos($match[0],'Proposed');
        $message_start = strpos($match[0],'Message');

        $name_length = $installed_version_start - $name_start;
        $installed_version_length = $proposed_version_start - $installed_version_start;
        $proposed_version_length = $message_start - $proposed_version_start;

        // Parse security updates
        preg_match_all('/.*(\bSECURITY UPDATE available\b).*/', $response[1], $matches);
        if (isset($matches[0])) {

          foreach ($matches[0] as $match) {

            $name = trim(substr($match, $name_start, $name_length));

            // Get or create machine name
            preg_match('/.*\((.*)\)/', $name, $machine_name_match);
            if (isset($machine_name_match[1])) {
              $machine_name = $machine_name_match[1];
            }
            else {
              $machine_name = strtolower(str_replace(' ', '', $name));
            }
            $site_update_array = [];
            $site_update_array['machine_name'] = $machine_name;

            $installed_version = trim(substr($match, $installed_version_start, $installed_version_length));
            preg_match('/([^-]+)-(\d)+\.*(\d)/', $installed_version, $version_match);
            $installed_version_core = $version_match[1];
            $installed_version_major = $version_match[2];
            $installed_version_minor = $version_match[3];
            $site_update_array['installed_version'] = $installed_version;

            $proposed_version = trim(substr($match, $proposed_version_start, $proposed_version_length));
            preg_match('/([^-]+)-(\d)+\.*(\d)/', $proposed_version, $version_match);
            $proposed_version_core = $version_match[1];
            $proposed_version_major = $version_match[2];
            $proposed_version_minor = $version_match[3];
            $site_update_array['proposed_version'] = $proposed_version;

            print ("\n" . "----------" . "\n");
            print ("name: " . $name . "\n");
            print ("machine name: " . $machine_name . "\n");
            print ("installed: " . $installed_version . "\n");
            print ("installed_core: " . $installed_version_core . "\n");
            print ("installed_major: " . $installed_version_major . "\n");
            print ("installed_minor: " . $installed_version_minor . "\n");
            print ("proposed: " . $proposed_version . "\n");
            print ("proposed_core: " . $proposed_version_core . "\n");
            print ("proposed_major: " . $proposed_version_major . "\n");
            print ("proposed_minor: " . $proposed_version_minor . "\n");

            // find url for changelog rss
            $releases_url_rss = file_get_contents("https://www.drupal.org/project/{$machine_name}/releases", false, stream_context_create($ssl_options));
            preg_match('/<div id="feeds">.*<a href="([^ ]*)"/', $releases_url_rss, $releases_url_match);
            if (isset($releases_url_match[1])) {

              $releases_content = file_get_contents($releases_url_match[1], false, stream_context_create($ssl_options));

              // loop through minor versions (current + 1 to new)
              for ($check_minor_version = $installed_version_minor+1; $check_minor_version <= $proposed_version_minor; $check_minor_version++) {
                $xml = new SimpleXMLElement($releases_content);
                $item_description = $xml->xpath("//channel/item[title/text() = '{$machine_name} {$installed_version_core}-{$installed_version_major}.{$check_minor_version}']/description/text()");
                if (isset($item_description[0][0])) {
                  $item_description = html_entity_decode($item_description[0][0]);

                  // find url of security advisory
                  preg_match('/<a href=\"(.*)\" .*>SA-.*<\/a>/', $item_description, $security_advisory_url_match);
                  if (isset($security_advisory_url_match[1])) {
                    print ($security_advisory_url_match[1] . "\n");
                    $site_update_array['security_advisory_url'] = $security_advisory_url_match[1];

                    // find security risk level on security advisory page
                    $sa_url_contents = file_get_contents($security_advisory_url_match[1], false, stream_context_create($ssl_options));
                    preg_match('/<a href=\"https:\/\/www\.drupal\.org\/security-team\/risk-levels\">.*\">(.*)<\/span>/', $sa_url_contents, $security_advisory_level_match);

                    if (isset($security_advisory_level_match[1])) {
                      print ("security_advisory_level_match: " . $security_advisory_level_match[1] . "\n");
                      $site_update_array['security_advisory_level'] = $security_advisory_level_match[1];
                    }

                  }
                }

                // find url of release
                $release_version_url = $xml->xpath("//channel/item[title/text() = '{$machine_name} {$installed_version_core}-{$installed_version_major}.{$check_minor_version}']/link/text()");
                if (isset($release_version_url[0])) {
                  print ((string)$release_version_url[0] . "\n");
                  $site_update_array['release_url'] = (string)$release_version_url[0];
                }



              }
            }
            $site_array['updates'][] = $site_update_array;
          }
        }
      }
    }

    $all_sites_array[] = $site_array;

    print_r ($all_sites_array);

    return;
    $this->sites()->fetch();
    $sites = $this->sites->serialize();
    if (empty($sites)) {
      $this->log()->notice('You have no sites.');
    }
    else {
      foreach ($sites as $site) {
        if ($site['name'] == 'facing-history') {


        }
      }
    }


  }

  function pipe_exec($cmd, $input='') {
    $proc = proc_open($cmd, array(
      array('pipe', 'r'),
      array('pipe', 'w'),
      array('pipe', 'w')), $pipes);
    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $return_code = (int)proc_close($proc);

    return array($return_code, $stdout, $stderr);
  }

}

