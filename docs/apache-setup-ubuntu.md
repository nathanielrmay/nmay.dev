# Apache (Ubuntu/Debian) setup for NMay front controller

This document explains how to enable mod_rewrite and configure an Apache VirtualHost so the front controller and the supplied [`src/.htaccess:1`] work correctly.

Files we created
- [`src/.htaccess:1`] — front-controller rewrite rules
- [`src/index.php:1`] — PHP front controller
- [`src/nmay_lib/nmay.css:1`] — site CSS
- [`src/nmay_lib/header.php:1`], [`src/nmay_lib/menu.php:1`], [`src/nmay_lib/footer.php:1`] — partials

Quick steps (commands)
1. Install Apache + PHP:
sudo apt update && sudo apt install -y apache2 php libapache2-mod-php php-mbstring php-xml

2. Enable mod_rewrite and restart Apache:
sudo a2enmod rewrite
sudo systemctl restart apache2

3. Create a VirtualHost file (example: /etc/apache2/sites-available/nmay.conf)
- Set DocumentRoot to the path where you deploy this repository's `src` directory (example below uses /var/www/nmay/src)

Example VirtualHost (replace example.com and paths):
<VirtualHost *:80>
    ServerName example.com
    ServerAlias www.example.com

    DocumentRoot /var/www/nmay/src

    <Directory /var/www/nmay/src>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/nmay-error.log
    CustomLog ${APACHE_LOG_DIR}/nmay-access.log combined
</VirtualHost>

Save the file and enable the site:
sudo a2ensite nmay.conf
sudo apache2ctl configtest
sudo systemctl reload apache2

Important notes
- AllowOverride All is required for `.htaccess` files to work. For best performance and security, move the rules from [`src/.htaccess:1`] into the VirtualHost's config (inside the <Directory> or server config) and set AllowOverride None.
- If both `index.html` and `index.php` exist, Apache's DirectoryIndex order determines which is served. Ensure `DirectoryIndex index.php index.html` is set if you want `index.php` prioritized.
- File ownership/permissions:
sudo chown -R www-data:www-data /var/www/nmay/src
sudo find /var/www/nmay/src -type d -exec chmod 755 {} \;
sudo find /var/www/nmay/src -type f -exec chmod 644 {} \;

PHP-FPM (optional)
If you run PHP via php-fpm, update the VirtualHost to use the FPM socket. Example (PHP 8.2):
<FilesMatch \.php$>
    SetHandler "proxy:unix:/run/php/php8.2-fpm.sock|fcgi://localhost/"
</FilesMatch>

Troubleshooting
- Check logs: /var/log/apache2/nmay-error.log and nmay-access.log.
- Verify mod_rewrite: create a small test .htaccess with "RewriteEngine On" and a simple rule, then access URLs.
- Use sudo apache2ctl -M to list enabled modules and confirm rewrite_module is present.

Final recommendations
- For production, prefer placing rewrite rules in the VirtualHost and disable .htaccess for performance (set AllowOverride None).
- Configure HTTPS using Certbot (snap install core; sudo snap install --classic certbot; sudo certbot --apache) and update ServerName accordingly.

Reference files in this repo
- [`src/index.php:1`] — front controller implementation
- [`src/.htaccess:1`] — the rules we added
- [`src/nmay_lib/nmay.css:1`] — CSS used by the partials

End of document