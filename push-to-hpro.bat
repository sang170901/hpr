@echo off
setlocal
set GIT="C:\Program Files\Git\bin\git.exe"

echo ========================================
echo Push to GitHub: hpro.git
echo ========================================

cd /d "c:\xampp\htdocs\vnmt"

echo.
echo Checking Git installation...
%GIT% --version
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Git not found at %GIT%
    pause
    exit /b 1
)

echo Git found! Continuing...
echo.

echo Step 1: Initialize Git repository
%GIT% init

echo.
echo Step 2: Configure Git user
%GIT% config user.name "sang170901"
%GIT% config user.email "sang170901@gmail.com"

echo.
echo Step 3: Add all files
%GIT% add .

echo.
echo Step 4: Commit
%GIT% commit -m "chore: initial commit - VNMaterial monorepo"

echo.
echo Step 5: Set remote to hpro.git
%GIT% remote remove origin 2>nul
%GIT% remote add origin https://github.com/sang170901/hpro.git

echo.
echo Step 6: Push to main branch
echo.
echo IMPORTANT: When prompted for credentials:
echo   Username: sang170901
echo   Password: [Your Personal Access Token]
echo.
echo If you don't have a token, create one at:
echo https://github.com/settings/tokens
echo (Select 'repo' scope)
echo.
pause
echo.
echo Pushing to GitHub...
%GIT% branch -M main
%GIT% push -u origin main

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo SUCCESS! Files uploaded to GitHub
    echo Check: https://github.com/sang170901/hpro
    echo ========================================
) else (
    echo.
    echo ========================================
    echo PUSH FAILED!
    echo.
    echo Possible reasons:
    echo 1. Authentication failed - create Personal Access Token
    echo 2. Repository doesn't exist on GitHub
    echo 3. No internet connection
    echo ========================================
)

echo.
pause
endlocal
