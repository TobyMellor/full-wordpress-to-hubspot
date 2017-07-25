### What is this repository for? ###

The default Hubspot Importer from Wordpress doesn't support featured images. This application will allow you to import featured images by using the default WordPress Export .xml file, and the export file from the plugin https://wordpress.org/plugins/export-featured-images/

### How do I get set up? ###

Make sure https://www.docker.com/ is installed.

* 1. Run the commands:
     `git clone https://username@bitbucket.org/teamsinspace/documentation-tests.git`
	 `docker build -t importer .`
* 2. Go to your WordPress site and Login. Go to "Tools" > "Export". Click on "Posts" then "Download Export File"
* 3. Take the contents from this XML file and paste it into "import_file.xml". Find and replace "wp:", "dc:" and ":encoded" to ""
* 4. Go to WordPress & install https://wordpress.org/plugins/export-featured-images/. Once installed, Go to "Tools" > "Export Featured Images". Click on all options then "Export"
* 5. Take the contents from this XML file and paste it into "import_meta_file.xml". Find and replace "wp:", "dc:" and ":encoded" to ""
* 6. Copy private/details.php.example to private/details.php and fill in the details. Your HAPIKey can be found by clicking on your name in the Hubspot Navbar, then Integrations. The Blog post ID can be found by looking in the URL bar when editing a blog, or by doing a GET request to https://api.hubapi.com/content/api/v2/blogs?hapikey=<HAPIKEY HERE>
* 7. Ensure there is more than 1 author and 1 blog post. Also ensure that all authors, blog posts and topics are removed from your blog before running this script
* 8 Run the command:
     `docker run importer`