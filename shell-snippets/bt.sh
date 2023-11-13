# Paste this into ~/.oh-my-zsh/custom/custom_functions.zsh. Assuming you're 
# in ~/Sites/[somesite] and the theme is built with "npm run build", this 
# builds the theme without making you cd a bunch of times.
function bt() {
  THISDIR="$(pwd)"
  THEMEDIR="web/themes/custom"
  if [[ $(ls -1 ${THEMEDIR} | wc -l | sed "s/ //g") == "1" ]]; then
    THEME="${THEMEDIR}/$(ls ${THEMEDIR})"
    if [[ -d $THEME ]]; then
      echo "cd'ing into ${THEME}..."
      cd $THEME
      npm run build
      cd $THISDIR
      echo "back to ${THISDIR}!"
    fi
  fi
}
