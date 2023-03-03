# Web Technologies
Moodle is a web application which is built on a variety of technologies. You do not need to have these technologies mastered but the more familiar you are with them and the more open to working with them you are, the better.

## HTML
All webpages are rendered in HTML _(HyperText Markup Language)_. You will most likely not write anything in HTML directly while working on Moodle plugins, but if you want to understand how webpages work, this is the best place to start. You can learn HTML at [Codecademy](https://www.codecademy.com/learn/learn-html) or [W3Schools](https://www.w3schools.com/html).

## PHP
PHP _(Hypertext Preprocessor)_ is the meat-and-potatos of Moodle. PHP is a server-side scripting language and a powerful tool for making dynamic and interactive web pages. You can learn PHP at [Codecademy](https://www.codecademy.com/learn/learn-php) or [W3Schools](https://www.w3schools.com/php)

## MySQL
SQL _(Structured Query Language)_ runs the databases that store and manage all of Moodle's data. Moodle provides a [Data Manipulation API](https://docs.moodle.org/dev/Data_manipulation_API) that allows you to execute simple queries without writing out the SQL. However, this API doesn't handle more complex situations, so you will still need to be familiar with how to construct an SQL query. Like HTML and PHP, SQL is a fundemental web technology and you can learn more at [Codecademy](https://www.codecademy.com/learn/learn-sql) or [W3Schools](https://www.w3schools.com/sql).

## JavaScript
JavaScript allows webpages to be interactive and change without the browser needing to reload the page. This greatly improves user experience on any web application of reasonable complexity. JavaScript is used on the server-side of Moodle Plugins, but it is used somewhat infrequently to make HTML forms and other elements interactive. As such, it is not essential that every individual working on the project have a deep understanding of the language, but you should at least be familiar with it. JavaScript's syntax is similar to that of Java, but it has many important syntactical and semantic differences. Codecademy's [Introduction to JavaScript](https://www.codecademy.com/learn/introduction-to-javascript) should be more than sufficient to get you up and running. Moodle relies primarily on the JS Library jQuery for all DOM interaction. Codecademy's [Introduction to jQuery](https://www.codecademy.com/learn/learn-jquery) and jQuery's [API documentation](https://api.jquery.com) are both good resources. Moodle plugins define all JavaScript in AMD _(Asynchronous Module Definition)_ modules. See [this MoodleDocs page](https://moodledev.io/docs/guides/javascript/modules) for more information.

## Mustache
Rather than writing HTML directly or building up the page within the PHP itself, Moodle uses the templating language Mustache to define HTML fragments which are ultimately combined and served to the user. Because these templates are inherently reusable, you won't often need to write new ones. However, if you do need to write a Mustache template, the [MoodleDocs page Templates page](https://moodledev.io/docs/guides/templates), the [Mustache Manual](https://mustache.github.io/mustache.5.html), and the documentation for the PHP implementation in [this GitHub repo](https://github.com/bobthecow/mustache.php) are all good resources.

## CSS
CSS _(Cascading Style Sheets)_ is what determines how a webpage is ultimately displayed in the client's browser. Moodle is designed to allow for theme plugins which can change how the site looks dramatically. As such, we don't have to worry much about CSS when writing other types of plugins. There is a small amount of CSS in the repository, but it is not something that will need to change as the project expands. Moodle's current default theme, Boost, is built on Twitter's [Bootstrap API](https://getbootstrap.com), so if you want to use any Bootstrap elements, all you need to do is add the appropriate class in your mustache file or in the PHP. [W3Schools](https://www.w3schools.com/cssref) has a nice CSS reference, if you need it.

## Markdown
[Markdown](https://www.markdownguide.org) is a simplified way to add formatting elements to otherwise plain text. It's fast, lightweight, and surprisingly younger than HTML and CSS. All of our documentation, including the user Wiki planned for future development, is hosted by GitHub and written in plain text with markdown.

## Moodle.org
In addition to these technologies, the [Moodle.org](https://moodle.org) website is an excellent resource for the many API layers and fundementals of Moodle. Be sure to access their [Documentation](https://docs.moodle.org), [Development site](https://moodledev.io), and [Community Forums](https://moodle.org/course/view.php?id=5).

-----
*Continue to our [Developer Tools](/docs/TOOLS.md) page.*  </br>
*Return to our [Getting Started](/docs/GETTING_STARTED.md) index.*
