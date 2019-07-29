# Getting Started with Moodle Programming

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

## Development Environment
You will need to set up a variety of software packages on your computer before you can begin developing Moodle plugins and running a server locally.

### Software
You will need to download and install all of the following applications before proceeding:

##### Text Editor
**Atom** is our recommended text editor because it has a number of packages that will make your life easier while developing for Moodle. You can download Atom [here](https://atom.io). See [Setting Up Atom](#setting-up-atom) below for more on what packages to install. You can also use another text editor if there is one which you prefer, but you will have to figure out how to set up the environment yourself, so you will likely be making things more difficult for yourself.

##### Git Client
This project uses **git** for version control. Specifically, our repository has remotes on both **GitHub** and **BitBucket**. As such, you will need a GitHub account in order to contribute to the project. If you don't have one, sign up on [GitHub.com](https://github.com/join). You will also need a git client on your computer. There are a number of good options out there including [Soucetree](https://www.sourcetreeapp.com), [GitHub Desktop](https://desktop.github.com), and the preinstalled [GitHub Package](https://github.atom.io) in Atom. You can even operate git from the command line directly if you are comfortable with that.

##### Trello
We use Trello to organize our development efforts and keep track of our progress. Trello is available in the browser, but we suggest that you install the desktop application from the [MacOS App Store](https://apps.apple.com/us/app/trello/id1278508951?mt=12). If you haven't used Trello before, you will need to create an account (just sign up with Google). Trello also has a mobile app.

##### MAMP
In order to test the code you are developing, you will need to have a server running locally on your computer. The software which we suggest for running a PHP and MySQL server is called [MAMP](https://www.mamp.info/en/). Conveniently, Moodle provides a package called Moodle4Mac which will set up a MAMP installation with the desired Moodle release automatically. You can download the appropriate package [here](https://download.moodle.org/macosx). It is very important that you are running the version of Moodle which we are currently developing for, so please ask someone if you do not know which version to download.

##### Sequel Pro
Sequel Pro is a great piece of software to view the structure and contents of your Moodle database. It is especially useful for isolating your database interactions while manually testing your code. You can download Sequel Pro [here](https://www.sequelpro.com).

_Note that all of the download links in this section are geared towards MacOS users, but these applications should all work on any platform._

### Setting Up Atom

### Installing Your Local Test Server
