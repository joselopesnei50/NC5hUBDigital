ubuntu@ip-172-26-2-81:/var/www/nc5$ sudo cat /etc/nginx/sites-enabled/nc5
server {

    server_name 50.17.213.144 nc5hubdigital.com.br www.nc5hubdigital.com.br;

    root /var/www/nc5/public;



    add_header X-Frame-Options "SAMEORIGIN";

    add_header X-XSS-Protection "1; mode=block";

    add_header X-Content-Type-Options "nosniff";



    index index.php;



    charset utf-8;



    location / {

        try_files $uri $uri/ /index.php?$query_string;
 }



    location = /favicon.ico { access_log off; log_not_found off; }

    location = /robots.txt  { access_log off; log_not_found off; }



    error_page 404 /index.php;



    location ~ \.php$ {

        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;

        include fastcgi_params;

    }



    location ~ /\.(?!well-known).* {
deny all;

    }


    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/nc5hubdigital.com.br/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/nc5hubdigital.com.br/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot




}
server {
    if ($host = www.nc5hubdigital.com.br) {
        return 301 https://$host$request_uri;
    } # managed by Certbot


    if ($host = nc5hubdigital.com.br) {
        return 301 https://$host$request_uri;
    } # managed by Certbot


    listen 80;

    server_name 50.17.213.144 nc5hubdigital.com.br www.nc5hubdigital.com.br;
    return 404; # managed by Certbot




ubuntu@ip-172-26-2-81:/var/www/nc5$ 


ubuntu@ip-172-26-2-81:/var/www/nc5$ ls -la /etc/nginx/sites-enabled/
total 8
drwxr-xr-x 2 root root 4096 Jul 15 03:54 .
drwxr-xr-x 8 root root 4096 Jul 15 05:29 ..
lrwxrwxrwx 1 root root   30 Jul 15 03:54 nc5 -> /etc/nginx/sites-available/nc5
ubuntu@ip-172-26-2-81:/var/www/nc5$ 