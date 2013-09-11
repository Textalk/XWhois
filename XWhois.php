<?php

/**
 * A port of the Perl CPAN module Net::XWhois version 0.90
 *
 * It may incomplete and be outdated - e.g. missing some top domains like .tv etc.
 *
 * whois is executed in the shell. The returned content is then parsed differently
 * depending on the whois server.
 *
 * Usage:
 *
 *   <?php
 *
 *   $whois = XWhois('textalk.com');
 *
 *   print_r($whois->name);
 *   print_r($whois->nameservers);
 *
 *   ?>
 *
 */
class XWhois {

  private static $DOMAIN_ASSOC = array(
    'al'  => 'whois.ripe.net',      'am'  => 'whois.ripe.net',
    'at'  => 'whois.ripe.net',      'au'  => 'whois.aunic.net',
    'az'  => 'whois.ripe.net',
    'ba'  => 'whois.ripe.net',      'be'  => 'whois.ripe.net',
    'bg'  => 'whois.ripe.net',      'by'  => 'whois.ripe.net',
    'ca'  => 'whois.cdnnet.ca',     'ch'  => 'whois.nic.ch',
    'com' => 'whois.internic.net',
    'cy'  => 'whois.ripe.net',      'cz'  => 'whois.ripe.net',
    'de'  => 'whois.denic.de',      'dk'  => 'whois.dk-hostmaster.dk',
    'dz'  => 'whois.ripe.net',
    'edu' => 'whois.internic.net',  'ee'  => 'whois.ripe.net',
    'eg'  => 'whois.ripe.net',      'es'  => 'whois.ripe.net',
    'fi'  => 'whois.ripe.net',      'fo'  => 'whois.ripe.net',
    'fr'  => 'whois.nic.fr',
    'gb'  => 'whois.ripe.net',      'ge'  => 'whois.ripe.net',
    'gov' => 'whois.nic.gov',       'gr'  => 'whois.ripe.net',
    'hr'  => 'whois.ripe.net',      'hu'  => 'whois.ripe.net',
    'ie'  => 'whois.ripe.net',      'il'  => 'whois.ripe.net',
    'is'  => 'whois.ripe.net',      'it'  => 'whois.ripe.net',
    'jp'  => 'whois.nic.ad.jp',
    'kr'  => 'whois.krnic.net',
    'li'  => 'whois.ripe.net',      'lt'  => 'whois.ripe.net',
    'lu'  => 'whois.ripe.net',      'lv'  => 'whois.ripe.net',
    'ma'  => 'whois.ripe.net',      'md'  => 'whois.ripe.net',
    'mil' => 'whois.nic.mil',       'mk'  => 'whois.ripe.net',
    'mt'  => 'whois.ripe.net',      'mx'  => 'whois.nic.mx',
    'net' => 'whois.internic.net',  'nl'  => 'whois.ripe.net',
    'no'  => 'whois.norid.no',      'nz'  => 'whois.domainz.net.nz',
    'org' => 'whois.internic.net',
    'pl'  => 'whois.ripe.net',      'pt'  => 'whois.ripe.net',
    'ro'  => 'whois.ripe.net',      'ru'  => 'whois.ripe.net',
    'se'  => 'whois.iis.se',        'sg'  => 'whois.nic.net.sg',
    'si'  => 'whois.ripe.net',      'sk'  => 'whois.ripe.net',
    'sm'  => 'whois.ripe.net',      'su'  => 'whois.ripe.net',
    'tn'  => 'whois.ripe.net',      'tr'  => 'whois.ripe.net',
    'tw'  => 'whois.twnic.net',
    'ua'  => 'whois.ripe.net',

    'uk'     => 'whois.nic.uk',
    'gov.uk' => 'whois.ja.net',
    'ac.uk'  => 'whois.ja.net',
    'eu.com' => 'whois.centralnic.com',
    'uk.com' => 'whois.centralnic.com',
    'uk.net' => 'whois.centralnic.com',
    'gb.com' => 'whois.centralnic.com',
    'gb.net' => 'whois.centralnic.com',

    'us'  => 'whois.isi.edu',
    'va'  => 'whois.ripe.net',
    'yu'  => 'whois.ripe.net',
    
    # Added 2010 by Viktor @ textalk
    'info' => 'whois.afilias.info',
  );

