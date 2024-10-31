<?php
  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<div class="card hidden" data-container="cdn">
    <table class="form-table">
      <tr>
        <th><label for="accessKeyID">Access Key ID:</label></th>
        <td class="form-field">
          <input id="accessKeyID" name="pxp-press-accessKeyID" type="text" value="<?php echo $accessKey ?>" />
        </td>
      </tr>
      <tr>
        <th><label for="secretKey">Secret Key</label></th>
        <td class="form-field">
          <input id="secretKey" name="pxp-press-secretKey" type="<?php echo ($secretKey != "" ? "password" : "text") ?>" value="<?php echo $secretKey ?>" />
        </td>
      </tr>
      <tr>
        <th><label for="distributionID">Cloudfront Distribution ID:</label></th>
        <td class="form-field">
          <input id="distributionID" name="pxp-press-distributionID" type="text" value="<?php echo $distributionID ?>" />
        </td>
      </tr>
      <tr>
        <th><label for="region">Region:</label></th>
        <td class="form-field">
          <input id="region" name="pxp-press-region" type="text" value="<?php echo $region ?>" />
        </td>
      </tr>
    </table>
</div>
