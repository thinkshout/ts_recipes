## Admin Perspective WYSIWYG Styling

Add styles to the wysiwyg editor by:
* Add this bit of code to your `/web/themes/custom/ts_themename/ts_themename.info.yml` file:

   ```
   ckeditor_stylesheets:
      - css/ckeditor.css
   ```

* Add a `ckeditor.css` file into `web/themes/custom/ts_themename/css` directory
* Find the styles in your `style.css` file (same directory) and add them to the `ckeditor.css` file.
* Clear caches
