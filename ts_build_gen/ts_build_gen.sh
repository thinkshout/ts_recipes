#!/bin/sh
set -e

#
# Build a new custom Drupal build based on ts_build.
#
# Usage generate.sh <DESTINATION_PATH> <PROFILE_NAME>
#

# Figure out directory real path.
realpath () {
  TARGET_FILE=$1

  cd `dirname $TARGET_FILE`
  TARGET_FILE=`basename $TARGET_FILE`

  while [ -L "$TARGET_FILE" ]
  do
    TARGET_FILE=`readlink $TARGET_FILE`
    cd `dirname $TARGET_FILE`
    TARGET_FILE=`basename $TARGET_FILE`
  done

  PHYS_DIR=`pwd -P`
  RESULT=$PHYS_DIR/$TARGET_FILE
  echo $RESULT
}

DESTINATION=$1
PROFILE_NAME=$2

DESTINATION=$(realpath $DESTINATION)

mkdir $DESTINATION

cd $DESTINATION

git clone git@github.com:thinkshout/ts_build.git $PROFILE_NAME

cd $PROFILE_NAME

mv ts_build.info $PROFILE_NAME.info
mv ts_build.install $PROFILE_NAME.install
mv ts_build.profile $PROFILE_NAME.profile

sed -i '' "s/ts_build/$PROFILE_NAME/g" scripts/config.sh
sed -i '' "s/ts_build/$PROFILE_NAME/g" $PROFILE_NAME.info
sed -i '' "s/ts_build/$PROFILE_NAME/g" $PROFILE_NAME.install
sed -i '' "s/ts_build/$PROFILE_NAME/g" $PROFILE_NAME.profile

# TODO: Clear git history from ts_build, start new git repo.
