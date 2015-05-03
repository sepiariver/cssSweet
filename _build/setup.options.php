<?php

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

$output = <<<HTML
<p>IMPORTANT: version 2.x of cssSweet contains several potentially <strong>breaking changes</strong> from version 1.x. If you are upgrading from version 1.x please ensure you read the documentation carefully, and backup your site before performing this upgrade.</p>
<br />
<p>Please confirm you understand the important imformation above:</p>
<div style="padding-left: 20px;">
    <input type="checkbox" name="confirm_upgrade_breaking_changes" id="confirm_upgrade_breaking_changes" onchange="javascript:this.checked?Ext.getCmp('package-setupoptions-install-btn').enable():Ext.getCmp('package-setupoptions-install-btn').disable()" disabled />
        <label for="confirm_upgrade_breaking_changes" style="display: inline;">Confirmed</label> <br />
</div>
<script>
Ext.getCmp('package-setupoptions-install-btn').disable();
</script>
HTML;
    break;
    default:
    case xPDOTransport::ACTION_UNINSTALL:
        $output = '';
    break;
}

return $output;