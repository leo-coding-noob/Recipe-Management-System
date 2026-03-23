# RecipeNest — BCA 4th Semester Project
## Full Setup Guide

---

## FOLDER STRUCTURE

Place files exactly like this inside your XAMPP/WAMP htdocs:

```
htdocs/
└── recipenest/
    ├── php/
    │   ├── config.php
    │   ├── header.php
    │   ├── footer.php
    │   ├── index.php          ← PUBLIC homepage (no login needed)
    │   ├── dashboard.php      ← User dashboard (login required)
    │   ├── login.php
    │   ├── signup.php
    │   ├── logout.php
    │   ├── add.php
    │   ├── edit.php
    │   ├── delete.php
    │   ├── view.php
    │   ├── favorites.php
    │   └── toggle_favorite.php
    ├── css/
    │   ├── base.css
    │   ├── home.css
    │   ├── dashboard.css
    │   ├── auth.css
    │   ├── form.css
    │   └── view.css
    ├── js/
    │   └── script.js
    ├── uploads/          ← Create this folder, set to writable (chmod 755)
    └── sql/
        └── setup.sql
```

---

## STEP 1 — DATABASE SETUP

1. Open phpMyAdmin → http://localhost/phpmyadmin
2. Click the **SQL** tab at the top
3. Paste the entire contents of `sql/setup.sql`
4. Click **Go**

This creates:
- `recipe_db` database
- `users` table
- `recipes` table (with category, prep_time, cook_time, servings, user_id)
- `favorites` table
- `activity_log` table
- 1 sample user + 5 sample recipes

**Sample login credentials:**
- Email: `anish@example.com`
- Password: `password`

---

## STEP 2 — CONFIG

Open `php/config.php` and update:
```php
$host = "127.0.0.1";
$user = "root";
$pass = "";        // your MySQL password (usually empty in XAMPP)
$port = 3306;      // usually 3306, check phpMyAdmin
$db   = "recipe_db";
```

---

## STEP 3 — UPLOADS FOLDER

Create an `uploads/` folder inside `recipenest/` and make sure it's writable.

**On Windows (XAMPP):** Right-click → Properties → Security → Full control

**On Linux/Mac:**
```bash
chmod 755 uploads/
```

---

## STEP 4 — RUN

Visit: `http://localhost/recipenest/php/index.php`

---

## FEATURES

### Public (no login needed)
- Browse all recipes with images
- Filter by category (Breakfast, Lunch, Dinner, Snacks, Dessert)
- Search recipes by title or ingredient
- View full recipe detail (ingredients, instructions, time)
- See related recipes

### Logged-in users
- Dashboard with stats (total recipes, my recipes, favorites, categories)
- Category breakdown bar chart
- Monthly recipe activity chart
- Recent activity feed (added/edited/deleted)
- Add new recipe with image upload
- Edit your own recipes
- Delete your own recipes
- Save favorites (stored in database, not just localStorage)
- View favorites page

### Security
- Passwords hashed with PHP password_hash()
- Session-based authentication
- Users can only edit/delete their OWN recipes
- AJAX favorite toggle with JSON response
- Input sanitized with real_escape_string()

---

## PAGES SUMMARY

| Page | URL | Access |
|------|-----|--------|
| Homepage | /php/index.php | Public |
| Recipe Detail | /php/view.php?id=X | Public |
| Login | /php/login.php | Public |
| Sign Up | /php/signup.php | Public |
| Dashboard | /php/dashboard.php | Login required |
| Add Recipe | /php/add.php | Login required |
| Edit Recipe | /php/edit.php?id=X | Login required + owner |
| Delete Recipe | /php/delete.php?id=X | Login required + owner |
| Favorites | /php/favorites.php | Login required |

---

## TECHNOLOGIES USED
- **PHP** — Server-side logic, sessions, database queries
- **MySQL** — Database (users, recipes, favorites, activity_log)
- **HTML5** — Semantic markup
- **CSS3** — Custom properties, Grid, Flexbox, animations
- **JavaScript** — AJAX favorites, image preview, toast notifications, animations
- **Google Fonts** — Fraunces (display) + DM Sans (body)
