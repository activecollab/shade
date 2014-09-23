# Principles

1. Keep the documentation where your code is,
2. Make it VCS friendly,
3. Plain-text FTW! Use Shade-flavored Markdown to write documentation. Keep it simple while doing that,
4. Static HTML outperforms dynamicly built pages,
5. Use the eco-system instead of reinventing the wheel (Disqus for comments, open API-s to send customer feedback to your favorite tracking systems),
6. Learn from companies that traditionally have great documentation (Borland, Microsoft etc).

# Why not a dynamic knowledge base? One with database backend, web interface etc?

This goes back to the roots, when this project was internal and used only by our team. In our team, writing documentation is integral part of development process, so integral that it has its own column on our Kanban board. Because of that, we like to have our document where our code is, and that means that we it needs to be in plain text + binary data form, and not in some external database that uses its own version system (if any), has its own editing tools etc.

Benefits of having the documentation in the same place where your code is:

1. It can evolve as code evolves. This makes documentation more likely to happen, because you can draft it as you are working on that awesame feature that has been consuming you for the past couple of weeks,
2. It is versioned. You use version control system for your code, right?
3. It follows your workflow and release cycle. Do you use GitFlow, or a similar workflow? Shade documentation plays well in that setting. Do you need to build an old release and have the documentation included? Easy, because Shade documentation is part of your code when you check-out the tag that you need to build.

# Why these elements (books, what's new articles, release notes and videos)?

These elements worked really well for us for the past 7+ years.