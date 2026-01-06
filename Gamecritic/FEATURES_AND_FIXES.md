# New Features and Bug Fixes

## Bug Fixes

### 1. User Search for Friend Requests ✅
**Issue**: User search was not working when trying to send friend requests.

**Fix**: 
- Updated `FriendController::searchUsers()` to use `UserModel::searchUsers()` method
- Added `searchUsers()` method to `UserModel` that properly excludes banned users
- Search now correctly filters out banned users and the current user

### 2. Game Pictures Not Appearing ✅
**Issue**: Some game cover images were not displaying correctly.

**Fix**:
- Normalized cover image paths in all views
- Added proper path handling for images starting with `/images/`, `images/`, or just filenames
- Fixed image paths in:
  - `app/views/home/index.php` (Top Rated Games and All Games sections)
  - `app/views/game/show.php` (Game detail page)
  - All game listings now use consistent image path normalization

## New Features

### 3. Admin Ban User Feature ✅
**Description**: Admins can now ban users based on their reviews.

**Implementation**:
- Added `banned`, `banned_at`, and `banned_reason` columns to `users` table
- Created `AdminUserController` with ban/unban functionality
- Added `/admin/users` page showing all users with reviews
- Admins can ban users with a reason (defaults to "Violation of community guidelines")
- Banned users are prevented from logging in
- Banned users are excluded from friend search results

**Files Created**:
- `app/controllers/AdminUserController.php`
- `app/views/admin/users.php`
- `add_ban_and_spoiler_features.sql`

**Files Modified**:
- `app/models/UserModel.php` - Added `banUser()`, `unbanUser()`, `isBanned()`, `searchUsers()`, `getUsersWithReviews()` methods
- `app/controllers/AuthController.php` - Updated authentication to check if user is banned
- `app/views/admin/dashboard.php` - Added link to user management
- `public/index.php` - Added routes for user management

**Usage**:
1. Admin logs in and goes to Admin Dashboard
2. Click "Manage Users" button
3. View all users with their review counts
4. Click "Ban" button next to a user
5. Enter ban reason and confirm
6. User is banned and cannot login

### 4. User Hide Game (Spoiler) Feature ✅
**Description**: Users can mark games as "spoiler" to hide them from their view.

**Implementation**:
- Created `user_hidden_games` table to track hidden games per user
- Created `UserHiddenGameModel` for managing hidden games
- Created `UserGameController` for hide/unhide actions
- Hidden games are filtered out from:
  - Homepage game listings
  - Search results
  - Top rated games
  - Recommended games
  - Filtered game lists
- Added "Hide Game (Spoiler)" button on game detail pages

**Files Created**:
- `app/models/UserHiddenGameModel.php`
- `app/controllers/UserGameController.php`
- `add_ban_and_spoiler_features.sql`

**Files Modified**:
- `app/models/GameModel.php` - Updated `getGamesBySearch()` to accept excluded game IDs
- `app/controllers/HomeController.php` - Added filtering logic for hidden games in `index()` and `filter()` methods
- `app/views/game/show.php` - Added hide/unhide button
- `public/index.php` - Added routes for hide/unhide

**Usage**:
1. User views a game detail page
2. Clicks "Hide Game (Spoiler)" button
3. Game is hidden from all listings for that user
4. User can click "Unhide Game" to show it again
5. Hidden games won't appear in:
   - Homepage listings
   - Search results
   - Top rated games
   - Recommendations
   - Genre/platform filters

## Database Setup

Run the following SQL file to add the new features:

```sql
source add_ban_and_spoiler_features.sql
```

Or execute the commands in `add_ban_and_spoiler_features.sql` manually.

## Routes Added

### Admin User Management
- `GET /admin/users` - List all users with reviews
- `POST /admin/ban-user` - Ban a user
- `POST /admin/unban-user` - Unban a user

### User Game Hide/Show
- `POST /game/hide` - Hide a game (mark as spoiler)
- `POST /game/unhide` - Unhide a game

## Testing

1. **Test User Search**: 
   - Login as a user
   - Go to Friend Requests page
   - Search for users - should find all non-banned users

2. **Test Game Images**:
   - View homepage - all game images should display
   - View game detail pages - cover images should display correctly

3. **Test Admin Ban**:
   - Login as admin
   - Go to Admin Dashboard → Manage Users
   - Ban a test user
   - Try to login as that user - should fail

4. **Test Hide Game**:
   - Login as a user
   - View a game detail page
   - Click "Hide Game (Spoiler)"
   - Return to homepage - game should not appear
   - Go back to game page and click "Unhide Game"
   - Game should appear again in listings
