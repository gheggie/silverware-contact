# SilverWare Contact Module

[![Latest Stable Version](https://poser.pugx.org/silverware/contact/v/stable)](https://packagist.org/packages/silverware/contact)
[![Latest Unstable Version](https://poser.pugx.org/silverware/contact/v/unstable)](https://packagist.org/packages/silverware/contact)
[![License](https://poser.pugx.org/silverware/contact/license)](https://packagist.org/packages/silverware/contact)

Provides a contact page and contact component for use with [SilverWare][silverware].

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Issues](#issues)
- [Contribution](#contribution)
- [Maintainers](#maintainers)
- [License](#license)

## Requirements

- [SilverWare][silverware]
- [SilverWare Countries][silverware-countries]
- [SilverWare Font Icons][silverware-font-icons]
- [SilverWare Validator][silverware-validator]

## Installation

Installation is via [Composer][composer]:

```
$ composer require silverware/contact
```

## Usage

### Contact Page

This module adds a new page type called `ContactPage` to the application. The contact
page displays a form which allows a user to send a message to one or more recipients.

Add recipients on the "Recipients" tab. Each recipient has a:

- name (shown when the user selects a recipient)
- send to name and email address
- send from name and email address
- email subject

By default, the contact page will notify all recipients via email when someone sends
a message.  To change this behaviour, you can check the "Show recipient field" on the
"Options" tab. A dropdown is then shown on the form allowing the user to select a
recipient for the message.

Also on the "Options" tab, you can choose whether to show phone and subject fields on
the form, and whether the phone field is required. If you uncheck the option "Send via email",
the form will record received messages on the "Messages" tab, but will no longer
notify recipients via email.

### Contact Component

This module also provides a component called `ContactComponent`, which can be added
to your SilverWare templates and layouts. A `ContactComponent` shows a list of
contact-related items, such as addresses, phone numbers, emails and so forth.

The following items are provided with the component:

- `AddressItem`
- `EmailItem`
- `FaxItem`
- `HeadingItem`
- `LinkItem`
- `LinksItem`
- `PhoneItem`
- `SkypeItem`
- `TextItem`

Simply add the `ContactComponent` where desired in your template or layout, and then
add your items as children via the site tree. The item titles will also show font icons if the
option "Show icons" is checked on the "Options" tab of `ContactComponent`.

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## Contribution

Your contributions are gladly welcomed to help make this project better.
Please see [contributing](CONTRIBUTING.md) for more information.

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](http://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](http://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive

[silverware]: https://github.com/praxisnetau/silverware
[silverware-countries]: https://github.com/praxisnetau/silverware-countries
[silverware-font-icons]: https://github.com/praxisnetau/silverware-font-icons
[silverware-validator]: https://github.com/praxisnetau/silverware-validator
[composer]: https://getcomposer.org
[issues]: https://github.com/praxisnetau/silverware-contact/issues
