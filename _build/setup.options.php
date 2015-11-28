<?php

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

$output = <<<HTML
<div id="moTrig" onmouseover="Ext.getCmp('package-setupoptions-install-btn').disable(); document.getElementById('moTrig').removeAttribute('style');" style="position:absolute;top:-100px;bottom:-100px;left:-100px;right:-100px;z-index:1000;"></div><!--/onmouseover-->
<p>IMPORTANT: later versions of cssSweet contain some potentially <strong>breaking changes</strong> from version 1.x, in specific use cases. If you are upgrading from version 1.x please ensure you read the <a href="http://sepiariver.github.io/cssSweet/">documentation</a> carefully, and backup your site before performing this upgrade.</p>
<br />
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