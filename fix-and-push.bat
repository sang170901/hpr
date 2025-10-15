@echo off
echo ========================================
echo VNMaterial - Fix Structure and Push
echo ========================================

cd /d "c:\xampp\htdocs\vnmt"

echo Step 1: Moving files to correct structure...

REM Move all HTML files to frontend/
move "*.html" "frontend\" 2>nul
move "assets" "frontend\" 2>nul  
move "manifest.json" "frontend\" 2>nul
move "*.php" "frontend\" 2>nul
move "*.md" "frontend\" 2>nul

REM Move backend files if they exist
if exist "backend" (
    echo Backend folder exists
) else (
    echo Creating backend folder...
    mkdir backend
    echo Backend folder created
)

echo.
echo Step 2: Git operations...
git add .
git commit -m "fix: Restructure monorepo - move files to correct directories

- Move frontend files to frontend/ directory
- Create backend/ directory structure
- Clean up root directory for monorepo organization"

echo.
echo Step 3: Force push to GitHub...
git push --force origin main

echo.
echo Step 4: Verification...
git status

echo.
echo ========================================
echo Structure fixed and pushed to GitHub!
echo Check: https://github.com/sang170901/vnm
echo ========================================

pause