masala
======

A WordPress plugin that puts the full content of uploaded text, PDF, DOC and other files into metadata upon upload.

This plugin (download) puts the full content of uploaded text, PDF, DOC into metadata upon upload.
what this means, is that WordPress 'search' will then also search the contents of uploaded documents.

this is done with the magic of Apache Tika.

This was written out of desperation - plugins called 'search everything' didn't, like, search everything. 

with masala, now they do.

I don't really want to be bothered with support - I have yet to clean this up and submit to official plugin directory - meanwhile I'm sharing because who knows when I'll get around to that, as it works as-is for my needs.


(home page here, same content below - http://avatari.net/public/wordpress/masala/)


How to install:
======
1) install plugin into wordpress
2) install tika's jar (http://tika.apache.org/) somewhere on your machine (this also assumes you have java on your machine)
3) change configuration settings - right now, just edit them in the plugin's PHP file
4) install 'search everything' (http://wordpress.org/plugins/search-everything/) - or any plugin that searches post metadata, configured it to do so

OK!

How to use:
======
1) upload content
	if it's a PDF or DOC, it will take an extra few seconds after upload to process the file & insert metadata.
2) search 
	the attachment's contents (now in metadata) will be searched per-search, and attachment will be listed in search results if there's a match.
3) delete content.
	when 'permanently deleted' through wordpress 'media' menu, this plugin will delete all metadata it created. 
	

notes:
======
 If it's not a PDF or doc, this plugin will not be activated. (yet, can support all formats tika supports (http://tika.apache.org/1.4/formats.html)- just edit the 'allowed' array in the plugin php file)

I also use 'publication manager' (http://wordpress.org/plugins/tags/publication-manager) - which is great for academic purposes - however when 'deleted' through there, it's still present in 'media' - so one must delete it in both places to actually be rid of it.

why masala? 
======

in the tradition of open source software, it's a bad pun.
Masala actually is a mixture of spices in Indian, Nepali, Bangladeshi and Pakastani cuisines. 
Masala digtiallyis this WordPress plugin - not as tasty, but still a mix of spicy code. as in, will cause indigestion.
also, this is my first WP plugin - it's very limited, only adds and deletes metadata upon upload for file formats that it knows.but it works !!! i'm using it on a few WP sites for work (named-data.net, ndn.ucla.edu)

The hope is that someone will either extend this, or bite this to use in existing plugins - and meanwhile that this distribution will help some users out.
