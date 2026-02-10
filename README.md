# tzdate

Command-line tool to print the date-time in multiple timezones, built on top of PHP.

## Require

- PHP >= 8.2

## Install

Run `build.sh` to build the `tzdate` standalone executable.

You can place the file anywhere you can easily access, such as `/usr/local/bin`.

## Basic usage

Print the current date-time in default timezones.

```
$ tzdate
Honolulu        -10:00  HST       2015-06-30 14:00:00
San Francisco   -07:00  PDT       2015-06-30 17:00:00
New York        -04:00  EDT       2015-06-30 20:00:00
UTC             +00:00  UTC       2015-07-01 00:00:00
London          +01:00  BST       2015-07-01 01:00:00
Paris           +02:00  CEST      2015-07-01 02:00:00
Beijing         +08:00  CST       2015-07-01 08:00:00
Tokyo           +09:00  JST       2015-07-01 09:00:00
```

See the Customize section below to modify the default timezones.

Type the following to print the date-time of 9pm, July 15 (current year) in San Francisco.

```
$ tzdate '7/15 9pm' sf
Honolulu        -10:00  HST       2015-07-15 18:00:00
San Francisco   -07:00  PDT       2015-07-15 21:00:00
New York        -04:00  EDT       2015-07-16 00:00:00
UTC             +00:00  UTC       2015-07-16 04:00:00
London          +01:00  BST       2015-07-16 05:00:00
Paris           +02:00  CEST      2015-07-16 06:00:00
Beijing         +08:00  CST       2015-07-16 12:00:00
Tokyo           +09:00  JST       2015-07-16 13:00:00
```

The first argument is a [string expression of date-time](http://php.net/datetime.formats) or Unix time number.

The second argument is the timezone name for the date-time
(If omitted, the timezone defined as [date.timezone](http://php.net/datetime.configuration#ini.date.timezone) of PHP will be used).

You can use the following forms to specify the timezone. All case-insensitive, and underscores and spaces can be omitted.

* [IANA Timezone Identifier](http://php.net/timezones) (e.g. `America/Los_Angeles`)
* The city name part of the identifier (e.g. `'Los Angeles'`, `losangeles`)
* Abbreviations can also be used for some cities (e.g. `la`)

You can even use the names of some cities that are not in the IANA registry.
For example, you can use `'San Francisco'`, `sanfrancisco` or `sf` for it.

For cities that are not in the IANA registry or for aliases/abbreviations of cities, see the Customize section below.

## Command options

### `-z`

You can use `-z` option to customize the timezones of the list.

For example, to print the current date-time in Hong Kong, Kolkata, and Anchorage, type the following.

```
$ tzdate -z hk -z kolkata -z anchorage
Anchorage       -08:00  AKDT      2015-06-30 16:00:00
UTC             +00:00  UTC       2015-07-01 00:00:00
Kolkata         +05:30  IST       2015-07-01 05:30:00
Hong Kong       +08:00  HKT       2015-07-01 08:00:00
```

The list always includes the timezone defined as [date.timezone](http://php.net/datetime.configuration#ini.date.timezone) of PHP.

### `-f`

With `-f` option, you can customize the date-time format in the list.
Any formats accepted by PHP's [`date()`](http://php.net/date) function can be used.

For example, type the following to print the date-time in RFC 2822 form (which can be specified by `r`).

```
$ tzdate -f r
Honolulu        -10:00  HST       Tue, 30 Jun 2015 14:00:00 -1000
San Francisco   -07:00  PDT       Tue, 30 Jun 2015 17:00:00 -0700
New York        -04:00  EDT       Tue, 30 Jun 2015 20:00:00 -0400
UTC             +00:00  UTC       Wed, 01 Jul 2015 00:00:00 +0000
London          +01:00  BST       Wed, 01 Jul 2015 01:00:00 +0100
Paris           +02:00  CEST      Wed, 01 Jul 2015 02:00:00 +0200
Beijing         +08:00  CST       Wed, 01 Jul 2015 08:00:00 +0800
Tokyo           +09:00  JST       Wed, 01 Jul 2015 09:00:00 +0900
```

## More usage examples

### Working with Unix time

You can directly specify a Unix time number as the first argument.

```
$ tzdate 1437019200
Honolulu        -10:00  HST       2015-07-15 18:00:00
San Francisco   -07:00  PDT       2015-07-15 21:00:00
New York        -04:00  EDT       2015-07-16 00:00:00
UTC             +00:00  UTC       2015-07-16 04:00:00
London          +01:00  BST       2015-07-16 05:00:00
Paris           +02:00  CEST      2015-07-16 06:00:00
Beijing         +08:00  CST       2015-07-16 12:00:00
Tokyo           +09:00  JST       2015-07-16 13:00:00
```

To instantly know the Unix time of a certain date-time in a certain timezone, use `-f` option with `U` format.

```
$ tzdate '7/15 9pm' sf -f U
Honolulu        -10:00  HST       1437019200
San Francisco   -07:00  PDT       1437019200
New York        -04:00  EDT       1437019200
UTC             +00:00  UTC       1437019200
London          +01:00  BST       1437019200
Paris           +02:00  CEST      1437019200
Beijing         +08:00  CST       1437019200
Tokyo           +09:00  JST       1437019200
```

## Customize

Copy [`res/config.json.dist`](https://github.com/tisogawa/tzdate/blob/master/res/config.json.dist) to `res/config.json`
and modify the entries. The entries in `res/config.json` will override the entry in `.dist`.

### default_timezones

Default timezones for the list (used when `-z` option is not present).

For example, to print the date-time in Russia's 11 timezones by default, edit the entry as follows.

```json
{
  "default_timezones": [
    "Kaliningrad",
    "Moscow",
    "Samara",
    "Yekaterinburg",
    "Omsk",
    "Krasnoyarsk",
    "Irkutsk",
    "Yakutsk",
    "Vladivostok",
    "Srednekolymsk",
    "Kamchatka"
  ]
}
```

### cities

You can define cities that are not in the IANA registry.

For example, St. Petersburg, Florida is not in IANA registry but if you want to specify the timezone by the name,
add the following line to the entry.

```json
{
  "cities": {
    "St Petersburg": "America/New_York"
  }
}
```

Then, you can give `'St Petersburg'` or `stpetersburg` to the command.

```
$ tzdate -z stpetersburg
St Petersburg   -04:00  EDT       2015-06-30 20:00:00
UTC             +00:00  UTC       2015-07-01 00:00:00
```

### aliases

You can define aliases/abbreviations for cities.

For example, if you have added `"St Petersburg"` in the `cities` entry as described above,
add the following lines to the entry, too.

```json
{
  "aliases": {
    "SPB": "St Petersburg",
    "St Pete": "St Petersburg"
  }
}
```

Then, you can use `spb` or `stpete` instead of `'St Petersburg'` or `stpetersburg`.

```
$ tzdate -z spb
St Petersburg   -04:00  EDT       2015-06-30 20:00:00
UTC             +00:00  UTC       2015-07-01 00:00:00
```

### list_format

The default format for the list.
