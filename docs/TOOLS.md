# Your Developer Environment
Although it is possible to edit and work within GitHub, itself, there are several tools that will help you on this project and coding, in general. The specific apps described here were chosen because they are open-source, free, and readily available on most platforms. If you have a favorite tool that serves the same purpose, that's cool, too.

## A Git Client
Our repository is hosted on GitHub. Since its purchase by Microsoft in 2018, the GitHub community has seen some very positive growth. We have consolidated a collection of technologies into GitHub and feel confident this is the best direction for this project going forward.
1. Go to [GitHub](https://github.com) and sign up for an account. When choosing an account name, please remember that your GitHub profile will also be part of the portfolio you share with prospective colleges and the contributions you make on GitHub will be publicly documented for years after you leave Middlesex. Please fill out your profile to your comfort level. We strongly recommend you [turn on two-factor authentication](https://docs.github.com/en/github/authenticating-to-github/securing-your-account-with-two-factor-authentication-2fa).
2. Download a Git client. We recommend [GitHub Desktop](https://desktop.github.com) because it is actively being developed by the GitHub team and it is open source. You may also want to install the Git command line, if you feel comfortable working with it.

## A Text Editor
We recommended the [Atom](https://atom.io) text editor because it is actively being developed by the GitHub team, it's open source, and there are many packages available to hack or customize it to your liking. Atom comes with a preinstalled Git package that brings you the GitHub Client functionality.

To help you stay in line with our [Coding Style Guidelines](CODING_STYLE.md):
- Set `Preferred Line Length` to `132`
- Set `Tab Length` to `4 spaces`

You will often want to use bash scripts to manipulate your development environment. In Atom, we suggest installing the terminal package `platformio-ide-terminal`.

Moodle has some specific and somewhat non-standard style guidelines that you will need to follow and your code can be automatically checked against these guidelines with PHP_CodeSniffer. In Atom's package installation menu, search for `linter-phpcs` and install it along with any dependancies.

Once installed, there are a couple of settings which you will need to change in order for the linter to function properly:
- Set `Executable Path` to the root of your Moodle installation followed by `/local/codechecker/pear/PHP/scripts/phpcs` — for example if you are running Moodle 3.11 use `/Applications/MAMP/htdocs/moodle311/local/codechecker/pear/PHP/scripts/phpcs`
- Set `Code Standard or Config File` to the root of your Moodle installation followed by `/local/codechecker/moodle/ruleset.xml` — for example if you are running Moodle 3.11 use `/Applications/MAMP/htdocs/moodle311/local/codechecker/moodle/ruleset.xml`
- Set `Tab Width` to `4`

###### NOTE: The linter sometimes seems to get overwhelmed with certain files and starts reporting indentation issues everywhere. The codechecker page on your local site (`Site administration` > `Development` > `Code checker`) can check all of your files simultaneously and does not have this issue.

Atom's [Teletype](https://teletype.atom.io) plugin adds live collaboration.

## A Database GUI
Our Moodle Server stores all of its data in a SQL database. Moodle v4 will require SQL 8.x and we are developing now using this latest version. The school year begins with a number of bulk data entry tasks using CSV files that combine data from our Student Information System, Sr Systems, and our Google Workplace account. We use and highly recommend [Sequel Ace](https://github.com/Sequel-Ace/Sequel-Ace) for this task as it is open source, it is supported by a large, world-wide community, and it is free. It is only available for macOS; If anyone has a good Windows alternative, please let us know.

## Apache2, MySQL, PHP
To run a local server on your own computer, you will need this trifecta of web technologies. Specifically, we are running Apache v2, MySQL v8, and PHP v8. The upcoming release of Moodle v4 will require these latest versions and we are already developing with that in mind. You can install these packages separately, but for ease and convenience, we recommend using [MAMP](https://www.mamp.info); a prebuilt package with all three together.

If you using macOS, Moodle provides a package called Moodle4Mac which will set up a MAMP installation with the desired Moodle release automatically. You can download the appropriate package [here](https://download.moodle.org/macosx).

-----
*Continue to [Prepare Your Local Server](/docs/SERVER.md) page.* </br>
*Return to our [Getting Started](/docs/GETTING_STARTED.md) index.*
