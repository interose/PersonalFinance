PersonalFinance
===============

PersonalFinance is a self-hosted personal finance manager. It uses the PHP FinTS/HBCI library (https://github.com/nemiah/phpFinTS) in order to fetch transfers.

Why another finance manager?
----------------------------
Back in 2017 I managed my finances within a spreadsheet. Soon I started the development of my personal finance manager tool because at this time I could not find any solution which meets my expectations and also as a software developer I was interested in creating my own solution ;)

Note/Disclaimer
---------------
This tool is extremely tailored to my needs. It is also a spare time project and there are some open points. E.g. not all items are translated.  

A picture is worth a thousand words
-----------------------------------

![Dashboard](/doc/images/01_dashboard.png)

![Monthly Grid](/doc/images/02_monthly_grid.png)

![Last half year tree](/doc/images/03_last_half_year_tree.png)

![Full year tree](/doc/images/04_full_year_tree.png)

![Chart](/doc/images/05_chart.png)

![Settings Categories](/doc/images/06_settings_categories.png)

![Settings Rules](/doc/images/07_settings_rules.png)

Installation
------------
Copy the .env file to .env.local and configure your environment. In order to use the FinTs library you have to register your software. Please check [phpFinTS](https://github.com/nemiah/phpFinTS#getting-started) for further information.

sfMoney requires PHP 8.0 and Symfony 6. Run the
following command to install it in your application:

```
$ composer install
```

The assets are installed via webpack. In order to build them you have to install the dependencies.

```
$ yarn install
$ yarn encore production
```

Setup database according your configuration. Setup tables.

```
$ bin/console doctrine:migrations:migrate
```

Start local webserver.

```
$ symfony server:start -d
```