
# FieldTypeRate 0.0.2

## About FieldTypeRate

This is a module that adds a star rating field for the Processwire CMS. This
module is based on the jQuery plugin [raty](http://wbotelhos.com/raty);

## Usage

This module assumes jQuery is included on your page before you render the field.

Add the field to your template the normal way. In your template file you can 
render the stars and their related html/javascript like below.

`echo $page->my_rating_field;`

## How it works

When a user clicks the stars, the browser will post the vote to the same page, and 
the stars will be replaced by a thank you message.

There is a mechanism to prevent duplicate votes inspired by @apeisa's 
[FieldtypePoll](https://github.com/apeisa/FieldtypePoll).

The admin user can see how many votes a particular page has recieved by editing the 
page in the admin. The admin user can also reset the vote count in the same place.
