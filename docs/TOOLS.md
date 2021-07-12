# Your Development Environment

Although it is possible to edit and work within GitHub, itself, there are several tools that will help you on this project and coding, in general. The specific apps described here were chosen because they are open-source, free, and readily available on most platforms. If you have a favorite tool that serves the same purpose, that's cool, too.

## A Git Client
Our repository is hosted on GitHub. Since it's purchase by Microsoft in 2018, the GitHub community has seen some very positive growth. We have consolidated a collection of technologies into GitHub and feel confident this is the best direction for this project going forward.
1. Go to [GitHub](https://github.com) and sign up for an account. When choosing an account name, please remember that your GitHub profile will also be part of the portfolio you share with prospective colleges and the contributions you make on GitHub will be publicly documented for years after you leave Middlesex. Please fill out your profile to your comfort level. We strongly recommend you turn on two-factor authentication.
2. Download a Git client such as [GitHub Desktop](https://desktop.github.com). You may also install the Git command line, if you feel comfortable working with it.

## A Text Editor
We recommended the [Atom](https://atom.io) text editor because it is actively being developed by the GitHub team, it's open source, and there are many packages available to hack or customize it to your liking. Atom comes with a preinstalled Git package that brings you the GitHub Client functionality.

The [Teletype](https://teletype.atom.io) adds live collaboration.
See [Setting Up Atom](#setting-up-atom) below for more on what packages to install.

**Instructions for setting up additional ATOM packages**


## A Database GUI
Our Moodle Server stores all of its data in a SQL database. Moodle v4 will require SQL 8.x and we are developing now using this latest version. Because of limitations in our work, it is often necessary to load and update our mxMoodle data in bulk using CSV files.
[Sequel Ace](https://github.com/Sequel-Ace/Sequel-Ace) -- macOS only.

## Apache2, MySQL, PHP
To run a local server on your own computer, you will need this trifecta of web technologies. Specifically, we are running Apache v2, MySQL v8, and PHP v8. The upcoming release of Moodle will require these latest versions and we are already developing with that in mind.

You can instal these packages separately, but for ease and convenience, we recommend using [MAMP](https://www.mamp.info); a prebuilt package with all three together, available for Windows and macOS.

If you using macOS, Moodle provides a package called Moodle4Mac which will set up a MAMP installation with the desired Moodle release automatically. You can download the appropriate package [here](https://download.moodle.org/macosx).

-----
#### *Continue to [Prepare Your Local Server](/docs/SERVER.md) page.*
#### *Return to our [Getting Started](/docs/GETTING_STARTED.md) index.*
