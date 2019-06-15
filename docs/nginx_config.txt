server {
        listen   80;
    
        root {piwik_root_path};
        index index.php;

        server_name {shortener_url};

	    rewrite "^\/([a-zA-Z0-9]{6})$" /index.php?module=API&method=ShortcodeTracker.performRedirectForShortcode&code=$1; 
					 
        location / {
	        index index.php;
        }

        error_page 404 /404.html;

        error_page 500 502 503 504 /50x.html;
        location = /50x.html {
              root /usr/share/nginx/www;
        }

        # pass the PHP scripts to FastCGI server listening on the php-fpm socket
        location ~ \.php$ {
                try_files $uri =404;
                fastcgi_pass unix:/var/run/php5-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
                
        }
}
