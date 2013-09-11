XWhois
======

A PHP port of the Perl CPAN module `Net::XWhois` version 0.90.

It may be incomplete and outdated for some top domains, e.g. missing some top
domains like .tv, etc.

`whois` is executed in the shell. The returned content is then parsed differently
depending on the whois server. The available data also differs between whois servers.

Example
-------

```PHP
<?php

$whois = XWhois('textalk.com');

// These are arrays, as thare may be multiple names, nameservers, etc.
print_r($whois->name);
print_r($whois->nameservers);

// Print everything we've got
print_r($whois->getValues());

?>
```

Supported top domains
---------------------

The source code of [XWhois.php](XWhois.php) is very readable on this point.
