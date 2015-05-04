cssSweet
=========================================
Version: 2.0.0-rc1

Author: YJ Tso (@sepiariver) [on Twitter](https://twitter.com/sepiariver)

cssSweet is a suite of tools for processing (S)CSS. It comes with several output modifiers to modify CSS values, and a plugin that concatenates and processes (S)CSS from specified Chunks and outputs to a minified CSS file. version 2+ also concatenates and minifies JS with a 2nd plugin.

The documentation site is here: 

http://sepiariver.github.io/cssSweet

as well as here:

http://rtfm.modx.com/extras/revo/csssweet (this one needs updating)

## Example Use Cases
- Use system or context settings to store primary colors in a site's color scheme, then process all other color values relative to these, so you can shift the entire color theme by modifying a few settings.
- Use in conjunction with ClientConfig to give site owners a nice UI to manage CSS styles--but only the ones you expose, and only in the manner you choose!
- Author MODX "themes" that are customizable by the end user.
- Use other MODX tags & placeholders in your CSS--limitless possibilities! Utilize "dynamic CSS" without the performance penalty, because the plugin writes the static CSS file on the Manager Clear Cache and Chunk Save actions, not per request.
- Add a code editor like Ace, and you can use MODX as your IDE for front-end development.

## Potentially Breaking Changes in Version 2
The utmost care has been taken to ensure that upgrades of cssSweet are as safe as possible. However, there are a few scenarios that could potentially break an existing site, when you upgrade cssSweet from version 1 to version 2+. Hopefully the following points will help you avoid those:

1. **The default property set.** I haven't found a way to update the saveCustomCss plugin without overwriting the default property set. If you've  customized the default property set, I recommend exporting it before performing the upgrade. You can then re-import your property set after upgrading, to restore custom values. However:
2. **Some property keys have changed.** Most notably, `custom_css_chunk` is now `custom_scss_chunks` to more accurately describe the plugin's actions. Ensure that after upgrading, your CSS Chunk names are listed in the new property, rather than the old one. I figured a major version upgrade is the most opportune time to change property keys.
3. **The sample Chunk has a different name.** This served 2 purposes: to demonstrate the scss syntax compatibility, and prevent overwriting the default Chunk on upgrade, because some users (including myself) erroneously customized the default Chunk without changing its name. As with any MODX Extra that installs Chunks, it's highly recommended that you create new Chunks or rename the sample Chunk. That said, the cssSweet 2.x packages have been authored to *not* overwrite existing Chunks (with the help of @TheBoxer 's GitPackageManagement component), but I've tested this minimally.
4. **The default output filename has changed.** In an effort to prevent overwriting your existing compiled CSS before you've had a chance to set the properties correctly, I've changed the default CSS output filename. However, if by sheer chance, you had previously customized your output filename to `custom_css.min.css`, then the saveCustomCss plugin will overwrite it by default! In this (probably rare) case, _*make sure*_ to setup the plugin properties correctly before executing the plugin.

## Usage

### Installation
You can install cssSweet via the MODX Extras Installer. Or you can download it from the [_packages directory](https://github.com/sepiariver/cssSweet/tree/master/_packages). You would upload it to the packages subdirectory of your MODX install's core directory, and use the "Search Locally for Packages" feature in the Extras Installer to find and install the package.

### CSS Sweetness
Once installed, you'll have a default Chunk in the "cssSweet" category. If you edit that Chunk and click the "Manage" » "Clear Cache" main menu item, a CSS file will be generated for you at 
`[[++assets_path]]components/csssweet/custom_css.min.css`

Note: it's highly recommended to create new Chunks with unique names. As of version 2.x, the cssSweet package has been configured to _not_ update an existing Chunk with the same name, but I haven't tested that thoroughly yet. I'd hate for it to fail in your production environment.

Reference the generated CSS file in your template, like this 
`<link rel="stylesheet" href="[[++assets_path]]components/csssweet/custom_css.min.css">`
and you'll be good to go.

### Customization
In the Plugin properties, you can set the names of Chunks that you wish to process and include, in a comma-separated list. (As of version 2, this is required for the plugin to do anything.) 

Note you can include static Chunks that reference files--even minified files. You can also customize the filesystem location to which to write the output file. Chunks will be included in the order which they appear in the comma-separated list. As of version 2, cssSweet will parse any SCSS syntax in your Chunks, and log error messages generated by the scssphp processor class.
![Screenshot](https://www.dropbox.com/s/b118dgvuk31xzni/Screenshot%202015-05-04%2012.25.19.png?dl=1)

### Events
You can optionally enable the `OnChunkFormSave` Event in the Plugin events tab. (As of version 2, this event is enabled by default.)
![Screenshot](https://www.dropbox.com/s/9diyvnu04mx15vd/Screenshot%202015-04-01%2011.01.16.png?dl=1)
Doing so will speed up your process immensely, as a new output file will be generated each time you save a Chunk that is in the list of Chunks you've set.

### "Dev" Mode
There's a new set of properties for firing the plugins in dev mode. By enabling dev mode, the plugin will use the properties prefixed with `dev_`, allowing you to process a different set of Chunks, output to a different file. You can then call some conditional on your site, like: 

```
[[!+modx.user.id:is=`0`:then=`<link rel="stylesheet" href="prod.css">`:else=`<link rel="stylesheet" href="dev.css">`]]
```
Granted this tag would be uncacheable and therefore this method shouldn't be used on busy production sites, but there are potentially other uses for dev mode.

## Thanks
A lot of time, research and consideration was put into which libraries should be included in this package, for the minification functions. I whole-heartedly support these fine developers and their open-source projects. Please extend your thanks to these folks, if you find cssSsweet useful (or even if you don't :P)

[@leafo/scssphp] (https://github.com/leafo/scssphp)

[@tedious/Jshrink] (https://github.com/tedious/JShrink)