  private static $WHOIS_PARSER = array(
    'whois.ripe.net'            => 'RPSL',
    'whois.nic.mil'             => 'INTERNIC',
    'whois.nic.ad.jp'           => 'JAPAN',
    'whois.domainz.net.nz'      => 'GENERIC',
    'whois.nic.gov'             => 'INTERNIC',
    'whois.nic.ch'              => 'RIPE_CH',
    'whois.twnic.net'           => 'TWNIC',
    'whois.internic.net'        => 'INTERNIC',
    'whois.aunic.net'           => 'RIPE',
    'whois.cdnnet.ca'           => 'CANADA',
    'whois.ja.net'              => 'UKERNA',
    'whois.nic.uk'              => 'NOMINET',
    'whois.krnic.net'           => 'KOREA',
    'whois.isi.edu'             => 'INTERNIC',
    'whois.norid.no'            => 'RPSL',
    'whois.centralnic.com'      => 'CENTRALNIC',
    'whois.denic.de'            => 'DENIC',
    'whois.InternetNamesWW.com' => 'INWW',
    'whois.bulkregister.com'    => 'BULKREG',
    'whois.arin.net'            => 'ARIN', #added 08/06/2002 by rwoodard
    'whois.apnic.net'           => 'RPSL', #added 08/06/2002 by rwoodard
    'whois.nic.fr'              => 'RPSL',
    'whois.lacnic.net'          => 'RPSL',
    'whois.nic.br'              => 'BRNIC',
    'whois.nic.mx'              => 'MEXICO',
    'whois.adamsnames.tc'       => 'ADAMS',
    'whois.afilias.info'        => 'AFILIAS',
    'whois.iis.se'              => 'RIPE',
  );

