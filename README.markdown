# Bugs
Report bugs on http://heykimo.lighthouseapp.com/projects/19632-moneris-magento/overview

# INSTALLATION
- Copy files to {YOUR INSTALLATION}/app/code/local/Mage/Moneris
- Copy etc/Mage_Moneris.xml to {YOUR INSTALLATION}/etc/module/Mage_moneris.xml
- Download Moneris PHP5 lib and install it to {YOUR INSTALLATION}/lib/moneris
- Set your store id and you api key in the payment module screen in the magento admin

Note by default moneris use their sandbox for the hostname, you need to change them to the appropriate host
when pushing them in production, look at their documentation for the right endpoint.

# TODO:
- Implement void
- move some public method to protected
- add tests/spec for it
- add more documentation?



feel free to send me patches or fork the project.

Pier-Hugues Pellerin ph - at - heykimo.com

Thanks to Antoine Girard - antoinegirard.com if you need magento integration contact him.


# .
 
Copyright (c) 2008 Pier-Hugues Pellerin

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.