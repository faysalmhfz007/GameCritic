# Social Features Setup Guide

This document describes the new social features added to GameCritic:
- Friend Requests System
- Chat/Messaging System  
- Notifications System

## Database Setup

1. Run the SQL file to create the necessary tables:
   ```sql
   source create_social_features_tables.sql
   ```
   
   Or manually execute the SQL commands in `create_social_features_tables.sql`

   This creates the following tables:
   - `friend_requests` - Stores friend request status
   - `friendships` - Stores confirmed friendships
   - `messages` - Stores chat messages
   - `notifications` - Stores user notifications

## Features

### 1. Friend Requests
- **Access**: Click "Friends" icon in navbar or visit `/friend/friends`
- **Send Request**: Visit `/friend/requests` and search for users
- **Accept/Reject**: View pending requests and accept/reject them
- **Cancel**: Cancel sent requests that are still pending

### 2. Chat/Messaging
- **Access**: Click "Messages" icon in navbar or visit `/chat`
- **Features**:
  - Real-time conversation view
  - Message history
  - Unread message indicators
  - Auto-refresh every 5 seconds

### 3. Notifications
- **Access**: Click the bell icon in the navbar
- **Features**:
  - Real-time notification badge showing unread count
  - Notification dropdown with latest notifications
  - Mark as read / Mark all as read
  - Delete notifications
  - Auto-refresh every 30 seconds
- **Notification Types**:
  - Friend requests
  - Friend accepted
  - New messages
  - System notifications

## Routes Added

### Friend Routes
- `GET /friend/requests` - Friend requests page
- `GET /friend/friends` - Friends list page
- `GET /friend/search-users` - Search users API
- `POST /friend/send-request` - Send friend request
- `POST /friend/accept-request` - Accept friend request
- `POST /friend/reject-request` - Reject friend request
- `POST /friend/cancel-request` - Cancel friend request

### Message Routes
- `GET /chat` - Chat page
- `POST /message/send` - Send message
- `GET /message/get-conversation` - Get conversation API
- `GET /message/get-conversations` - Get conversations list API
- `GET /message/get-unread-count` - Get unread count API

### Notification Routes
- `GET /notification/get` - Get notifications API
- `GET /notification/get-unread-count` - Get unread count API
- `POST /notification/mark-read` - Mark notification as read
- `POST /notification/mark-all-read` - Mark all as read
- `POST /notification/delete` - Delete notification

## Files Created

### Models
- `app/models/FriendModel.php` - Friend request and friendship management
- `app/models/MessageModel.php` - Message and conversation management
- `app/models/NotificationModel.php` - Notification management

### Controllers
- `app/controllers/FriendController.php` - Friend request actions
- `app/controllers/MessageController.php` - Message actions
- `app/controllers/NotificationController.php` - Notification actions

### Views
- `app/views/user/friend-requests.php` - Friend requests management page
- `app/views/user/friends.php` - Friends list page
- `app/views/user/chat.php` - Chat interface

### JavaScript
- `public/js/notifications.js` - Notification system JavaScript

### Database
- `create_social_features_tables.sql` - Database schema

## Files Modified

- `public/index.php` - Added new routes
- `app/views/layouts/main.php` - Added notification icon, chat link, friends link
- `app/controllers/BaseController.php` - Updated to always pass currentUser to views

## Usage

1. **Setup Database**: Run the SQL file first
2. **Login**: Users must be logged in to use social features
3. **Friend Requests**: 
   - Go to Friend Requests page
   - Search for users
   - Send friend requests
   - Accept/reject pending requests
4. **Chat**: 
   - Click Messages icon
   - Select a friend to chat with
   - Send messages in real-time
5. **Notifications**: 
   - Click bell icon to view notifications
   - Notifications auto-update every 30 seconds
   - Click notifications to mark as read

## Notes

- All features require user authentication
- Friend requests are two-way (users must accept to become friends)
- Messages are stored in the database and persist across sessions
- Notifications are automatically created for:
  - Friend request sent/received
  - Friend request accepted
  - New messages received
- The notification badge shows unread count and updates automatically
