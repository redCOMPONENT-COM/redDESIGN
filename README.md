IMPORTANT NOTE: this version of redDESIGN was created for Stickers.dk site. This, is a complete updated version of the original release "redDESIGN 1.0" used for ramsign.dk but refactored in FoF (Framework on framework). 
redDESIGN 1.5 can work in Joomla 2.5 and 3 but Needs a redSHOP 1.3.3 (only available for joomla 2.5) or earlier version for working (all interaction plugins must be installed). This series was discontinued because we decided to add SVG (vectorial graphics support) in version 2.0, created for exakt.dk customer. If you want to see the other versions please check the other branches in this repository.

# redDESIGN 1.5
redDESIGN is a redCOMPONENT extension for Joomla and redSHOP. redDESIGN allows you to sell customizable products like banners, sign or stickers. 

The web user will be able to add text in serveral fonts to a base design.


## Status
Master: [![Build Status](https://magnum.travis-ci.com/redCOMPONENT-COM/redDESIGN.png?branch=master&token=vxVVpxnq2ZPuMp3yebRz)](https://magnum.travis-ci.com/redCOMPONENT-COM/redDESIGN/)

Develop: [![Build Status](https://magnum.travis-ci.com/redCOMPONENT-COM/redDESIGN.png?branch=develop&token=vxVVpxnq2ZPuMp3yebRz)](https://magnum.travis-ci.com/redCOMPONENT-COM/redDESIGN/)


##Roadmap
There are four main milestones in this project:
- [1st Component basic structure](https://github.com/redCOMPONENT-COM/redDESIGN/issues?milestone=4&state=open)
- [2nd Component advanced features](https://github.com/redCOMPONENT-COM/redDESIGN/issues?milestone=5&state=open)
- [3rd component advanced features involving several views](https://github.com/redCOMPONENT-COM/redDESIGN/issues?milestone=7&state=open)
- [4th redshop integration with redDESIGN](https://github.com/redCOMPONENT-COM/redDESIGN/issues?milestone=6&state=open)

##Contributing to redDESIGN

All the code must use [redCOMPONENT Coding standards](https://github.com/redCOMPONENT-COM/documentation/blob/master/coding_standards/coding_standards.md "redCOMPONENT Coding standards") and commits must follow the [Commit Messages Structure](#CMM)  

<a name="CMM"></a> Commit messages Structure
----------------

When you contribute code to the project we ask you to strictly use the standard way of writing a commit message. This way, when building the code history, your commit message will fit nicely with the ones from other developers.  

Commit messages must be in English and has to be formatted like this:  

<code>[type] Description</code>  

### Types  

* [-] Bug fix.
* [*] Improvement.
* [+] New feature.
* [~] Feature deprecation.

### Examples  
<code>[-] #RR-13 : Undefined variable </code>  

## Link your pull with Issues
All the task are listed in the issues page: https://github.com/redCOMPONENT-COM/redDESIGN/issues?state=open (remember to filter by milestone).

Every time you submit a Pull make sure to attach the Issue number that you are solving in the "commit" text. You can reference and automatically close issues with commit messages adding any of this texts:

- fixes #xxx
- fixed #xxx
- fix #xxx
- closes #xxx
- close #xxx
- closed #xxx
