# Getting Started with Moodle Programming

This work is a combination of talents and technologies - Coding, UX, Documenting, QA, etc. No matter how you contribute, the more open you are to working with the many pieces that make up this complex project, the greater your impact will be.

1. [Web Technologies](#prereq)
2. [Your Development Environment](#dev)

<a name="prereq"></a>
## Web Technologies
Moodle is a web application which is built on a variety of technologies. You do not need to have these technologies mastered but the more familiar you are with them and the more open to working with them you are, the better.

#### HTML
All webpages are rendered in HTML _(HyperText Markup Language)_. You will most likely not write anything in HTML directly while working on Moodle plugins, but if you want to understand how webpages work, this is the best place to start. You can learn HTML at [Codecademy](https://www.codecademy.com/learn/learn-html) or [W3Schools](https://www.w3schools.com/html).

#### PHP
PHP _(Hypertext Preprocessor)_ is the meat-and-potatos of Moodle. PHP is a server scripting language and a powerful tool for making dynamic and interactive web pages. You can learn PHP at [Codecademy](https://www.codecademy.com/learn/learn-php) or [W3Schools](https://www.w3schools.com/php)

#### MySQL
SQL _(Structured Query Language)_ runs the databases that store and manage all of Moodle's data. Moodle provides a [Data Manipulation API](https://docs.moodle.org/dev/Data_manipulation_API) that allows you to execute simple queries without writing out the SQL. However, this API doesn't handle more complex situations, so you will still need to be familiar with how to construct an SQL query. Like HTML and PHP, SQL is a fundemental web technology and you can learn more at [Codecademy](https://www.codecademy.com/learn/learn-sql) or [W3Schools](https://www.w3schools.com/sql).

#### JavaScript
JavaScript allows webpages to be interactive and change without the browser needing to reload the page. This greatly improves user experience on any web application of reasonable complexity. JavaScript is used on the server-side of Moodle Plugins, but it is used somewhat frequently to make HTML forms and other elements interactive. As such, it is not essential that every individual working on the project have a deep understanding of the language, but you should at least be familiar with it. JavaScript's syntax is similar to that of Java, but it has many important syntactical and semantic differences. Codecademy's [Introduction to JavaScript](https://www.codecademy.com/learn/introduction-to-javascript) should be more than sufficient to get you up and running. Moodle relies primarily on the JS Library jQuery for all DOM interaction. Codecademy's [Introduction to jQuery](https://www.codecademy.com/learn/learn-jquery) and jQuery's [API documentation](https://api.jquery.com) are both good resources. Moodle plugins define all JavaScript in AMD _(Asynchronous Module Definition)_ modules. See [this MoodleDocs page](https://docs.moodle.org/dev/Javascript_Modules) for more information.

#### Mustache
Rather than writing HTML directly or building up the page within the PHP itself, Moodle uses the templating language Mustache to define HTML fragments which are ultimately combined and served to the user. Because these templates are inherently reusable, you won't often need to write new ones. However, if you do need to write a Mustache template, the [MoodleDocs page Templates page](https://docs.moodle.org/dev/Templates), the [Mustache Manual](https://mustache.github.io/mustache.5.html), and the documentation for the PHP implementation in [this GitHub repo](https://github.com/bobthecow/mustache.php) are all good resources.

#### CSS
CSS _(Cascading Style Sheets)_ is what determines how a webpage is ultimately displayed in the client's browser. Moodle is designed to allow for theme plugins which can change how the site looks dramatically. As such, we don't have to worry much about CSS when writing other types of plugins. There is a small amount of CSS in the repository, but it is not something that will need to change as the project expands. Moodle's current default theme, Boost, is built on Twitter's [Bootstrap API](https://getbootstrap.com), so if you want to use any Bootstrap elements, all you need to do is add the appropriate class in your mustache file or in the PHP.

#### Moodle.org
In addition to these technologies, the [Moodle.org](https://moodle.org) website is an excellent resource for the many API layers and fundementals of Moodle. Be sure to access their [Documentation](https://docs.moodle.org), [Development site](https://docs.moodle.org/dev), and [Community Forums](https://moodle.org/course/view.php?id=5).








<a name="dev"></a>
## Your Development Environment
You will need to install and set up a variety of software packages on your computer before you can begin developing Moodle plugins and running a server locally.

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
Once you have installed Moodle's MAMP Package, you are ready to set up your local development server. Because the server is pre-pacakged for you, you won't have to do very much set up. To start your server, all you need to do is open the MAMP app and press the start servers button if they do not start automatically. Now select the `My Website` tab from the top of the landing page, and you will have arrived at your Moodle installation (this may take some time to load the first time you do it). Once you reach the homepage, select log in at the bottom and use the default credentials:

    username: admin
    password: 12345

##### Adding Middlesex Plugins
The first thing you will need to do once you have your server running is to add the existing version of our custom plugins to your server before you configure certain settings to mimic our production server as well as some specifically for development. To do this, you will first need to clone the repository to a location on your computer. This location should be somewhere within your user directory and _not_ within the MAMP installation.

Once you have cloned the repository, you will need to copy our plugins into your installation. After you have set up your IDE, this will be an automated process, but for this first time, it is a good idea to take a look at how Moodle's file structure works. In Finder navigate to /Applications/MAMP/htdocs. Here you should find a couple of directories. Select the one which corresponds to the version of Moodle you are running. In this directory, you will find all of Moodle core and the preinstalled plugins (there are close to 400). There is something on the order of 10,000 files in the default installation of Moodle. We will be contributing to the /local and /blocks directories. You now want to _copy_ (hold option while dragging in Finder) everything from MXMoodle/local to your installation's /local directory. Do the same from MXMoodle/blocks to /blocks. **When you are doing this be sure to copy the _contents_ of each directory not the entire directory itself, because you don't want to overwrite any of the existing plugins in your Moodle installation.**

While you are adding our plugins, you should also take a moment to add Moodle's `code checker` plugin which will be useful during development. All you need to do is download the plugin [here](https://github.com/moodlehq/moodle-local_codechecker/zipball/master) then move the entire directory into the same /local directory of your server. Lastly, you need to rename the directory to codechecker.

Once you have copied the plugin files if you navigate to the dashboard as an administrator, you will be redirected to the installation page. All you need to do is scroll to the bottom of the page and click `Upgrade Moodle database now`. Once the plugins install, click `continue`, and you will be prompted to enter a number of settings for our plugins. For most of these, you can just accept the default values, but you should change the `Redirect email` to your own email address for testing purposes. Now click `Save Changes`, and you are ready to move to the next step.

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
- `Site administration` > `Front page` > `Front page settings`
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
    - `Alternate login URL`: enter the root Moodle url followed by `/local/mxschool/login.php` — for example, if your root Moodle url is `http://localhost:8888/moodle37`, then enter `http://localhost:8888/moodle37/local/mxschool/login.php`
    - Click `Save Changes`
- `Site administration` > `Appearance` > `Navigation`
    - `Default home page for users`: select `Dashboard`
    - Click `Save Changes`
- `Site administration` > `Appearance` > `AJAX and Javascript`
    - `Cache Javascript`: uncheck the box
    - Click `Save Changes`
- `Site administration` > `Appearance` > `Themes` > `Boost`
    - `Brand color`: enter `#CF003D`
    - Click `Save Changes`
- `Site administration` > `Server` > `System paths`
    - `Path to PHP CLI`: enter `/usr/bin/php`
    - Click `Save Changes`
- `Site administration` > `Server` > `Support contact`
    - `Supoort Name`: enter whatever you want — this value will appear in the from field of all mxschool emails sent from your server
    - `Support Email`: enter `admin@localhost.local` (or anything else that looks like an email)
    - Click `Save Changes`
- `Site administration` > `Server` > `Email` > `Outgoing mail configuration`
    - `SMTP hosts`: enter `smtp.sendgrid.net:587`
    - `SMTP security`: select `TLS`
    - `SMTP username`: enter `mx-moomail`
    - `SMTP password`: _ask someone for our password and enter it here_
    - `No-reply address`: enter `noreply@localhost.local` (or anything else that looks like an email) — this address will be the "sender" of all mxschool emails sent from your server
    - `Email via information`: select `Never`
    - Click `Save Changes`
- `Site administration` > `Development` > `Debugging`
    - `Debug messages`: select `DEVELOPER: extra Moodle debug messages for developers`
    - Click `Save Changes`

Now log out and be sure that you are still able to log in successfully with the new login page. If your server fails to load the login page, you probably entered the url incorrectly in the settings. You can manually enter the correct version in your browser's search bar to regain access to the site. You should land on your dashboard when you log in, and if you updated the appearance settings successfully, your theme color should be Middlesex's red.

##### Installing Testing User Data
The last step for your installation is to install users to work with. This will require that you fill data in 5 different tables. You will need to acquire test data CSVs before you proceed.

The first table contains the basic user data, such as the user's First name, Last name, username and password. Because passwords need to be hashed, this step has to happen through the Site Administration interface. Navigate to `Site administration` > `Users` > `Accounts` > `Upload users` and choose the CSV from your computer. Then select the following options:
- `Settings` > `New user password`: select `Field required in file`
- `Settings` > `Prevent email address duplicates`: select `No`
- `Default Values` > `City/town`: clear this field if there is any default
- `Default Values` > `Preferred language`: make sure this is set to `English - United States (en_us)`
Now click `Upload Users`, and you are ready to move to the next table.

The other data which you need to enter, can be imported directly through Sequel Pro. To connect to your local server, initiate a socket connection from the Sequel Pro connection interface with the following information:

    Username: moodle
    Password: moodle
    Database: moodle##
    Socket: Applications/MAMP/tmp/mysql/mysql.sock

Where ## is replaced with the version of Moodle you are running. For example, for Moodle 3.7, you would use:

    Database: moodle37

While you are here, I would suggest that you add a name and color then save this configuration as a favorite by selecting the `Add to Favorites` option before you connect.

Now that you are connected, feel free to look around and see all of the tables which Moodle has by default. One table of interest is the user table (`mdl_user`). If you look at this table you should see all of the users which you just added. While you are here go ahead and set the country for the Administrator to `US` and the language to `en_us` (the installation defaults to your being Australian). If you search for 'local', you will see all of the tables for our plugins, some of which automatically pre-install their data, but a few of which you will have to enter data for.

Importing data within Sequel Pro is very easy. While viewing the table you want to import to, select `File` > `Import...` and choose the CSV file. Then match the fields in the CSV with the fields in the data, click `Import`, and you are all set.

While initializing your database, you will need to enter records into the `mdl_local_mxschool_faculty`, `mdl_local_mxschool_dorm`, `mdl_local_mxschool_student`, and `mdl_local_mxschool_permissions` tables.
###### WARNING: When importing this data, be sure that the `userid` and `hohid` fields correctly reference the `id`s assigned to your users when you uploaded them to the `mdl_user` table.

___

### Setting Up Atom
The final part of preparing your development environment is setting up your text editor. This will basically come down to installing a few Atom packages and adding some useful scripts to your bash profile. If you have decided to use something other than Atom, the information about packages won't be useful, but you should still find a way to run the CodeSniffer. The environment scripts, on the other hand, will still be useful.

##### Editor Settings
Atom is highly configurable, and there are a few editor settings which you should change to help you stay in line with the [Style Guidelines](CODING_STYLE.md).
- Set `Preferred Line Length` to `132`
- Set `Tab Length` to `4`

##### Installing a Linter Package
Atom is generally a great IDE, but by default, it won't tell you about syntactical or stylistic errors as you are writing. Moodle has some specific and somewhat non-standard style guidelines (See our [Style Guidelines](CODING_STYLE.md) documentation) that you will need to follow. Luckily, they provide a way to automatically check your code against many of the guidelines with PHP_CodeSniffer. In Atom's package installation menu, search for `linter-phpcs` and install it. A pop-up will appear asking you to install the necessary dependencies if you do not already have them. These dependencies also have their own dependencies, so keep selecting `Yes` whenever you are asked if you want to install another package. In total you will need to install 5 packages. If you aren't prompted to install all of the dependencies, restarting Atom should trigger any additional prompts.

Once the package is installed, there are a couple of settings which you will need to change in order for the linter to function properly:
- Set `Executable Path` to the root of your Moodle installation followed by `/local/codechecker/pear/PHP/scripts/phpcs` — for example if you are running Moodle 3.7 use `/Applications/MAMP/htdocs/moodle37/local/codechecker/pear/PHP/scripts/phpcs`
- Set `Code Standard or Config File` to the root of your Moodle installation followed by `/local/codechecker/moodle/ruleset.xml` — for example if you are running Moodle 3.7 use `/Applications/MAMP/htdocs/moodle37/local/codechecker/moodle/ruleset.xml`
- Set `Tab Width` to `4`.

###### NOTE: The linter sometimes seems to get overwhelmed with certain files and starts reporting indentation issues everywhere. The codechecker page on your local site (`Site administration` > `Development` > `Code checker`) can check all of your files simultaneously and does not have this issue.

##### Installing a Terminal Package
Because you will often want to use bash scripts to manipulate your development environment, it is very useful to have a terminal embedded into your IDE. I would suggest installing the Atom package `platformio-ide-terminal`.

##### Environment Scripts
There are a number of file manipulation operations which are common enough that you will save a lot of time by having a script that will them for you. My suggestion is to include following lines in `~/.bash_profile`. You first need to export environment variables `MOODLE_SERVER_ROOT` and `MOODLE_PROJECT_ROOT` which should hold the path to your Moodle installation and working copy respectively. For example, if you are running Moodle 3.7 add this line for your `MOODLE_SERVER_ROOT`:

```bash
export MOODLE_SERVER_ROOT="/Applications/MAMP/htdocs/moodle37"
```

Then add the following lines to be able to move files back and forth as well as some other commonly used functionality which is explained below.

```bash
alias moodlePullDBSchema="(cd $MOODLE_SERVER_ROOT/local; rsync -R */db/install.xml $MOODLE_PROJECT_ROOT/local)"
alias moodlePushPlugins="rsync -r --del $MOODLE_PROJECT_ROOT/local/* $MOODLE_SERVER_ROOT/local"
alias moodlePushBlocks="rsync -r --del $MOODLE_PROJECT_ROOT/blocks/* $MOODLE_SERVER_ROOT/blocks"
alias moodleBuild="(cd $MOODLE_SERVER_ROOT/; grunt; cd $MOODLE_SERVER_ROOT/local; rsync -R */amd/build/* $MOODLE_PROJECT_ROOT/local)"
alias moodleErrorLog="open /Applications/MAMP/logs/php_error.log"
```

If you have `ssh` access to the test server and have set up an `ssh` profile as `moodledev`, you can also add these:

```bash
alias moodleTestServerPushPlugins="rsync -r --del --rsh=ssh $MOODLE_PROJECT_ROOT/local/* moodledev:/var/www/html/moodle/local"
alias moodleTestServerPushBlocks="rsync -r --del --rsh=ssh $MOODLE_PROJECT_ROOT/blocks/* moodledev:/var/www/html/moodle/blocks"
```

###### NOTE: You will need to restart any shells you are using for your changes to take effect.

##### Build Script
In order to minify your AMD modules, you will need to be able to use Moodle's build script. To do this you will need to install the `node` package `grunt`. Unfortunately Moodle currently requires a `node` version `>=8.9.0 <9.0.0`. To test your current version you can run the following command:

```bash
brew -v
```

If you don't have an appropriate version, you will need to install it. If you do, you can skip the next two steps. You will need to have `Homebrew` installed on your computer for this installation. If you don't already have `brew`, you can install it with the following command:

```bash
/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
```

Then press `Return` to allow `brew` to be installed. Next you need to install the correct version of `node` and `npm` with the following commands:

```bash
brew install node@8
brew link --force node@8
```

Now that you have a working version of `node`, you need to install Moodle's build tool which is called `grunt`. You will need to install the module globally so that it is on your `$PATH`. You will also need to add to your Moodle installation all of the `node` modules which Moodle uses. You can do both of these with the following commands:

```bash
npm install -g grunt-cli
cd $MOODLE_SERVER_ROOT
npm install
```

##### Error Log
While you are testing the code you write on your local server, you will undoubtedly run into PHP errors. It is advisable to have the error log open whenever you are running your server because you will see not only more information about fatal errors but also warnings about the code you are writing that you might not catch otherwise. Assuming you have already added it to your profile, all you have to do is run the following command in any shell:

```bash
moodleErrorLog
```

##### Global Git Ignore File
While not specific to this project, if you are doing any git-based development on Mac OS, it is important that you have a global gitignore file set up to prevent .DS_Store files from ending up in your version control. If you don't have a global gitignore, run the following commands to create one:

```bash
echo -e "# Mac OS\n.DS_Store" > ~/.gitignore_global
git config --global core.excludesfile ~/.gitignore_global
```