  #these are the parser definitions for each whois server.
  #the AUTOLOAD subroutine creates an object method for each key defined within
  #the server's hash of regexps; this applies the regexp to the response from
  #the whois server to extract the data.  of course you can just write your own
  #parsing subroutine as described in the docs.
  #
  #there ought to be some standardization of the fields being parsed.  for my
  #own personal purposes only RPSL and ARIN are standardized; there needs to be
  #some work done on the other defs to get them to return at least these fields:
  #
  #  name        name of registrant entity (company or person)
  #  netname     name assigned to registrant's network
  #  inetnum     address range registered
  #  abuse_email email addresses named 'abuse@yaddayadda'
  #  gen_email   general correspondence email addresses
  #
  #yes some of these are redundant to what is already there; I saw no reason to
  #delete non-standardized keys, they don't take that much space and might be
  #needed for backwards compatibility. -rwoodard 08/2002
  private static $PARSERS = array(

    'RPSL' => array( #updated by rwoodard 08/06/2002
      'name'            => '(?:descr|owner):\s+([^\n]*)\n',
      'netname'         => 'netname:\s+([^\n]*)\n',
      'inetnum'         => 'inetnum:\s+([^\n]*)\n',
      'abuse_email'     => '\b(?:abuse|security)\@\S+',
      'gen_email'       => 'e-*mail:\s+(\S+\@\S+)',

      'country'         => 'country:\s+(\S+)',
      'status'          => 'status:\s+([^\n]*)\n',
      'contact_admin'   => '(?:admin|owner)-c:\s+([^\n]*)\n',
      'contact_tech'    => 'tech-c:\s+([^\n]*)\n',
      'contact_emails'  => 'email:\s+(\S+\@\S+)',
      'contact_handles' => 'nic-hdl(?:-\S*):\s+([^\n]*)\n',
      'remarks'         => 'remarks:\s+([^\n]*)\n',
      'notify'          => 'notify:\s+([^\n]*)\n',
      'forwardwhois'    => 'remarks:\s+[^\n]*(whois.\w+.\w+)',
    ),

    'ARIN' => array( #from Jon Gilbert 09/04/2000 updated/added to by rwoodard 08/07/2002

      'name'                 => '(?:OrgName|CustName):\s*(.*?)\n',

      'netname'              => 'etName:\s*(\S+)\n+',
      'inetnum'              => 'etRange:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3} - \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})[\n\s]*',
      'abuse_email'          => '(?:abuse|security)\@\S+',
      'gen_email'            => 'Coordinator:[\n\s]+.*?(\S+\@\S+)',

      'netnum'               => 'Netnumber:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})[\n\s]*',
      'hostname'             => 'Hostname:\s*(\S+)[\n\s]*',
      'maintainer'           => 'Maintainer:\s*(\S+)',
      #'record_update'       => 'Record last updated on (\S+)\.\n+',
      'record_update'        => 'Updated:(\S+)\n+',
      'database_update'      => 'Database last updated on (.+)\.[\n\s]+The',
      'registrant'           => '^(.*?)\n\n',
      'reverse_mapping'      => 'Domain System inverse[\s\w]+:[\n\s]+(.*?)\n\n',
      'coordinator'          => 'Coordinator:[\n\s]+(.*?)\n\n',
      'coordinator_handle'   => 'Coordinator:[\n\s]+[^\(\)]+\((\S+?)\)',
      'coordinator_email'    => 'Coordinator:[\n\s]+.*?(\S+\@\S+)',
      'address'              => 'Address:\s+(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})',
      'system'               => 'System:\s+([^\n]*)\n',
      'non_portable'         => 'ADDRESSES WITHIN THIS BLOCK ARE NON-PORTABLE',
      #'multiple'            => 'To single out one record',
      'multiple'             => '\((NET\S+)\)',
      'net_handle'           => '(NET\S+)\)',
      'country'              => 'Country:\s*(\S+)\n+',
    ),

    'BRNIC' => array(
      'name'            => '(?:descr|owner):\s+([^\n]*)\n',
      'netname'        => 'netname:\s+([^\n]*)\n',
      'inetnum'        => 'inetnum:\s+([^\n]*)\n',
      'abuse_email'    => '\b(?:abuse|security)\@\S+',
      'gen_email'      => 'e-*mail:\s+(\S+\@\S+)',

      'country'        => 'BR', #yes this is ugly, tell BRNIC to start putting country fields in their responses
      'status'          => 'status:\s+([^\n]*)\n',
      'contact_admin'   => '(?:admin|owner)-c:\s+([^\n]*)\n',
      'contact_tech'    => 'tech-c:\s+([^\n]*)\n',
      'contact_emails'  => 'email:\s+(\S+\@\S+)',
      'contact_handles' => 'nic-hdl(?:-\S*):\s+([^\n]*)\n',
      'remarks'        => 'remarks:\s+([^\n]*)\n',
      'notify'         => 'notify:\s+([^\n]*)\n',
      'forwardwhois'   => 'remarks:\s+[^\n]*(whois.\w+.\w+)',
    ),

    'KRNIC' => array( #added by rwoodard 08/06/2002

    ),


    'TWNIC' => array( #added by rwoodard 08/06/2002
      'name'                 => '^([^\n]*)\n',
      'netname'              => 'etname:\s*(\S+)\n+',
      'inetnum'              => 'etblock:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3} - \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})[\n\s]*',
      'abuse_email'          => '(?:abuse|security)\@\S+',
      'gen_email'            => 'Coordinator:[\n\s]+.*?(\S+\@\S+)',

      'netnum'               => 'Netnumber:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})[\n\s]*',
      'hostname'             => 'Hostname:\s*(\S+)[\n\s]*',
      'maintainer'           => 'Maintainer:\s*(\S+)',
      'record_update'        => 'Record last updated on (\S+)\.\n+',
      'database_update'      => 'Database last updated on (.+)\.[\n\s]+The',
      'registrant'           => '^(.*?)\n\n',
      'reverse_mapping'      => 'Domain System inverse[\s\w]+:[\n\s]+(.*?)\n\n',
      'coordinator'          => 'Coordinator:[\n\s]+(.*?)\n\n',
      'coordinator_handle'   => 'Coordinator:[\n\s]+[^\(\)]+\((\S+?)\)',
      'coordinator_email'    => 'Coordinator:[\n\s]+.*?(\S+\@\S+)',
      'address'              => 'Address:\s+(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})',
      'system'               => 'System:\s+([^\n]*)\n',
      'non_portable'         => 'ADDRESSES WITHIN THIS BLOCK ARE NON-PORTABLE',
      'multiple'             => 'To single out one record',
      'net_handle'           => '\((NETBLK\S+)\)',
      'country'              => '\n\s+(\S+)\n\n',
    ),

    'INTERNIC' => array(
      'name'            => '[\n\r\f]+\s*[Dd]omain [Nn]ame[:\.]*\s+(\S+)',
      'status'          => 'omain Status[:\.]+\s+(.*?)\s*\n',
      'nameservers'     => '[\n\r\f]+\s*([a-zA-Z0-9\-\.]+\.[a-zA-Z0-9\-]+\.[a-zA-Z\-]+)[:\s\n$]',
      'registrant'      => '(?:egistrant|rgani[sz]ation)[:\.]*\s*\n(.*?)\n\n',
      'contact_admin'   => '(?:dministrative Contact|dmin Contact).*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_tech'    => '(?:echnical Contact|ech Contact).*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_zone'    => 'one Contact.*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_billing' => 'illing Contact.*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_emails'  => '(\S+\@\S+)',
      'contact_handles' => '\(([^\W\d]+\d+)\)',
      'domain_handles'  => '\((\S*?-DOM)\)',
      'org_handles'     => '\((\S*?-ORG)\)',
      'not_registered'  => 'No match',
      'forwardwhois'    => 'Whois Server: (.*?)(?=\n)',
    ),


    'BULKREG' => array(
      'name'            => 'omain Name[:\.]*\s+(\S+)',
      'status'          => 'omain Status[:\.]+\s+(.*?)\s*\n',
      'nameservers'     => '[\n\r\f]+\s*([a-zA-Z0-9\-\.]+\.[a-zA-Z0-9\-]+\.[a-zA-Z\-]+)[:\s\n$]',
      'registrant'      => '(.+)\([\w\-]+\-DOM\).*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_admin'   => 'dmin[a-zA-Z]*? Contact.*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_tech'    => 'ech[a-zA-Z]*? Contact.*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_zone'    => 'one Contact.*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_billing' => 'illing Contact.*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_emails'  => '(\S+\@\S+)',
      'contact_handles' => '\((\w+\d+\-BR)\)',
      'domain_handles'  => '\((\S*?-DOM)\)',
      'org_handles'     => '\((\S*?-ORG)\)',
      'not_registered'  => 'Not found\!',
      'forwardwhois'    => 'Whois Server: (.*?)(?=\n)',
      'registrar'       => 'egistrar\s*\w*[\.\:]* (.*?)\.?\n',
      'reg_date'        => 'reated on[\.\:]* (.*?)\.?\n',
      'exp_date'        => 'xpires on[\.\:]* (.*?)\.?\n',
    ),


    'INWW' => array(
      'name'            => 'omain Name\.+ (\S+)',
      'status'          => 'omain Status\.+ ([^\n]*)\n',
      'nameservers'     => 'Name Server\.+ (\S+)',
      'registrant'      => 'Organisation \w{4,7}\.+ ([^\n]+?)\n',
      'contact_admin'   => 'Admin \w{3,7}\.+ ([^\n]*)\n',
      'contact_tech'    => 'Tech \w{3,7}\.+ ([^\n]*)\n',
      'contact_zone'    => 'Zone \w{3,7}\.+ ([^\n]*)\n',
      'contact_billing' => 'Billing \w{3,7}\.+ ([^\n]*)\n',
      'contact_emails'  => '(\S+\@\S+)',
      'contact_handles' => '\((\w+\d+)\)',
      'domain_handles'  => '\((\S*?-DOM)\)',
      'org_handles'     => '\((\S*?-ORG)\)',
      'not_registered'  => 'is not registered',
      'forwardwhois'    => 'Whois Server: (.*?)(?=\n)',
      'registrar'       => 'egistrar\s*\w*[\.\:]* (.*?)\.?\n',
      'exp_date'        => 'Expiry Date\.+ ([^\n]*)\n',
      'reg_date'        => 'Registration Date\.+ ([^\n]*)\n',
    ),


    'INTERNIC_CONTACT' => array(
      'name'            => '(.+?)\s+\(.*?\)(?:.*?\@)',
      'address'         => '\n(.*?)\n[^\n]*?\n\n\s+Re',
      'email'           => '\s+\(.*?\)\s+(\S+\@\S+)',
      'phone'           => '\n([^\n]*?)\(F[^\n]+\n\n\s+Re',
      'fax'             => '\(FAX\)\s+([^\n]+)\n\n\s+Re',
    ),


    'CANADA'  => array(
      'name'            => 'domain:\s+(\S+)\n',
      'nameservers'     => '-Netaddress:\s+(\S+)',
      'contact_emails'  => '-Mailbox:\s+(\S+\@\S+)',
    ),

    'RIPE' => array(
      'name'            => 'domain:\s+(\S+)\n',
      'nameservers'     => 'nserver:\s+(\S+)',
      'contact_emails'  => 'e-mail:\s+(\S+\@\S+)',
      'registrant'      => 'descr:\s+(.+?)\n',
    ),

    'RIPE_CH' => array(
      'name'            => 'Domain Name:[\s\n]+(\S+)\n',
      'nameservers'     => 'Name servers:[\s\n]+(\S+)[\s\n]+(\S+)',
    ),

    'NOMINET' => array(
      'name'                => 'omain Name:\s+(\S+)',
      'registrant'          => 'egistered For:\s*(.*?)\n',
      'ips_tag'             => 'omain Registered By:\s*(.*?)\n',
      'record_updated_date' => 'Record last updated on\s*(.*?)\s+',
      'record_updated_by'   => 'Record last updated on\s*.*?\s+by\s+(.*?)\n',
      'nameservers'         => 'listed in order:[\s\n]+(\S+)\s.*?\n\s+(\S*?)\s.*?\n\s*\n',
      'whois_updated'       => 'database last updated at\s*(.*?)\n',
    ),

    'UKERNA'  => array(
      'name'                => 'omain Name:\s+(\S+)',
      'registrant'          => 'egistered For:\s*(.*?)\n',
      'ips_tag'             => 'omain Registered By:\s*(.*?)\n',
      'record_updated_date' => 'ecord updated on\s*(.*?)\s+',
      'record_updated_by'   => 'ecord updated on\s*.*?\s+by\s+(.*?)\n',
      'nameservers'         => 'elegated Name Servers:[\s\n]+(\S+)[\s\n]+(\S+).*?\n\s*\n',
      'contact_emails'      => 'Domain contact:\s*(.*?)\n',
    ),

    'CENTRALNIC' => array(
      'name'                => 'omain Name:\s+(\S+)',
      'registrant'          => 'egistrant:\s*(.*?)\n',
      'contact_admin'       => 'lient Contact:\s*(.*?)\n\s*\n',
      'contact_billing'     => 'illing Contact:\s*(.*?)\n\s*\n',
      'contact_tech'        => 'echnical Contact:\s*(.*?)\n\s*\n',
      'record_created_date' => 'ecord created on\s*(.*?)\n',
      'record_paid_date'    => 'ecord paid up to\s*(.*?)\n',
      'record_updated_date' => 'ecord last updated on\s*(.*?)\n',
      'nameservers'         => 'in listed order:[\s\n]+(\S+)\s.*?\n\s+(\S*?)\s.*?\n\s*\n',
      'contact_emails'      => '(\S+\@\S+)',
    ),

    'DENIC' => array(
      'name'            => 'domain:\s+(\S+)\n',
      'registrants'     => 'descr:\s+(.+?)\n',
      'contact_admin'   => 'admin-c:\s+(.*?)\s*\n',
      'contact_tech'    => 'tech-c:\s+(.*?)\s*\n',
      'contact_zone'    => 'zone-c:\s+(.*?)\s*\n',
      'nameservers'     => 'nserver:\s+(\S+)',
      'status'          => 'status:\s+(.*?)\s*\n',
      'changed'         => 'changed:\s+(.*?)\s*\n',
      'source'          => 'source:\s+(.*?)\s*\n',
      'person'          => 'person:\s+(.*?)\s*\n',
      'address'         => 'address:\s+(.+?)\n',
      'phone'           => 'phone:\s+(.+?)\n',
      'fax_no'          => 'fax-no:\s+(.+?)\n',
      'contact_emails'  => 'e-mail:\s+(.+?)\n',
    ),

    'JAPAN' => array(
      'name'            => '\[Domain Name\]\s+(\S+)',
      'nameservers'     => 'Name Server\]\s+(\S+)',
      'contact_emails'  => '\[Reply Mail\]\s+(\S+\@\S+)',
    ),

    'TAIWAN' => array(
      'name'            => 'omain Name:\s+(\S+)',
      'registrant'      => '^(\S+) \(\S+?DOM)',
      'contact_emails'  => '(\S+\@\S+)',
      'nameservers'     => 'servers in listed order:[\s\n]+\%see\-also\s+\.(\S+?)\:',
    ),

    'KOREA'  => array(
      'name'            => 'Domain Name\s+:\s+(\S+)',
      'nameservers'     => 'Host Name\s+:\s+(\S+)',
      'contact_emails'  => 'E\-Mail\s+:\s*(\S+\@\S+)',
    ),

    'MEXICO' => array(
      'name'            => '[\n\r\f]+\s*[Nn]ombre del [Dd]ominio[:\.]*\s+(\S+)',
      'status'          => 'omain Status[:\.]+\s+(.*?)\s*\n',
      'nameservers'     => 'ameserver[^:]*:\s*([a-zA-Z0-9.\-])+',
      'registrant'      => '(?:egistrant|rgani[sz]acion)[:\.]*\s*\n(.*?)\n\n',
      'contact_admin'   => '(?:tacto [Aa]dministrativo|dmin Contact).*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_tech'    => '(?:tacto [Tt]ecnico|ech Contact).*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_billing' => 'to de Pago.*?\n(.*?)(?=\s*\n[^\n]+?:\s*\n|[\n\r\f]{2})',
      'contact_emails'  => '(\S+\@\S+)',
      'contact_handles' => '\(([^\W\d]+\d+)\)',
      'not_registered'  => 'No Encontrado',
      'reg_date'        => 'de creacion[\.\:]* (.*?)\.?\n',
      'record_updated_date' => 'a modificacion[\.\:]* (.*?)\.?\n',
    ),

    'ADAMS' => array(
      'name'            => '(\S+) is \S*\s*registered',
      'not_registered'  => 'is not registered',
    ),

    'AFILIAS' => array(
      'name'            => 'Domain Name:[\t ]*(\S+)',
      'status'          => 'Status:[\t ]*(\S+)',
      'contact_emails'  => 'Admin Email:[\t ]*(\S+)',
      'nameservers'     => 'Name Server:[\t ]*(\S+)',
    ),

    'GENERIC' => array(
      'contact_emails'  => '(\S+\@\S+)',
    ),

  );

