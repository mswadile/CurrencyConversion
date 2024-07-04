# Currency Conversion Web Application
This is a simple web application for converting currencies.

## Table of Contents
- [Overview](#overview)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Setup Instructions](#setup-instructions)
- [Configuration](#configuration)

## Overview

This web application allows users to convert currencies. It supports:
- User authentication with IP restrictions
- Password reset functionality
- Automatic exchange rate updates
- Convert currency.

## Project Structure
project-root/
│
├── public/
│ ├── index.php
│ ├── login.php
│ ├── forgot_password.php
│ ├── reset_password.php
│ └── convert.php
│ 
│
├── src/
│ ├── auth.php
│ └── currencyConverter.php
│
├── templates/
│ ├── login.html
│ ├── forgot_password.html
│ ├── reset_password.html
│ ├── convert.html
| └── css/
│   ├── convert.css
│   └── login.css
│
├── config/
| └── config.php
|
|___ cron.php
|___ currency_app.sql


## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache)

## Setup Instructions
### 1. Clone the Repository

```bash
git clone https://github.com/mswadile/CurrencyConversion.git
cd currency-converter
```

### 2. Configure the Database
- Import currency_app.sql in PHPMYADMIN

## Configuration
- Edit config/config.php file and update it with you settings.