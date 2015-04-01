cssSweet
=========================================
Version: 1.2.1-pl

Author: YJ Tso (@sepiariver) [on Twitter](https://twitter.com/sepiariver)

cssSweet is a suite of tools for processing CSS. It comes with several output modifiers to modify CSS values, and a plugin that writes processed CSS from the specified chunk to a flat CSS file.

## Example Use Cases
- Use system or context settings to store primary colors in a site's color scheme, then process all other color values relative to these, so you can shift the entire color theme by modifying a few settings.
- Use in conjunction with ClientConfig to give site owners a nice UI to manage CSS styles---but only the ones you expose, and only in the manner you choose!
- Author MODX "themes" that are customizable by the end user.
- Use other MODX tags & placeholders in your CSS - limitless possibilities, "dynamic CSS" without performance penalty, because the plugin writes the static CSS file on the Manager Clear Cache and Chunk Save actions, not per request.

## Usage

### Installation
You can install cssSweet via the MODX Extras Installer. Or you can download it from the [_packages directory](https://github.com/sepiariver/cssSweet/tree/master/_packages). You would upload it to the packages subdirectory of your MODX install's core directory, and use the "Search Locally for Packages" feature in the Extras Installer to find and install the package.

### CSS Sweetness
Once installed, you'll have a default Chunk in the "cssSweet" category. If you edit that Chunk and click the "Manage" » "Clear Cache" main menu item, a CSS file will be generated for you at 
`[[++assets_path]]components/csssweet/csss-custom.css`

Reference this generated CSS file in your template, like this 
`<link rel="stylesheet" href="[[++assets_path]]components/csssweet/csss-custom.css">`
and you'll be good to go.

### Customization
In the Plugin properties, you can set the Chunks that you wish to process and include, by name, in a comma-separated list. Note you can include static Chunks that reference files---even minified files. You can also customize the filesystem location to which to write the output file.
![Screenshot](https://www.dropbox.com/s/9i5kkcb7k5097pu/Screenshot%202015-04-01%2010.51.16.png?dl=1)

### Events
You can optionally enable the `OnChunkFormSave` Event in the Plugin events tab.
![Screenshot](https://www.dropbox.com/s/9diyvnu04mx15vd/Screenshot%202015-04-01%2011.01.16.png?dl=1)
Doing so will speed up your process immensely, as a new output file will be generated each time you save a Chunk that is in the list of Chunks you've set.
