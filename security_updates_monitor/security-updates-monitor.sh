sites=`drush sa`
dev_sites=`echo "$sites" | grep '\.dev$'`

while read -r line; do

  echo "* Checking security updates for $line..."
  result=`drush $line pm-updatecode --security-only -n < /dev/null`

  if [[ $result == *"Command pm-updatecode needs the following modules installed/enabled"* ]]; then
    echo "- update module not enabled on this site"

  elif [[ $result == *"This codebase is assembled with Composer instead of Drush."* ]]; then
      echo "- site uses Composer instead of Drush"

  elif [[ $result == *"No security updates available."* ]]; then
      echo "- no security updates available"

  else
    security_update_8=`echo "$result" | grep '(SECURITY UPDATE available)'`

    if [[ $security_update_8 == *"(SECURITY UPDATE available)"* ]]; then
      security_update_8=`echo "$result" | grep -B4 '(SECURITY UPDATE available)'`
      echo "$security_update_8"

    else
      security_update=`echo "$result" | grep 'SECURITY UPDATE available'`

      if [[ $security_update == *"SECURITY UPDATE available"* ]]; then

        # loop through security updates
        while read -r item; do

          item_part=`echo $item | sed -e 's/SECURITY UPDATE available//' | xargs`
          # echo "item_part: $item_part"

          # get new version
          version_new=`echo "$item_part" | sed 's#.*\ ##g' | xargs`
          version_new_minor=`echo "$version_new" | sed 's#.*\.##g'`
          version_new_before_minor=`echo "$version_new" | sed 's/\(.*\)\..*/\1/'`
          # echo "version_new: $version_new"
          # echo "version_new_before_minor: $version_new_before_minor"
          # echo "version_new_minor: $version_new_minor"

          item_part=`echo $item_part | sed 's/\ [^ ]*$//' | xargs`
          # echo "item_part: $item_part"

          # get current version
          version_current=`echo "$item_part" | sed 's#.*\ ##g' | xargs`
          version_current_minor=`echo "$version_current" | sed 's#.*\.##g'`
          version_current_before_minor=`echo "$version_current" | sed 's/\(.*\)\..*/\1/'`
          # echo "version_current: $version_current"
          # echo "version_current_before_minor: $version_current_before_minor"
          # echo "version_current_minor: $version_current_minor"

          item_part=`echo $item_part | sed 's/\ [^ ]*$//'`
          # echo "item_part: $item_part"

          # get project machine name
          machine_name=`echo "$item_part" | awk -F"[()]" '{print $2}'`

          # couldn't find machine name, try to make it from the full name
          if [[ -z "$machine_name" ]]; then

            machine_name=`echo "$item_part"`
            #remove white space
            machine_name=`echo "${machine_name//[[:space:]]/}"`
            #make lowercase
            machine_name=`echo "$machine_name" | tr '[:upper:]' '[:lower:]'`
          fi
          # echo "-machine name: $machine_name"

          # find url for changelog rss
          releases_url_rss=`curl -s https://www.drupal.org/project/$machine_name/releases | grep '<div id="feeds">' | sed -n 's/.*href="\([^"]*\).*/\1/p'`
          # echo "releases_url_rss: $releases_url_rss"

          echo "- Security update available for: $machine_name (current: $version_current / new: $version_new)"

          # loop through minor versions (current + 1 to new)
          startloop=`echo $((version_current_minor+1))`
          for check_minor_version in $(eval echo "{$startloop..$version_new_minor}")
          do
          	# echo "check_minor_version: $check_minor_version"
            check_version=`echo "$version_current_before_minor.$check_minor_version"`
            # echo "check_version: $check_version"

            # find description for specific version
            item_description=`curl -s "$releases_url_rss" | xmllint --xpath "//channel/item[title/text() = '$machine_name $check_version']/description/text()" -`
            # echo "$item_description"

            # find link for specific version
            release_version_url=`curl -s "$releases_url_rss" | xmllint --xpath "//channel/item[title/text() = '$machine_name $check_version']/link/text()" -`
            # echo "release_version_url: $release_version_url"

            # decode html entities in description
            item_description=`echo "$item_description" | perl -CS -MHTML::Entities -pe 'decode_entities($_);'`
            # echo "$item_description"

            # find url of security advisory
            regex="<a href=\"(.*)\" .*>SA-.*<\/a>"
            if [[ $item_description =~ $regex ]]; then
              sa_url="${BASH_REMATCH[1]}"
              # echo "sa_url: $sa_url"

              # find security risk level on security advisory page
              sa_page=`curl -s "$sa_url" | grep risk-levels`
              # echo "$sa_page"
              regex="<a href=\"https:\/\/www\.drupal\.org\/security-team\/risk-levels\">.*\">(.*)<\/span>"
              if [[ $sa_page =~ $regex ]]; then
                sa_level="${BASH_REMATCH[1]}"
                sa_level=`echo "$sa_level" | xargs`
                # echo "sa_level: $sa_level"
                echo "  ! Security advisory level: $sa_level ($sa_url)"
              fi
            fi
          done

        done <<< "$security_update"
        # echo "-----original notice-----"
        # echo "$security_update"
        # echo "-----original notice-----"
      else
        # echo $result
        echo "- unknown error"
      fi
    fi
  fi
done <<< "$dev_sites"
