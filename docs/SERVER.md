# Setting Up Your Own Development Server
With all the tools installed, you are ready to configure Moodle. Open your browser window and navigate to Moodle on your localhost. The config script will start automatically and walk you through accepting the EULA and getting started.

*With Moodle4Mac, this really is just a couple of clicks. More advanced setups may require more individualized assistance which is beyond the scope of this document. Please ask and we can help you through this process.*

## Site Configuration
Once the config script finishes, you will be on the Moodle Dashboard and we recommend the following settings for your Moodle Server. To get to these settings, select `Site administration` from the NavBar at the top of the Dashboard. Moodle has a lot of settings, so follow these steps carefully as you navigate through the settings tree to find the appropriate ones.
- `Site administration` > `General` > `Location` > `Location Settings`
    - `Default timezone`: set to `America/New York`
    - `Force timezone`: set to `America/New York`
    - `Default country`: set to `United States`
    - Click `Save Changes`
- `Site administration` > `Language` > `Language Packs`
    - Find `English (United States) (en_us)` and install it.
- `Site administration` > `Language` > `Language Settings`
    - `Default language`: set to `English (United States) (en_us)` - This will be the default if you set this during the initial config.
    - `Display language menu`: set to `No` by unchecking the box.
    - `Cache language menu`: set to `No`.
    - `Cache all language strings`: set to `No`.
    - Click `Save Changes`
- `Site administration` > `Security` > `Site security settings`
    - `Force users to log in`: set to `Yes` by checking the box.
    - `Cron execution via command line only`: optionally set this to `No` if you are working from your local machine.
    - `Password policy`: set to `No`.
    - Click `Save Changes`
- `Site administration` > `Users` > `Accounts` > `Browse list of users`
    - With the Password policy disabled, you can, optionally, change the Admin password to something simple.
- `Site administration` > `Plugins` > `Authentication` > `Manage authentication`
    - Enable `Allow accounts with same email`
    - `Guest login button`: select `Hide`
    - Click `Save Changes`
- `Site administration` > `Appearance` > `AJAX and Javascript`
    - `Cache Javascript`: uncheck the box
    - Click `Save Changes`
- `Site administration` > `Server` > `System paths`
    - `Path to PHP CLI`: enter `/usr/bin/php`
    - Click `Save Changes`
- `Site administration` > `Server` > `Support contact`
    - `Supoort Name`: *enter whatever you want — this value will appear in the from field of all emails sent from your server*
    - `Support Email`: `admin@localhost.local` *(or anything else that looks like an email)*
    - Click `Save Changes`
- `Site administration` > `Server` > `Email` > `Outgoing mail configuration`
    - *Optionally, see Chuck for these settings. You can use SendGrid or your own Google account*
- `Site administration` > `Development` > `Debugging`
    - `Debug messages`: select `DEVELOPER: extra Moodle debug messages for developers`.
    - `Display debug messages`: select `Yes`.
    - `Performance info`: select `Yes`.
    - `Show origin of languages strings`: select `Yes`.
    - `Show origin of SQL calls`: choose to show at least 2 lines of the stack trace.
    - `Show validator links`: select `Yes`.
    - `Show page information`: select `Yes`.
    - Click `Save Changes`

Now log out and log back in. You should land on your dashboard when you log in. The mxMoodle development team's server uses the default blue colour as a strong visual distinction from the live Production server.

## Moodle Development Plugins
From the Moodle Plugin Library, you should download these plugins:
- [Code-Checker](https://moodle.org/plugins/local_codechecker) is a PHP Code Sniffer tool to check that code follows the [Moodle coding style](https://docs.moodle.org/dev/Coding_style).
- [Moodle PHPdoc check](https://moodle.org/plugins/local_moodlecheck) Tool for Moodle developers that allows one to check phpdocs in the code for compliance with [Moodle Coding Style](https://docs.moodle.org/dev/Coding_style).

## Adding mxMoodle Plugins
With your server configured and running, you are ready to add our custom plugins. Clone our mxMoodle repository to a location on your computer. This location should be somewhere within your user directory and _not_ within the MAMP installation. Also, we advise against cloning it to a folder that synchronizes with Google Drive or other cloud storage to avoid extraneous files being added.

Now copy our plugins into your installation. We will automate this process later, but for the first time, it is a good idea to take a look at how Moodle's file structure works. Navigate to your web folder \(/Applications/MAMP/htdocs if you are using MAMP\). Here you should find a couple of directories. Select the one which corresponds to the version of Moodle you are running. In this directory, you will find the Moodle core and the preinstalled plugins (there are close to 400).

Our code goes in the /local and /blocks directories. Please _copy_ (hold option while dragging in Finder) everything from MXMoodle/local to your installation's /local directory. Do the same from MXMoodle/blocks to /blocks.
**When you are doing this be sure to copy the _contents_ of each directory not the entire directory itself, because you don't want to overwrite any of the existing plugins in your Moodle installation.**

## Installing Sample User Data
With your Moodle installation up and running, and your plugins installed, you are ready to add data. Our current shared folder has a set of CSV files that mirror the data on the Moodle Development Server and is a good place to start.

Once you have copied the plugin files, if you navigate to the dashboard as an administrator, you will be redirected to the installation page. All you need to do is scroll to the bottom of the page and click `Upgrade Moodle database now`. Once the plugins install, click `continue`, and you will be prompted to enter a number of settings for our plugins. For most of these, you can just accept the default values, but you should change the `Redirect email` to your own email address for testing purposes.

The first table contains the basic user data, such as the user's First name, Last name, username and password. This step has to happen through the Site Administration interface. Navigate to `Site administration` > `Users` > `Accounts` > `Upload users` and choose the CSV from your computer. Then select the following options:
- `Settings` > `New user password`: select `Field required in file`
- `Settings` > `Prevent email address duplicates`: select `No`
- `Default Values` > `City/town`: clear this field if there is any default
- `Default Values` > `Preferred language`: make sure this is set to `English - United States (en_us)`
Now click `Upload Users`, and you are ready to move to the next table.

The rest of the testing data is imported directly through your Sequel client.
Enter records into the `mdl_local_mxschool_faculty`, `mdl_local_mxschool_dorm`, `mdl_local_mxschool_student`, and `mdl_local_mxschool_permissions` tables.

## Environment Scripts
There are a number of file manipulation operations which are common enough that you will save a lot of time by having a script that will run them for you. My suggestion is to include following lines in your bash profile (generally, `~/.bash_profile`). You first need to export environment variables `MOODLE_SERVER_ROOT` and `MOODLE_PROJECT_ROOT` which should hold the path to your Moodle installation and working copy respectively. For example, if you are running Moodle 3.11 add this line for your `MOODLE_SERVER_ROOT`:

```bash
export MOODLE_SERVER_ROOT="/Applications/MAMP/htdocs/moodle311"
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
*Return to our [Getting Started](/docs/GETTING_STARTED.md) index.*
