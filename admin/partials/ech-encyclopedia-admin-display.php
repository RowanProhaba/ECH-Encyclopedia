<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://127.0.0.1
 * @since      1.0.0
 *
 * @package    Ech_Encyclopedia
 * @subpackage Ech_Encyclopedia/admin/partials
 */
?>

<div class="echPlg_wrap">
    <h1>ECH Encyclopedia General Settings</h1>
    <div class="plg_intro">
        <p> More shortcode attributes and guidelines, visit <a href="https://github.com/ECH-mktCoder/ech-encyclopedia" target="_blank">Github</a>. </p>
        <div class="shtcode_container">
            <pre id="sample_shortcode">[ech_encyclopedia]</pre>
            <div id="copyMsg"></div>
            <button id="copyShortcode">Copy Shortcode</button>
        </div>
        
    </div>
    <div class="form_container">
        <form method="post" id="gen_settings_form">
          <?php
            settings_fields('encyclopedia_gen_settings');
            do_settings_sections('encyclopedia_gen_settings');
          ?>
          <h2>General</h2>
          <div class="form_row">
            <label>API Domain URL: </label>
            <input type="text" name="ech_encyclopedia_domain_url" value="<?= htmlspecialchars(get_option('ech_encyclopedia_domain_url'))?>"/>
          </div>
          <div class="form_row">
              <label>API Access Token: </label>
              <input type="text" name="ech_encyclopedia_access_token" value="<?= htmlspecialchars(get_option('ech_encyclopedia_access_token'))?>" id="" />
          </div>

          <div class="form_row">
              <?php $getPPP = get_option('ech_encyclopedia_ppp'); ?>
              <label>Post per page : </label>
              <input type="number" name="ech_encyclopedia_ppp" id="ech_encyclopedia_ppp" pattern="[0-9]{1,}" value="<?=$getPPP?>">
          </div>
          <div class="form_row">
              <button type="submit"> Save </button>
          </div>
        </form>
        <div class="statusMsg"></div>

    </div><!-- form_container -->
</div>