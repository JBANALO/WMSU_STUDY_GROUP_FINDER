# Google OAuth Setup Guide

## Overview
This guide will help you set up Google Sign-In for the WMSU Study Group Finder application.

## Benefits of Google Sign-In
- ✅ Verifies real WMSU accounts (@wmsu.edu.ph)
- ✅ Prevents fake accounts
- ✅ Easier for users (no password to remember)
- ✅ Still requires admin approval for security

## Setup Steps

### Part 1: Google Cloud Console Setup

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/

2. **Create a New Project**
   - Click "Select a project" → "New Project"
   - Project name: `WMSU Study Finder` (or your choice)
   - Click "Create"

3. **Enable Google+ API**
   - Go to "APIs & Services" → "Library"
   - Search for "Google+ API"
   - Click "Enable"

4. **Create OAuth 2.0 Credentials**
   - Go to "APIs & Services" → "Credentials"
   - Click "Create Credentials" → "OAuth client ID"
   - If prompted, configure consent screen first:
     - User Type: External
     - App name: WMSU Study Group Finder
     - User support email: Your email
     - Developer contact: Your email
     - Click "Save and Continue"
     - Scopes: No changes needed, click "Save and Continue"
     - Test users: Add your @wmsu.edu.ph email
     - Click "Save and Continue"

5. **Configure OAuth Client**
   - Application type: **Web application**
   - Name: `WMSU Study Finder Web Client`
   
   - **Authorized JavaScript origins:**
     - Local: `http://localhost:8000`
     - Railway: `https://your-app.up.railway.app`
   
   - **Authorized redirect URIs:**
     - Local: `http://localhost:8000/handlers/google_oauth_handler.php`
     - Railway: `https://your-app.up.railway.app/handlers/google_oauth_handler.php`
   
   - Click "Create"

6. **Save Your Credentials**
   - Copy the **Client ID** (looks like: `123456789-abc...googleusercontent.com`)
   - Copy the **Client Secret** (looks like: `GOCSPX-...`)
   - Keep these safe!

### Part 2: Local Setup (XAMPP)

1. **Update Google OAuth Config**
   - Open: `config/google_oauth.php`
   - Replace `YOUR_LOCAL_CLIENT_ID` with your Client ID
   - Replace `YOUR_LOCAL_CLIENT_SECRET` with your Client Secret

2. **Run Database Migration**
   - Open browser: `http://localhost:8000/add_google_id_column.php`
   - This adds the `google_id` column to users table

3. **Test Google Sign-In**
   - Go to: `http://localhost:8000/index.php?page=login`
   - Click "Sign in with Google"
   - Select your @wmsu.edu.ph account
   - Verify you're redirected back

### Part 3: Railway Deployment

1. **Add Environment Variables**
   - Go to Railway dashboard → Your project
   - Click on your app service → "Variables"
   - Add these variables:
     ```
     GOOGLE_CLIENT_ID=your_client_id_here
     GOOGLE_CLIENT_SECRET=your_client_secret_here
     ```

2. **Update Redirect URI in config**
   - Open: `config/google_oauth.php`
   - Replace `https://web-production-...up.railway.app` with your actual Railway URL
   - Commit and push:
     ```bash
     git add config/google_oauth.php
     git commit -m "Update Google OAuth redirect URI for Railway"
     git push
     ```

3. **Run Database Migration on Railway**
   - Visit: `https://your-app.up.railway.app/add_google_id_column.php`
   - Verify the column is added

4. **Test on Railway**
   - Visit: `https://your-app.up.railway.app/index.php?page=login`
   - Click "Sign in with Google"
   - Verify authentication works

## How It Works

### User Flow

1. **User clicks "Sign in with Google"**
   - Redirected to Google sign-in page
   - Must use @wmsu.edu.ph email

2. **Google authenticates user**
   - Verifies it's a real Google account
   - Returns user info (email, name, etc.)

3. **System checks account status:**
   - **New user:** Creates account with `status='pending'`
   - **Existing user:** Links Google ID to account
   - **Pending user:** Shows "Wait for admin approval" message
   - **Approved user:** Logs in successfully

4. **Admin approves account**
   - Admin reviews in dashboard
   - Approves legitimate WMSU students
   - User can then sign in

### Security Benefits

- ✅ **Two-layer security:**
  1. Google OAuth verifies real account
  2. Admin approval verifies WMSU student
  
- ✅ **Prevents fake accounts:** Can't use fake emails

- ✅ **Easy for users:** One-click sign-in

- ✅ **No password management:** Google handles it

## Troubleshooting

### Error: "redirect_uri_mismatch"
**Solution:** Check that redirect URI in Google Console matches exactly:
- Local: `http://localhost:8000/handlers/google_oauth_handler.php`
- Railway: `https://your-actual-domain.up.railway.app/handlers/google_oauth_handler.php`

### Error: "Please use your WMSU email"
**Solution:** This is correct! System only accepts @wmsu.edu.ph emails.

### Error: "Account pending approval"
**Solution:** Admin needs to approve the account first. Check admin dashboard.

### Google button not working
**Solution:** 
1. Check `config/google_oauth.php` has correct Client ID/Secret
2. Check Railway environment variables are set
3. Check browser console for errors

### Error: "Failed to open stream: No such file or directory" (Railway)
**Solution:** This means composer dependencies aren't installed on Railway.

**Fix:**
1. Make sure `composer.json` and `composer.lock` are committed to Git
2. Railway should auto-detect and run `composer install`
3. Check Railway deployment logs to verify composer ran
4. If button shows "Not Configured" it's working correctly - just needs setup

**Force Railway to reinstall:**
```bash
# Commit any changes
git add composer.json composer.lock
git commit -m "Force composer reinstall"
git push

# Railway will auto-redeploy and run composer install
```

### Google button shows "Not Configured"
**Solution:** This is normal if:
- Google Client ID/Secret not set in `config/google_oauth.php` (local), OR
- Environment variables not set on Railway (production)

The button will automatically enable once credentials are configured.

## Testing Checklist

- [ ] Local Google Sign-In works
- [ ] Railway Google Sign-In works
- [ ] Non-WMSU emails are rejected
- [ ] New users show "pending approval" message
- [ ] Admin can see new Google users in dashboard
- [ ] After approval, user can sign in
- [ ] Declined users see appropriate message

## Support

If you encounter issues:
1. Check Google Cloud Console logs
2. Check Railway logs (if deployed)
3. Verify all credentials are correct
4. Ensure redirect URIs match exactly

---

**Last Updated:** January 12, 2026
