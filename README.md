DirEve Plugin for WordPress
===========================

## Introduction

This plugin creates a search interface for [Virtual Health Library](http://modelo.bvsalud.org/en/) Events Directory (DirEve) information source.

## Requirements

Wordpress 3.x

## Install

1. [Download](https://github.com/bireme/direve-wp-plugin/archive/master.zip) the DirEve plugin for Wordpress;
2. Unzip the plugin below the 'wp-content/plugins' folder of your Wordpress instance and rename it to `direve`;
3. Activate the DirEve plugin through the administration panel of WordPress (dashboard).
    * For further information on installing plugins please see the [Manual Plugin Installation from Wordpress codex site](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

## Configuration

Go to `Settings` in the administration panel (dashboard) and click on the newly created `DirEVE` item.

* `Plugin page` is mandatory and is set to `direve` by default. It defines the URL of the search interface page;
* `Filter query` is optional and defines the strategy (a term or expression) to act as a filter for record displaying;
* `Search form` is optional and defines a flag to control the display of the search box in the `DirEve` homepage;
* `Disqus shortname` is optional. If used, it requires a code for the integration with the associated comments service  [Disqus](http://disqus.com/). Notice this requires previous registration within the comments service;
* `AddThis profile ID` is optional and is provided to allow the integration with sharing tools services [AddThis](http://www.addthis.com/). Notice this requires previous registration within the sharing tools service;
* `Google Analytics code` is optional and allows the integration of website analytics services provided by Google. Notice this requires previous registration in Google.

## Configure RSS feed

To configure the RSS feed, you can also use the following parameters and filters:

* `mode` - Filter by event modality. Possible values are __in-person__, __hybrid__ and __online__
* `start_date` - Filter by start date

## Usage example

Filter by event modality:

```
https://<domain>/<plugin_slug>/events-feed?q=&filter=&mode=online
https://<domain>/<plugin_slug>/events-feed?q=&filter=&mode=online,hibrid
```

Filter by start date:

```
https://<domain>/<plugin_slug>/events-feed?q=&filter=&start_date=20230101
https://<domain>/<plugin_slug>/events-feed?q=&filter=&start_date=2023-01-01
https://<domain>/<plugin_slug>/events-feed?q=&filter=&start_date=+2 days
https://<domain>/<plugin_slug>/events-feed?q=&filter=&start_date=+2 weeks
https://<domain>/<plugin_slug>/events-feed?q=&filter=&start_date=+2 months
```

Filter by event modality and start date:

```
https://<domain>/<plugin_slug>/events-feed?q=&filter=&mode=online&start_date=20230101
```

## Translations of this document

Español: [Instalación del Plugin DirEve para Wordpress](http://wiki.bireme.org/es/index.php/Instalaci%C3%B3n_del_Plugin_DirEve_para_Wordpress)

Português: [Plugin DirEve para Wordpress](http://wiki.bireme.org/pt/index.php/Plugin_DirEve_para_Wordpress)