  private $whois;
  private $parser;

  /**
   * The constructor makes a whois call and the initalizes an object with accessors to get the info.
   */
  public function __construct($domain) {
    // Determine which part of the domain to run whois on
    list ($topdomain, $name, $subdomain) = array_reverse(explode('.', $domain));
    if ($topdomain != 'name') $registered_domain = "$name.$topdomain";
    else $registered_domain = "$subdomain.$name.$topdomain";

    // Execure whois in a shell
    $this->whois = shell_exec("whois " . escapeshellarg($registered_domain));

    // Select a whois parser depending on topdomain
    if (isset(self::$DOMAIN_ASSOC[$topdomain])) {
      $parser = self::$WHOIS_PARSER[self::$DOMAIN_ASSOC[$topdomain]];
    }
    else {
      $parser = 'GENERIC';
    }
    $this->parser = self::$PARSERS[$parser];
  }

  /**
   * Returns an array of the found values, or null if isset would return false.
   */
  public function __get($key) {
    if (!isset($this->parser[$key])) return null;
    $pattern = '/' . $this->parser[$key] . '/';
    $values = array();
    if (preg_match_all($pattern, $this->whois, $matchess, PREG_SET_ORDER)) {
      foreach ($matchess as $matches) {
        for ($i = 1; $i < count($matches); $i++) {
          $value = trim($matches[$i]);
          if ($value != '') $values[] = $value;
        }
      }
    }
    return $values;
  }

  /** Returns true if and only if __get would return null */
  public function __isset($key) {
    return isset($this->parser[$key]);
  }

  /** Returns all available values */
  public function getValues() {
    $values = array();
    foreach ($this->parser as $key => $pattern) {
      $values[$key] = $this->__get($key);
    }
    return $values;
  }
}

