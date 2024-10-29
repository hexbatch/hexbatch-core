#uploads for uploading images and docs
if [[ ! -e /var/www/html/storage/app/uploads ]]; then
    mkdir -p /var/www/html/storage/app/uploads
fi

chmod 755 /var/www/html/storage/app/uploads

if [[ ! -e /var/www/html/public/uploads ]]; then
    ln -s /var/www/html/storage/app/uploads /var/www/html/public
fi

# when docker-compose starts, some directors may not be readable to the php or web, Run this script in the container terminal
chown -R :www-data /var/www/html/storage/
chown -R :www-data /var/www/html/public/storage
chown -R :www-data /var/www/html/bootstrap/cache

chmod -R g+rw /var/www/html/storage/
chmod -R g+rw /var/www/html/public/storage
chmod -R g+rw /var/www/html/bootstrap/cache





