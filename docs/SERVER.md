# Setting Up Your Own Development Server
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

-----
#### *Return to our [Getting Started](/docs/GETTING_STARTED.md) index.*