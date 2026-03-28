# RecipeNest — Recipe Sharing Platform

## 📋 Overview

RecipeNest is a full-stack web application for sharing and discovering recipes. Users can browse recipes without an account, while registered users can add their own recipes, save favorites, and manage their personal recipe collection. The application features a clean, responsive design with interactive dashboards and real-time feedback.

##  Features

### Public Access (No Login Required)
- **Browse Recipes** — View all recipes with images, categories, and metadata
- **Search** — Search recipes by title or ingredients
- **Filter by Category** — Filter by Breakfast, Lunch, Dinner, Snacks, Dessert
- **Recipe Details** — View full recipe with ingredients, instructions, cooking times, and servings
- **Related Recipes** — See similar recipes in the same category


### Registered Users
- **User Dashboard** — Personal dashboard with:
  - Statistics (total recipes, your recipes, favorites count, categories)
  - Category breakdown chart with visual bars
  - Monthly recipe activity chart (last 6 months)
  - Recent activity feed (track added/edited/deleted recipes)
  - Quick access to your recent recipes
- **Recipe Management** — Add, edit, and delete your own recipes
- **Image Upload** — Upload recipe images (JPG, PNG, WEBP up to 5MB)
- **Favorites System** — Save favorite recipes with AJAX (no page reload)
- **Favorites Page** — View all saved recipes in one place
- **Comments** — Leave comments on recipes (delete own comments)

### Admin Features
- **Admin Panel** — View all recipes with author details
- **Delete Any Recipe** — Admin can remove inappropriate content
- **Delete Any Comment** — Admin can moderate comments

---

## 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| PHP | Server-side logic, authentication, database operations |
| MySQL | Database storage (users, recipes, favorites, comments, activity logs) |
| HTML5 | Semantic structure |
| CSS3 | Custom styling with CSS variables, Grid, Flexbox, animations |
| JavaScript | AJAX favorites, image preview, toast notifications, animations |
| Google Fonts | Fraunces (display) + DM Sans (body) |



## 📁 Project Structure

recipenest/
├── php/                      # PHP backend files
│   ├── config.php            # Database configuration
│   ├── header.php            # Shared header template
│   ├── footer.php            # Shared footer template
│   ├── index.php             # Public homepage
│   ├── dashboard.php         # User dashboard (login required)
│   ├── login.php             # Login page
│   ├── signup.php            # Registration page
│   ├── logout.php            # Logout handler
│   ├── add.php               # Add new recipe
│   ├── edit.php              # Edit existing recipe
│   ├── delete.php            # Delete recipe
│   ├── view.php              # Recipe detail page
│   ├── favorites.php         # User favorites list
│   ├── myrecipes.php         # User's own recipes
│   ├── admin.php             # Admin panel
│   └── toggle_favorite.php   # AJAX favorite toggle
├── css/                      # Stylesheets
│   ├── base.css              # Global styles, variables, navigation
│   ├── home.css              # Homepage styling
│   ├── dashboard.css         # Dashboard layout and components
│   ├── auth.css              # Login/signup pages
│   ├── form.css              # Add/edit recipe forms
│   └── view.css              # Recipe detail page
├── js/
│   └── script.js             # Favorites, previews, toasts, animations
├── uploads/                  # Uploaded recipe images (create this folder)
└── README.md


##  Database Setup

### Step 1: Create Database

1. Open phpMyAdmin at `http://localhost/phpmyadmin`
2. Click the **SQL** tab
3. Copy and paste the entire contents of `sql/setup.sql`
4. Click **Go**

This creates the `food_db` database with the following tables:

| Table | Columns |
|---|---|
| users | id, username, email, password, is_admin, avatar_color, created_at |
| recipes | id, user_id, title, category, ingredients, instructions, image, prep_time, cook_time, servings, created_at |
| favorites | id, user_id, recipe_id, created_at |
| comments | id, recipe_id, user_id, comment, created_at |
| activity_log | id, user_id, action, recipe_title, created_at |

### Sample Login Credentials

| Email | Password |
|---|---|
| saurav@example.com | password |
| admin@recipenest.com | admin123 |

---

## ⚙️ Configuration

### Step 1: Update Database Credentials

Open `php/config.php` and update:

```php
$host = "127.0.0.1";
$user = "root";
$pass = "";        // Your MySQL password (empty for XAMPP)
$port = 3307;      // 3306 for default XAMPP, 3307 if using MariaDB
$db   = "food_db";
```

### Step 2: Create Uploads Directory

Create the `uploads/` folder inside `recipenest/` and ensure it is writable.

**Windows (XAMPP):**
Right-click `uploads` folder → Properties → Security → Edit → Full control



##  Running the Application

1. Start **Apache** and **MySQL** in XAMPP/WAMP
2. Place the `recipenest/` folder in your `htdocs` directory
3. Open your browser and go to:

```
http://localhost/recipenest/php/index.php
```

---

##  Page URLs

| Page | URL | Access |
|---|---|---|
| Homepage | /php/index.php | Public |
| Recipe Detail | /php/view.php?id=X | Public |
| Login | /php/login.php | Public |
| Sign Up | /php/signup.php | Public |
| Dashboard | /php/dashboard.php | Login Required |
| My Recipes | /php/myrecipes.php | Login Required |
| Add Recipe | /php/add.php | Login Required |
| Edit Recipe | /php/edit.php?id=X | Login + Owner |
| Delete Recipe | /php/delete.php?id=X | Login + Owner |
| Favorites | /php/favorites.php | Login Required |
| Admin Panel | /php/admin.php | Admin Only |

---

##  Security Features

- **Password Hashing** — Passwords stored using `password_hash()`
- **Session Authentication** — User sessions for access control
- **Owner Verification** — Users can only edit/delete their own recipes
- **SQL Injection Prevention** — All inputs sanitized with `real_escape_string()`
- **File Upload Validation** — Only allowed image types, size limit 5MB
- **Admin Privileges** — Separate admin flag for moderation access

---

##  Responsive Design

The application is fully responsive and works on:

- Desktop (1200px+)
- Tablet (768px – 1199px)
- Mobile (below 768px)

Key responsive features: collapsible mobile navigation, adaptive grid layouts, touch-friendly buttons, readable font sizes on all devices.


##  Key JavaScript Features

| Feature | Description |
|---|---|
| Image Preview | Live preview when uploading recipe images |
| AJAX Favorites | Toggle favorites without page reload |
| Toast Notifications | Non-intrusive feedback messages |
| Number Animations | Animated stats counter on dashboard |
| Chart Animations | Smooth transitions for category and monthly charts |
| Drag & Drop Upload | Drag images directly to the upload area |
| Live Search | Search as you type with debounce |
| Comment Management | Delete comments with confirmation dialog |

---

## 🐛 Troubleshooting

**Cannot upload images**
- Check `uploads/` folder exists and has write permissions
- Verify PHP file upload limits in `php.ini`

**Database connection error**
- Confirm MySQL is running
- Check credentials in `config.php`
- Verify port number (3306 or 3307)

**Images not displaying**
- Check image paths in database (`uploads/filename.jpg`)
- Verify images exist in the `uploads/` folder
- Check file permissions

**Session not persisting**
- Ensure `session_start()` is called at the top of each PHP file
- Check browser cookie settings



## 👥 Credits

BCA 4th Semester Project — Recipe Sharing Platform by #Saurav Bhandari @ Rusran Gopali



## 📝 License

This project is for educational purposes as part of the BCA curriculum.
