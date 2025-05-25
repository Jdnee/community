# Deployment Guide

## Prerequisites
1. A web hosting account with:
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Apache/Nginx web server
   - SSL certificate (recommended)

## Files to Upload
1. All PHP files
2. CSS files
3. JavaScript files
4. Uploads directory (create if not exists)
5. Configuration files

## Database Setup
1. Create a new database in your hosting control panel
2. Update `config/database.php` with your hosting credentials:
   ```php
   $host = 'your_host'; // Usually 'localhost'
   $dbname = 'your_database_name';
   $username = 'your_database_username';
   $password = 'your_database_password';
   ```

## Steps to Deploy

1. **Prepare Your Files**
   - Create a ZIP file of your project
   - Exclude unnecessary files:
     - `.git` directory
     - `node_modules`
     - Development configuration files
     - Any temporary files

2. **Upload to Hosting**
   - Use FTP or hosting control panel's file manager
   - Upload all files to your public_html or www directory
   - Set proper permissions:
     - Directories: 755
     - Files: 644
     - Uploads directory: 777

3. **Database Setup**
   - Create a new database in your hosting control panel
   - Import the database structure:
     - Use phpMyAdmin
     - Import the SQL from `database.sql`
   - Update database configuration in `config/database.php`

4. **Configure Web Server**
   - Ensure mod_rewrite is enabled (for Apache)
   - Set proper PHP version
   - Configure SSL if available

5. **Security Measures**
   - Set proper file permissions
   - Enable error reporting in development, disable in production
   - Update `config/database.php` to use production credentials
   - Remove or protect setup files:
     - `setup.php`
     - `create_db.php`
     - `init_db.php`

6. **Testing**
   - Test user registration
   - Test login functionality
   - Test post creation
   - Test image uploads
   - Test all features in production environment

## Common Issues and Solutions

1. **Database Connection Error**
   - Verify database credentials
   - Check if database exists
   - Ensure database user has proper permissions

2. **File Upload Issues**
   - Check upload directory permissions
   - Verify PHP upload settings in php.ini
   - Check file size limits

3. **404 Errors**
   - Verify .htaccess configuration
   - Check file permissions
   - Ensure mod_rewrite is enabled

4. **500 Server Errors**
   - Check error logs
   - Verify PHP version compatibility
   - Check file permissions

## Maintenance

1. **Regular Backups**
   - Database backups
   - File backups
   - Upload directory backups

2. **Updates**
   - Keep PHP version updated
   - Update dependencies
   - Monitor security advisories

3. **Monitoring**
   - Set up error logging
   - Monitor server resources
   - Check for security issues

## Support

If you encounter any issues during deployment:
1. Check error logs
2. Verify all prerequisites
3. Test in a staging environment first
4. Contact hosting support if needed 