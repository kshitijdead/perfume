# Use official PHP image with Apache
FROM php:8.2-apache

# Copy all project files to Apache web directory
COPY . /var/www/html/

# Expose Apache default port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
