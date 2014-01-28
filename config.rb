# Require any additional compass plugins here.
require "zurb-foundation"

# Set this to the root of your project when deployed:
http_path = "/"
css_dir = "web/css"
sass_dir = "web/sass"
images_dir = "web/images"
javascripts_dir = "web/js"

relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false
environment = :dev

if environment == :production
  output_style = :compressed
else
  output_style = :expanded
  sass_options = { :debug_info => false }
end
