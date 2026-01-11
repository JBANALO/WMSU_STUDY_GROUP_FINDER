# Quick Setup: Google OAuth for WMSU Study Finder

## Step-by-Step Guide (Follow Exactly)

### Part 1: Google Cloud Console (5 minutes)

1. **Open Google Cloud Console**
   - Go to: https://console.cloud.google.com/
   - Sign in with your Google account

2. **Create New Project**
   - Click dropdown at top (next to "Google Cloud")
   - Click "NEW PROJECT"
   - Project name: `WMSU Study Finder`
   - Click "CREATE"
   - Wait for project creation (check bell icon for notification)

3. **Select Your New Project**
   - Click dropdown again
   - Select "WMSU Study Finder"

4. **Enable Required APIs**
   - Click hamburger menu (☰) → "APIs & Services" → "Library"
   - Search: `Google+ API`
   - Click "Google+ API"
   - Click "ENABLE"
   - Wait for it to enable

5. **Configure Consent Screen**
   - Click hamburger menu (☰) → "APIs & Services" → "OAuth consent screen"
   - You'll land on "OAuth Overview" page - this is correct!
   
   **Now configure the consent screen:**
   - Look at LEFT SIDEBAR, click **"Branding"**
   - Or scroll down and find the setup wizard
   - Select User Type: **External**
   - Click "CREATE" or "CONFIGURE"
   
   **Fill in the Branding/App Information:**
   - App name: `WMSU Study Group Finder`
   - User support email: (select your email from dropdown)
   - App logo: (skip this, optional)
   - Developer contact email: (your email)
   - Click "SAVE AND CONTINUE"
   
   **Scopes page:**
   - Just click "SAVE AND CONTINUE" (no changes needed)
   
   **Test users:**
   - Click "ADD USERS"
   - Add your @wmsu.edu.ph email
   - Click "ADD"
   - Click "SAVE AND CONTINUE"
   
   **Summary page:**
   - Review everything
   - Click "BACK TO DASHBOARD"

6. **Create OAuth Credentials**
   - Click hamburger menu (☰) → "APIs & Services" → "Credentials"
   - Click "CREATE CREDENTIALS" → "OAuth client ID"
   
   **Application type:** Web application
   
   **Name:** `WMSU Study Finder Web`
   
   **Authorized JavaScript origins:**
   - Click "ADD URI"
   - Add: `http://localhost:8000`
   - Click "ADD URI"
   - Add: `https://web-production-76301.up.railway.app`
   
   **Authorized redirect URIs:**
   - Click "ADD URI"
   - Add: `http://localhost:8000/handlers/google_oauth_handler.php`
   - Click "ADD URI"
   - Add: `https://web-production-76301.up.railway.app/handlers/google_oauth_handler.php`
   
   - Click "CREATE"

7. **COPY YOUR CREDENTIALS** ⚠️ IMPORTANT!
   - You'll see a popup with:
     - **Your Client ID** (looks like: 123456789-abc...apps.googleusercontent.com)
     - **Your Client Secret** (looks like: GOCSPX-abc...)
   
   - **COPY BOTH** and send them to me via chat
   - Or download JSON and I'll extract them

---

### Part 2: I'll Configure Everything Else

Once you send me the Client ID and Client Secret, I'll:
1. ✅ Update local config file
2. ✅ Update Railway environment variables (via CLI)
3. ✅ Test and verify everything works

---

## What to Send Me

Just copy-paste this format and fill it in:

```
Client ID: [paste here]
Client Secret: [paste here]
```

Or send me the downloaded JSON file content.

---

## Quick Visual Guide

### Finding Credentials Page:
```
Google Cloud Console
  ↓
☰ Menu → APIs & Services → Credentials
  ↓
CREATE CREDENTIALS → OAuth client ID
```

### What the Credentials Look Like:
```
Client ID: 
123456789012-abcdefghijklmnop.apps.googleusercontent.com

Client Secret:
GOCSPX-abcd1234efgh5678ijkl
```

---

Ready? Start with Step 1 and send me the credentials when you reach Step 7!
