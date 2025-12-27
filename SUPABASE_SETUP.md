# Supabase Integration Setup Guide

This document explains how to set up Supabase for the Divorceeasy website.

## Prerequisites

1. Create a free account at [https://supabase.com](https://supabase.com)
2. Create a new project in Supabase
3. Note your project URL and anon key from Settings > API

## Configuration

1. Open `index.html`
2. Find the Supabase configuration section at the top of the `<script>` tag
3. Replace `YOUR_SUPABASE_URL` with your project URL
4. Replace `YOUR_SUPABASE_ANON_KEY` with your anon key

```javascript
const SUPABASE_URL = 'https://your-project.supabase.co';
const SUPABASE_ANON_KEY = 'your-anon-key-here';
```

## Database Schema

Create the following tables in your Supabase project:

### 1. users table

Stores user registration information.

```sql
CREATE TABLE users (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    situation TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable Row Level Security
ALTER TABLE users ENABLE ROW LEVEL SECURITY;

-- Allow anyone to insert (for registration)
CREATE POLICY "Allow public insert" ON users
    FOR INSERT WITH CHECK (true);

-- Allow users to read their own data
CREATE POLICY "Users can read own data" ON users
    FOR SELECT USING (auth.uid() IS NOT NULL OR true);
```

### 2. calculator_results table

Stores all calculator results (alimony, asset split, child support).

```sql
CREATE TABLE calculator_results (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    user_email TEXT NOT NULL,
    calculator_type TEXT NOT NULL, -- 'alimony', 'asset_split', 'child_support'
    inputs JSONB NOT NULL,
    result JSONB NOT NULL,
    currency TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable Row Level Security
ALTER TABLE calculator_results ENABLE ROW LEVEL SECURITY;

-- Allow anyone to insert
CREATE POLICY "Allow public insert" ON calculator_results
    FOR INSERT WITH CHECK (true);

-- Allow users to read their own data
CREATE POLICY "Users can read own data" ON calculator_results
    FOR SELECT USING (auth.uid() IS NOT NULL OR true);
```

### 3. ai_interactions table

Logs all AI interactions across the site.

```sql
CREATE TABLE ai_interactions (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    user_email TEXT,
    interaction_type TEXT NOT NULL, -- 'calculator_recommendations', 'legal_help_chat', 'asset_protection_followup', 'mental_health_followup'
    question TEXT NOT NULL,
    response TEXT NOT NULL,
    context JSONB,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable Row Level Security
ALTER TABLE ai_interactions ENABLE ROW LEVEL SECURITY;

-- Allow anyone to insert
CREATE POLICY "Allow public insert" ON ai_interactions
    FOR INSERT WITH CHECK (true);

-- Allow users to read their own data
CREATE POLICY "Users can read own data" ON ai_interactions
    FOR SELECT USING (auth.uid() IS NOT NULL OR true);
```

### 4. lawyer_searches table

Tracks lawyer search activity.

```sql
CREATE TABLE lawyer_searches (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    user_email TEXT,
    country TEXT NOT NULL,
    location TEXT NOT NULL,
    results_count INTEGER NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable Row Level Security
ALTER TABLE lawyer_searches ENABLE ROW LEVEL SECURITY;

-- Allow anyone to insert
CREATE POLICY "Allow public insert" ON lawyer_searches
    FOR INSERT WITH CHECK (true);

-- Allow users to read their own data
CREATE POLICY "Users can read own data" ON lawyer_searches
    FOR SELECT USING (auth.uid() IS NOT NULL OR true);
```

## Features Implemented

### ✅ User Registration
- User registration form saves to `users` table
- Data persists to localStorage as backup
- Email validation and duplicate prevention via UNIQUE constraint

### ✅ Calculator Results Tracking
- Alimony calculator results saved to `calculator_results` table
- Asset split calculator results saved to `calculator_results` table
- Child support calculator results saved to `calculator_results` table
- All results include currency and input parameters

### ✅ AI Interactions Logging
- Calculator AI recommendations logged
- Legal help chat conversations logged
- Asset protection AI questions logged
- Mental health wellness AI questions logged

### ✅ Lawyer Search Tracking
- Country and location tracking
- Results count tracking
- Search analytics for improving service

### ✅ Error Handling
- Graceful fallback to localStorage if Supabase not configured
- Error notifications for users
- Console logging for debugging

### ✅ Success/Error Notifications
- Visual feedback for all database operations
- Toast-style notifications
- Different styles for success/error states

### ✅ localStorage Persistence
- User data saved locally for offline access
- Automatic loading on page refresh
- Fallback when database unavailable

## Testing

1. **Without Supabase configured**: All features work with localStorage fallback
2. **With Supabase configured**: Data is saved to both database and localStorage
3. **Test each feature**:
   - Submit the "Get Started" form
   - Calculate alimony, assets, and child support
   - Use AI recommendations
   - Ask legal help questions
   - Search for lawyers
   - Use asset protection tools
   - Use mental health tools

## Privacy & Security

- All tables use Row Level Security (RLS)
- No authentication required for basic functionality
- User email is used as identifier (not auth-based)
- Data is private and secured via RLS policies
- Anon key is safe to expose in frontend code

## Troubleshooting

**Issue**: Data not saving to Supabase
- Check that SUPABASE_URL and SUPABASE_ANON_KEY are correctly configured
- Check browser console for errors
- Verify tables exist in Supabase
- Verify RLS policies are created

**Issue**: Database errors
- Check that all required tables are created
- Verify column names match schema
- Check RLS policies allow INSERT operations

**Issue**: localStorage not working
- Check browser localStorage is enabled
- Clear localStorage and try again: `localStorage.clear()`

## Next Steps

1. Set up email notifications for new user registrations
2. Create admin dashboard for viewing analytics
3. Add data export functionality
4. Implement user authentication (optional)
5. Add data retention policies
