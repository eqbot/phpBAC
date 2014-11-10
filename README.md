phpBAC
======

A php implementation of Ball and Chain

This is pretty much a direct clone of the webpages I made for this.
Don't expect this to be secure for industry use, I probably made really bad choices with padding.
On a LAMP server, this all just goes into your /var folder.

You'll have to set up your own ball. I did this by pulling 1gig of data from urandom. It goes in wwwincludes.

Also, you'll have to set up your own SQL server. I might update this later with my configuration.

I did my best to have it accomodate varying sizes of balls, but I may have left some sloppy code in that only works with 1gig balls.

I claim no responsibility for any passwords stored with this. There may be some horrible flaw with my implementation.
