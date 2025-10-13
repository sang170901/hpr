@echo off
echo ========================================
echo VNMaterial - Force Push Script
echo ========================================

REM Change to project directory
cd /d "c:\xampp\htdocs\vnmt"

echo Current directory: %CD%
echo.

REM Check if git is available
echo Checking Git installation...
git --version
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Git is not installed or not in PATH
    echo Please install Git first: https://git-scm.com/download/win
    pause
    exit /b 1
)

echo.
echo Step 1: Git Status
git status

echo.
echo Step 2: Adding all files...
git add .

echo.
echo Step 3: Committing changes...
git commit -m "feat: Complete VNMaterial monorepo restructure - BREAKING CHANGE: Replace entire repository with new structure"

echo.
echo Step 4: Force pushing to GitHub...
git push --force --set-upstream origin main

echo.
echo Step 5: Final status check...
git status

echo.
echo ========================================
echo SUCCESS! Check your repository at:
echo https://github.com/sang170901/vnm
echo ========================================

pause