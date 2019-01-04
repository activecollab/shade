Shade builds help portals from Markdown files. Write a couple of Markdown files, put them in folders and let the Shade build a website just like this one. If you are interested in learning why is Shade the way it is, check out <{page name="principles" book="shade"}>the Principles Page<{/page}> (it's an interesting read if you are working on a complex software project). Or dive right in…

<{sub}>Installing Shade<{/sub}>

Are you on a Unix type of an operating system? Mac or Linux? Than it's easy opening a terminal and doing this:

<{code highlight=shell}>curl -O https://www.activecollab.com/labs/shade/downloads/shade-latest.phar
chmod +x shade-latest.phar
sudo mv shade-latest.phar /usr/local/bin/shade<{/code}>

Now run:

<{code inline=false}>shade --version<{/code}>

to confirm that you got it installed correctly. If all is good, you should get the information about current version of the utility:

<{code inline=false}>Shade version 1.0.0<{/code}>

<{sub}>Create a Project<{/sub}>

All Shade projects have a simple folder structure for regular, single language projects:

<{code}>/build
/books
/releases
/videos
/whats_new
/temp
index.md
project.json<{/code}>

or a bit more complex structure for multilingual projects:

<{code}>/build
/en/books
/en/releases
/en/videos
/en/whats_new
/en/index.md
/sr/books
/sr/releases
/sr/videos
/sr/whats_new
/sr/index.md
/temp
project.md<{/code}>

To create a project, navigate to a folder where you want to set up a project structure and execute:

<{code inline=false highlight=shell}>shade project<{/code}>

This will create a single language project. Creating a multilingual project is not complicated either. Just run:

<{code inline=false highlight=shell}>shade project --default-locale=en<{/code}>

There are more options available for <{term}>project<{/term}> command and you can find them in <{page name="commands" section="project" book="shade"}>this article<{/page}>.

<{sub}>Writing Content<{/sub}>

There are five building blocks for every Shade project:

1. **Project** itself. You use it to define the home page, how build will work, which integrations will be put into pages, whether the is multilingual content etc. More info is available <{page name="new-project" book="shade"}>here<{/page}>,
2. **Books** are collections of **Pages** that cover a particular topic. They are great for writing user manuals. More info is available <{page name="new-book" book="shade"}>here<{/page}> and <{page name="new-book-page" book="shade"}>here<{/page}>,
3. **What's New Articles** let you create a small blog with news about your product. They should be written so all of your users are interested in reading them. More info is available <{page name="new-whats-new-article" book="shade"}>here<{/page}>,
4. **Release Notes** are used to document all the small changes that you make to your product. These are for your fans, users obsessed with details and power-users. More info is available <{page name="new-release" book="shade"}>here<{/page}>.
5. **Videos** let you instructional or promo videos to your documentation. More info is available <{page name="new-video" book="shade"}>here<{/page}>.

<{sub}>Example Project<{/sub}>

If you are like us and learn more by looking at example than reading the documentation, check out a Shade project that was used to build this website here:

[https://github.com/activecollab/shade/tree/master/docs](https://github.com/activecollab/shade/tree/master/docs)

Notice the interesting thing - documentation is in the same repository where the project code is. In our opinion, that is one of the main <{page name="principles" book="shade"}>benefits<{/page}> of using Shade.

<{sub}>Building<{/sub}>

Now that you have wrote your documentation, you can simply run:

<{code inline=false  highlight=shell}>shade build<{/code}>

to have the system build a static website from the help elements. <{page name="commands" book="shade" section="build"}>This page<{/page}> goes into details about <{code}>build<{/code}> command.

<{sub}>Advanced Topics<{/sub}>

* <a data-target="page" data-page-name="project-config" data-book-name="shade">Project Configuration</a>
* <a data-target="page" data-page-name="commands" data-book-name="shade">CLI Commands</a>

More information on how to extend Shade can be found in <a data-target="book" data-book-name="shade-dev">Shade Development</a> book:

* <a data-target="page" data-page-name="themes" data-book-name="shade-dev">Themes</a> (coming soon)
* <a data-target="page" data-page-name="plugins" data-book-name="shade-dev">Plugins</a> (coming soon)

<{sub}>Contributing<{/sub}>

GitHub project is here: [https://github.com/activecollab/shade](https://github.com/activecollab/shade). To contribute simply fork the repo, make the change and submit a pull request.
