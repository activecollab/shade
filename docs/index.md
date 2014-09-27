Shade builds help portals from Markdown files. Write a couple of Markdown files, put them in folders and let the Shade build a website just like this one. If you are interested in learning why is Shade the way it is, check out <{page name="principles" book="shade"}>the Principles Page<{/page}> (it's an interesting read if you are working on a complex software project). Or dive right in…

<{sub}>Installing Shade<{/sub}>

Are you on a Unix type of an operating system? Mac or Linux? Than it's easy opening a terminal and doing this:

<{code}>curl -O https://www.activecollab.com/labs/shade/downloads/shade-latest.phar
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
/en_US.UTF-8/books
/en_US.UTF-8/releases
/en_US.UTF-8/videos
/en_US.UTF-8/whats_new
/en_US.UTF-8/index.md
/sr_RS.UTF-8/books
/sr_RS.UTF-8/releases
/sr_RS.UTF-8/videos
/sr_RS.UTF-8/whats_new
/sr_RS.UTF-8/index.md
/temp
project.md<{/code}>

To create a project, navigate to a folder where you want to set up a project structure and execute:

<{code inline=false}>shade project<{/code}>

This will create a single language project. Creating a multilingual project is not complicated either. Just run:

<{code inline=false}>shade project --default-locale=en_US.UTF-8<{/code}>

There are more options available for <{term}>project<{/term}> command and you can find them in <{page name="project-command" book="shade"}>this article<{/page}>.

<{sub}>Writing Content<{/sub}>

There are five building blocks for every Shade project:

1. **Project** itself. You use it to define the home page, how build will work, which integrations will be put into pages, whether the is multilingual content etc,
2. **Books** are collections of **Pages** that cover a particular topic. They are great for writing user manuals,
3. **What's New Articles** let you create a small blog with news about your product. They should be written so all of your users are interested in reading them,
4. **Release Notes** are used to document all the small changes that you make to your product. These are for your fans, users obsessed with details and power-users.
5. **Videos** let you instructional or promo videos to your documentation.

<{sub}>Building<{/sub}>

Now that you have wrote your documentation, you can simply run:

<{code inline=false}>shade build<{/code}>

to have the system build a static website from the help elements. <{page name="command-build" book="shade"}>This page<{/page}> goes into details about build command and all of its various options.

<{sub}>Advanced Topics<{/sub}>

* <{page name="themes" book="shade"}>Themes<{/page}>
* <{page name="plugins" book="shade"}>Plugins<{/page}>

<{sub}>Contributing<{/sub}>

GitHub project is here: [https://github.com/activecollab/shade](https://github.com/activecollab/shade). To contribute simply form the repo, make the change and submit a pull request.