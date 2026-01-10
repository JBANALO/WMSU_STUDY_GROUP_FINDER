# Railway Deployment Guide - WMSU Study Group Finder

## Prerequisites
- GitHub Account (already done ✓)
- Railway Account (https://railway.app)
- MySQL/PostgreSQL Database on Railway

## Deployment Steps

### Step 1: Create a Railway Account
1. Go to https://railway.app
2. Sign up with GitHub account (recommended for easy integration)
3. Verify your email

### Step 2: Create a New Railway Project
1. Click "New Project" on Railway dashboard
2. Select "Deploy from GitHub repo"
3. Select your repository: `JBANALO/WMSU_STUDY_GROUP_FINDER`
4. Authorize Railway to access your GitHub

### Step 3: Add MySQL Database
1. In Railway project dashboard, click "New Service"
2. Select "MySQL"
3. Railway will create a MySQL instance
4. Go to MySQL service → "Connect" tab
5. Copy the connection details:
   - Host
   - Port
   - User
   - Password
   - Database name

### Step 4: Configure Environment Variables
1. In Railway dashboard, go to your app service
2. Click "Variables" tab
3. Add the following environment variables:

```
APP_ENV=production
APP_DEBUG=false
DB_HOST=[MySQL Host from Step 3]
DB_PORT=3306
DB_USER=[MySQL User from Step 3]
DB_PASS=[MySQL Password from Step 3]
DB_NAME=[Database name from Step 3]
SITE_URL=[Your Railway app URL]
SECRET_KEY=[Generate a random string]
```

### Step 5: Database Setup (Automatic)
Simply visit your Railway app URL with `/setup_database.php`:
```
https://your-railway-url.app/setup_database.php
```

The setup script will:
- ✓ Connect to Railway's MySQL database
- ✓ Create all required tables automatically
- ✓ Show confirmation when complete

**Alternative Manual Setup:**
If you prefer, manually run the schema using a MySQL client like phpMyAdmin or Adminer:
```sql
-- Create users table
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(255) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  first_name VARCHAR(255),
  last_name VARCHAR(255),
  password VARCHAR(255) NOT NULL,
  status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create study_groups table
CREATE TABLE study_groups (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_name VARCHAR(255) NOT NULL,
  description TEXT,
  subject VARCHAR(255),
  creator_id INT NOT NULL,
  status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
  decline_reason TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES users(id)
);

-- Create group_members table
CREATE TABLE group_members (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_id INT NOT NULL,
  user_id INT NOT NULL,
  role VARCHAR(50) DEFAULT 'member',
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES study_groups(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create group_messages table
CREATE TABLE group_messages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_id INT NOT NULL,
  user_id INT NOT NULL,
  message TEXT,
  attachment VARCHAR(255),
  message_type VARCHAR(50) DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES study_groups(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create meetings table
CREATE TABLE meetings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  meeting_date DATETIME NOT NULL,
  location VARCHAR(255),
  is_online BOOLEAN DEFAULT 1,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES study_groups(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create notifications table
CREATE TABLE notifications (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  type VARCHAR(50),
  title VARCHAR(255),
  message TEXT,
  is_read BOOLEAN DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create user_last_seen table
CREATE TABLE user_last_seen (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  group_id INT NOT NULL,
  last_seen_message_id INT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (group_id) REFERENCES study_groups(id)
);
```

### Step 6: Deploy
1. Railway will automatically detect `Procfile`
2. Deployment will start automatically
3. You'll see a unique Railway URL (e.g., `https://wmsu-studyfinder-production.up.railway.app`)

### Step 7: Access Your App
1. Once deployment is complete, visit your Railway app URL
2. Test the application:
   - Create an account
   - Create a study group
   - Test responsive design on mobile

## Important Notes

### Database Persistence
- Railway MySQL persists data automatically
- Make backups regularly through Railway dashboard

### File Uploads
- Uploads folder (`/uploads`) is ephemeral in Railway
- For production, use a cloud storage solution like:
  - AWS S3
  - Cloudinary
  - Railway's Disk storage

### Email Configuration
- Update `.env` with actual SMTP credentials for email notifications
- Popular options:
  - Gmail (with App Password)
  - SendGrid
  - Mailgun

### HTTPS
- Railway automatically provides HTTPS
- Update `SITE_URL` in environment variables to use `https://`
- Update `config/production.php` to enforce HTTPS cookies

### Monitoring
1. Check logs in Railway dashboard → Logs tab
2. Monitor errors and performance
3. Set up alerts if needed

## Testing Deployment

### Test Admin Account
Create test users:
- Admin: Username `admin`, Email `admin@wmsu.edu.ph`
- Student: Username `student1`, Email `student1@wmsu.edu.ph`

### Test Features
- ✓ User registration and login
- ✓ Create and manage study groups
- ✓ Responsive design (test on mobile)
- ✓ Notifications system
- ✓ Group messaging
- ✓ Admin dashboard

## Troubleshooting

### Database Connection Error
- Verify `DB_HOST`, `DB_USER`, `DB_PASS` in environment variables
- Check MySQL service is running in Railway
- Test connection using MySQL client

### Upload Errors
- Configure cloud storage for production
- Check upload folder permissions

### Session Issues
- Clear browser cookies
- Check cookie configuration in `config/production.php`

### HTTPS Mixed Content
- Ensure all resources use `https://`
- Update `SITE_URL` to use HTTPS

## Support
- Railway Docs: https://docs.railway.app
- GitHub Issues: https://github.com/JBANALO/WMSU_STUDY_GROUP_FINDER/issues
- Contact: studyfinder@wmsu.edu.ph

---

**Deployment Status:** Ready for Railway ✓
**Last Updated:** January 11, 2026
