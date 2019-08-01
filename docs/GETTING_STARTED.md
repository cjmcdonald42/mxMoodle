# Getting Started with Moodle Programming

---

## Prerequisites
Moodle is a web application which is built on a variety of technologies. Before working on this project, it will be necessary for you to know each of these technologies to a varying degree:

#### HTML
All webpages are rendered in HTML _(Hyper Text Markup Language)_. You will most likely not write anything in HTML directly while working on Moodle plugins, but understanding how webpages work is really a prerequisite for any kind of web development. If you are unfamiliar with HTML, Codecademy's [Learn HTML](https://www.codecademy.com/learn/learn-html) course should be sufficient to teach you the basics. W3schools has a useful [language reference](https://www.w3schools.com/tags/default.asp) should you need to look up something specific.

#### PHP
PHP is the central language of Moodle, and you will need a solid understanding of the syntax and semantics before continuing. If you already have a Java background, perusing the [PHP Manual](https://secure.php.net/manual/en) might be enough to give you a working sense of the language. In particular, you should read the [introduction](https://secure.php.net/manual/en/introduction.php) and look through the [language reference](https://secure.php.net/manual/en/langref.php). If you want a more thorough understanding and prefer to learn actively rather than from reading language documentation, you should work through Codecademy's [Learn PHP](https://www.codecademy.com/learn/learn-php) course.

#### SQL
Moodle uses an SQL _(Structured Query Language)_ database to store and manage all of its data. Moodle provides a [Data Manipulation API](https://docs.moodle.org/dev/Data_manipulation_API) that allows you to execute simple queries without writing out the SQL. However, this API doesn't handle more complex situations, so you will still need to be familiar with how to construct an SQL query. If you haven't used SQL before, Codecademy's [Learn SQL](https://www.codecademy.com/learn/learn-sql) is a good introductory resource. You can also consult the [MySQL documentation](https://dev.mysql.com/doc/refman/8.0/en), though be wary that Moodle doesn't support all of the features of MySQL in order to support various database options. For example, many of the data types are not supported.

#### JavaScript
JavaScript allows webpages to be interactive and change without the browser needing to reload the page. This greatly improves user experience on any web application of reasonable complexity. JavaScript is used on the server-side of Moodle Plugins, but it is used somewhat frequently to make HTML forms and other elements interactive. As such, it is not essential that every individual working on the project have a deep understanding of the language, but you should at least be familiar with it. JavaScript's syntax is similar to that of Java, but it has many important syntactical and semantic differences. Codecademy's [Introduction to JavaScript](https://www.codecademy.com/learn/introduction-to-javascript) should be more than sufficient to get you up and running. Moodle relies primarily on the JS Library jQuery for all DOM interaction. Codecademy's [Introduction to jQuery](https://www.codecademy.com/learn/learn-jquery) and jQuery's [API documentation](https://api.jquery.com) are both good resources. Moodle plugins define all JavaScript in AMD _(Asynchronous Module Definition)_ modules. See [this MoodleDocs page](https://docs.moodle.org/dev/Javascript_Modules) for more information.

#### Mustache
Rather than writing HTML directly or building up the page within the PHP itself, Moodle uses the templating language Mustache to define HTML fragments which are ultimately combined and served to the user. Because these templates are inherently reusable, you won't often need to write new ones. However, if you do need to write a Mustache template, the [MoodleDocs page Templates page](https://docs.moodle.org/dev/Templates), the [Mustache Manual](https://mustache.github.io/mustache.5.html), and the documentation for the PHP implementation in [this GitHub repo](https://github.com/bobthecow/mustache.php) are all good resources.

#### CSS
CSS _(Cascading Style Sheets)_ is what determines how a webpage is ultimately displayed in the client's browser. Moodle is designed to allow for theme plugins which can change how the site looks dramatically. As such, we don't have to worry much about CSS when writing other types of plugins. There is a small amount of CSS in the repository, but it is not something that will need to change as the project expands. Moodle's current default theme, Boost, is built on Twitter's [Bootstrap API](https://getbootstrap.com/docs/4.3/getting-started/introduction), so if you want to use any Bootstrap elements, all you need to do is add the appropriate class in your mustache file or in the PHP.

Once you feel you have a sufficient grip in these technologies, you are almost ready to start working on our Moodle plugins. Before you begin, you should familiarize yourself with [MoodleDocs](https://docs.moodle.org/dev/Main_Page). Moodle's Documentation is incomplete in some areas and not always up to date, but it definitely a useful resource nonetheless. The documentation for this project will frequently reference the MoodleDocs pages of any relevant APIs.

---

## Development Environment
You will need to set up a variety of software packages on your computer before you can begin developing Moodle plugins and running a server locally.

### Software
You will need to download and install all of the following applications before proceeding:
###### NOTE: All of the download links in this section are geared towards MacOS users, but versions of these applications should all work on any platform.

##### Text Editor
**Atom** is our recommended text editor because it has a number of packages that will make your life easier while developing for Moodle. You can download Atom [here](https://atom.io). See [Setting Up Atom](#setting-up-atom) below for more on what packages to install.
###### NOTE: You can also use another text editor if there is one which you prefer, but you will have to figure out how to set up the environment yourself, so you will likely be making things more difficult for yourself.

##### Git Client
This project uses **git** for version control. Specifically, our repository has a remote on **GitHub**. As such, you will need a GitHub account in order to contribute to the project. If you don't have one, sign up on [GitHub.com](https://github.com/join). You will also need a git client on your computer. There are a number of good options out there including [Soucetree](https://www.sourcetreeapp.com), [GitHub Desktop](https://desktop.github.com), and the preinstalled [GitHub Package](https://github.atom.io) in Atom. You can even operate git from the command line directly if you are comfortable with that.

##### Trello
We use Trello to organize our development efforts and keep track of our progress. Trello is available in the browser, but we suggest that you install the desktop application from the [MacOS App Store](https://apps.apple.com/us/app/trello/id1278508951?mt=12). If you haven't used Trello before, you will need to create an account (just sign up with Google). Trello also has a mobile app.

##### MAMP
In order to test the code you are developing, you will need to have a server running locally on your computer. The software which we suggest for running a PHP and MySQL server is called [MAMP](https://www.mamp.info/en/). Conveniently, Moodle provides a package called Moodle4Mac which will set up a MAMP installation with the desired Moodle release automatically. You can download the appropriate package [here](https://download.moodle.org/macosx). It is very important that you are running the version of Moodle which we are currently developing for, so please ask someone if you do not know which version to download.
###### NOTE: You can also construct this package yourself, but the pre-packaged version that Moodle provides will be faster and less prone to configuration issues.

##### Sequel Pro
Sequel Pro is a great piece of software to view the structure and contents of your Moodle database. It is especially useful for isolating your database interactions while manually testing your code. You can download Sequel Pro [here](https://www.sequelpro.com).

___

### Setting Up Your Local Test Server

Once you have installed Moodle's MAMP Package, you are ready to set up your local development server. Because the server is pre-pacakged for you, you won't have to do very much set up. To start your server, all you need to do is open the MAMP app and press the start servers button if they do not start automatically. Now select the 'My Website' tab from the top of the landing page, and you will have arrived at your Moodle installation (this may take some time to load the first time you do it). Once you reach the homepage, select log in at the bottom and use the default credentials:

    username: admin
    password: 12345

##### Adding Middlesex Plugins
The first thing you will need to do once you have your server running is to add the existing version of our custom plugins to your server before you configure certain settings to mimic our production server as well as some specifically for development. To do this, you will first need to clone the repository to a location on your computer. This location should be somewhere within your user directory and _not_ within the MAMP installation.

Once you have cloned the repository, you will need to copy our plugins into your installation. After you have set up your IDE, this will be an automated process, but for this first time, it is a good idea to take a look at how Moodle's file structure works. In Finder navigate to /Applications/MAMP/htdocs. Here you should find a couple of directories. Select the one which corresponds to the version of Moodle you are running. In this directory, you will find all of Moodle core and the preinstalled plugins (there are close to 400). There is something on the order of 10,000 files in the default installation of Moodle. We will be contributing to the /local and /blocks directories. You now want to _copy_ (hold option while dragging in Finder) everything from MXMoodle/local to your installation's /local directory. Do the same from MXMoodle/blocks to /blocks. **When you are doing this be sure to copy the _contents_ of each directory not the entire directory itself, because you don't want to overwrite any of the existing plugins in your Moodle installation.**

Once you have copied the plugin files if you navigate to the dashboard as an administrator, you will be redirected to the installation page. All you need to do is scroll to the bottom of the page and select 'Upgrade Moodle database now,' and the plugins will be ready to go.

##### Site Configuration
Now that you have installed our plugins, there are a few more very important settings which you will need to configure for your development site. To get to these settings, select `Site administration` in the sidebar. Moodle has a lot of settings, so follow these steps carefully as you navigate through the settings tree to find the appropriate ones.
- `Site administration` > `Location` > `Location Settings`
    - `Default timezone`: set to `America/New York`
    - `Force timezone`: set to `America/New York`
    - `Default country`: set to `United States`
    - Click `Save Changes`
- `Site administration` > `Language` > `Language Packs`
    - Find `English - United States (en_us)` and install it.
- `Site administration` > `Language` > `Language Settings`
    - `Default language`: set to `English - United States (en_us)`
    - Click `Save Changes`
- `Site administration` > `Front page`
    - `Full site name`: set to whatever you want
    - `Short name for site`: set to an abbreviated version of the full site name
    - Click `Save Changes`
- `Site administration` > `Security` > `Site security settings`
    - `Force users to log in`: check the box
    - `Password policy`: uncheck the box
    - Click `Save Changes`
- `Site administration` > `Plugins` > `Authentication` > `Manage authentication`
    - Disable `Email-based self-registration`
    - Disable `MNet authentication`
    - `Allow accounts with same email`: check the box
    - `Guest login button`: select `Hide`
    - `Alternate login URL`: enter the root Moodle url followed by '/local/mxschool/login.php' — for example, if your root Moodle url is http://localhost:8888/moodle34, then enter http://localhost:8888/moodle34/local/mxschool/login.php
    - Click `Save Changes`
- `Site administration` > `Appearance` > `AJAX and Javascript`
    - `Cache Javascript`: uncheck the box
    - Click `Save Changes`
- `Site administration` > `Appearance` > `Themes` > `Boost`
    - `Brand color`: enter '#CF003D'
    - Click `Save Changes`
- `Site administration` > `Development` > `Debugging`
    - `Debug messages`: select `DEVELOPER: extra Moodle debug messages for developers`
    - Click `Save Changes`

Now log out and be sure that you are still able to log in successfully with the new login page and that your theme color has changed to Middlesex's red.

##### Installing Testing User Data
The last step for your installation is to install users to work with. This will require that you fill data in 5 different tables. You will need to acquire test data CSVs before you proceed.

The first table contains the basic user data, such as the user's First name, Last name, username and password. Because passwords need to be hashed, this step has to happen through the Site Administration interface. Navigate to `Site administration` > `Users` > `Accounts` > `Upload users` and choose the CSV from your computer. Then select the following options:
- `Settings` > `New user password`: select `Field required in file`
- `Settings` > `Prevent email address duplicates`: select `No`
- `Default Values` > `City/town`: clear this field if there is any default
- `Default Values` > `Preferred language`: make sure this is set to `English - United States (en_us)`

The other data which you need to enter, can be imported directly through Sequel Pro. To connect to your local server, initiate a socket connection from the Sequel Pro connection interface with the following information:

        Username: moodle
        Password: moodle
        Database: moodle##
        Socket: Applications/MAMP/tmp/mysql/mysql.sock

Where ## is replaced with the version of Moodle you are running. For example, for Moodle 3.4, you would use:

        Database: moodle34

While you are here, I would suggest that you add a name and color then save this configuration as a favorite by selecting the `Add to Favorites` option before you connect.

Now that you are connected, feel free to look around and see all of the tables which Moodle has by default. One table of interest is the user table (`mdl_user`). If you look at this table you should see all of the users which you just added. While you are here go ahead and set the country for the Administrator to 'US' and the language to 'en_us' (the installation defaults to your being Australian). If you search for 'local,' you will see all of the tables for our plugins, some of which automatically pre-install their data, but a few of which you will have to enter data for.

Importing data within Sequel Pro is very easy. While viewing the table you want to import to, select `File` > `Import...` and choose the CSV file. Then match the fields in the CSV with the fields in the data, click `Import`, and you are all set.

While initializing your database, you will need to enter records into the `mdl_local_mxschool_faculty`, `mdl_local_mxschool_dorm`, `mdl_local_mxschool_student`, and `mdl_local_mxschool_permissions` tables.
###### WARNING: When importing this data, be sure that the `userid` and `hohid` fields correctly reference the `id`s assigned to your users when you uploaded them to the `mdl_user` table.

___

### Setting Up Atom
