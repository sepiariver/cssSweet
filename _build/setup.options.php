<?php

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

$output = <<<HTML
<div id="moTrig" onmouseover="Ext.getCmp('package-setupoptions-install-btn').disable(); document.getElementById('moTrig').removeAttribute('style');" style="position:absolute;top:-100px;bottom:-100px;left:-100px;right:-100px;z-index:1000;"></div><!--/onmouseover-->
<p>Some things to pay attention to:</p>
<ul>
<li>The "Property Preprocess" checkbox for the Plugins may have been reset on upgrade</li>
<li>The default property set may have been overwritten on upgrade. It's best to use a custom property set. Even so, check that the custom property set is assigned to the Plugin Events after upgrading.</li>
<li>Major version upgrades may contain breaking changes. It's a good idea to backup your site before upgrading Extras.</li>
<li>**Version 5.x adds namespaces to Snippets, and significantly changes their behaviour.</li>
</ul>
<p>Please confirm you understand the above:</p>
<div style="padding-left: 20px;">
    <input type="checkbox" name="confirm_upgrade_breaking_changes" id="confirm_upgrade_breaking_changes" onchange="javascript:this.checked?Ext.getCmp('package-setupoptions-install-btn').enable():Ext.getCmp('package-setupoptions-install-btn').disable()" />
        <label for="confirm_upgrade_breaking_changes" style="display: inline;">Confirmed</label> <br />
</div>


HTML;
    break;
    default:
    case xPDOTransport::ACTION_UNINSTALL:
        $output = '';
    break;
}

return $output;