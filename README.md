# Acculance SAAS

Acculance SAAS - Mulitenancy Based Ultimate Sales, Inventory, Accounting Management System

> Acculance is an all-in-one management system that enables you to manage expenses, purchases, sales, payments, accounting, loans, assets, payroll, and many more.

## Setup Instructions

- Clone this repository to your device and run this commands:

- Copy `.env.example` file to `.env` and configure `.env` file with your own credentials

```sh
cp .env.example .env
```

> **REQUIRED FIELDS [DEV]**

```dotenv
APP_URL=http://acculance-saas.test
CENTRAL_DOMAIN=acculance-saas.test

STRIPE_KEY=required
STRIPE_SECRET=required
```

---
> **REQUIRED FIELDS [PROD]**

```dotenv
MAIL_FROM_ADDRESS=noreply@acculance-saas.test
```

> **INSTALL COMMANDS [DEV]**

---

```sh
composer install 

# if fails use 
composer update rachidlaasri/laravel-installer

# then generate a new token for composer
https://github.com/settings/tokens/new?scopes=repo&description=Composer

# paste that token in terminal
# it wil work now

npm install

php artisan key:generate

php artisan migrate --seed

npm run dev
```

---

> **INSTALL COMMANDS [PROD]**

- Coming Soon!

## **Translation Generate** **(MAKE SURE `VUE CLI` IS INSTALLED ON YOUR DEVICE)**
- Do not use _ (underscore) style translate able strings anymore.
```vue
// wrong
$t('user.first_name')

// right
$t('First Name')
```

- Do not use . (full stop) in translate able sting
```vue
// wrong
$t('Thanks for subscribing. We will notify you every week.')

// right
$t('Thanks for subscribing') + '.' + $t('We will notify you every week') + '.'

// best maybe?
{{ `${$t('Thanks for subscribing')}. ${$t('We will notify you every week')}.` }}
```

- Generate Translate Able Keys:
```shell
# for vue pages
npx vue-i18n-extract report --vueFiles './resources/js/pages/**/*.vue' --languageFiles './resources/js/lang/en.json' --add

# for vue components
npx vue-i18n-extract report --vueFiles './resources/js/components/**/*.vue' --languageFiles './resources/js/lang/en.json' --add
```
## Project setup for Import functionality

### Please follow the below steps to run the application
#### 1. Create 'demo-csv-file' folder in the public directory of the project
#### 2. Create these empty files in the 'demo-csv-file' folder
* brand.csv
* sub-categories.csv
* taxes.csv
* units.csv
### 3. Provide these two files in the 'demo-csv-file' folder for examples
* products.csv (name, model, barcode_symbology, sub_cat_id, brand_id, unit_id, tax_id, tax_type, regular_price, discount, note, alert_qty, status )
* demo.csv (name, phone, email, company_name, address)


## For Demo Setup

1) Web route welcome page in demo but not in production
2) Login page user credentials add in demo version
3) Dashboard Alert add in demo version
4) Database Export not in demo version [Central, Tenant]
5) Profile Update disabled in demo version [Central, Tenant]
6) Role Create and Update disabled in demo version [Central, Tenant]
7) Update general settings disabled in demo version [Central, Tenant]
