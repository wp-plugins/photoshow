<?php
/**
CodePeoplePhotoshow allows to insert photos from Flickr in the blog posts
*/

class CodePeoplePhotoshow {
	
	var $text_domain = 'photoshow';
	
	function init(){
		// I18n
		load_plugin_textdomain($this->text_domain, false, dirname(plugin_basename(__FILE__)) . '/../languages/');
	}
	
	/*
		Remove tables
	*/
	function deactivePlugin() {
		// Remove configuration options
		delete_option('photoshow_flickr_api_key');
	} // End deactivePlugin
	
	/*
		Set a link to plugin settings
	*/
	function settingsLink($links) { 
		$settings_link = '<a href="options-general.php?page=photoshow.php">'.__('Settings').'</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	} // End settingsLink
 
 
	/**
		Print out the admin page
	*/
	function printAdminPage(){
		if(isset($_POST['photoshow_settings']))
			if(!empty($_POST['flickr_api_key'])){
				update_option('photoshow_flickr_api_key', $_POST['flickr_api_key']);
				echo '<div class="updated"><p><strong>'.__("Settings Updated").'</strong></div>';
			}else{
				echo '<div class="error"><p><strong>'.__("The Flickr API Key is required", $this->text_domain).'</strong></div>';
			}
		
		
		$flickr_api_key = get_option('photoshow_flickr_api_key');
		echo '
			<div class="wrap">
				<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
					<h2>Photoshow</h2>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="flickr_api_key">'.__('Enter the Flickr API Key', $this->text_domain).'*</label>
								</th>
								<td>
									<input type="text" id="flickr_api_key" name="flickr_api_key" value="'.$flickr_api_key.'" />
								</td>
							</tr>
						</tbody>
					</table>	
					<h3>'.__('The next options are only available for the advanced version of Photoshow', $this->text_domain).'. <a href="http://wordpress.dwbooster.com/galleries/photoshow-advanced" target="_blank">'.__('Here').'</a></h3>
					<h3  style="color:#DDD;">'.__('Save in cache(optional)', $this->text_domain).'</h3>
					<table class="form-table" style="color:#DDD;">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label  style="color:#DDD;">'.__('Save results in cache', $this->text_domain).'</label>
								</th>
								<td>
									<input type="radio" name="photoshow_advanced_cache" id="photoshow_advanced_cache_yes" value="1" disabled /> <label for="photoshow_advanced_cache_no">'.__('No', $this->text_domain).'</label>
									<select id="photoshow_advanced_cache_expire" name="photoshow_advanced_cache_expire" disabled>
										<option value="day">'.__('Expires in a day', $this->text_domain).'</option>
										<option value="month">'.__('Expires in a month', $this->text_domain).'</option>
										<option value="year">'.__('Expires in a year', $this->text_domain).'</option>
										<option value="infinite">'.__('Never expires', $this->text_domain).'</option>
									</select></td>
							</tr>
						</tbody>
					</table>
					<h3  style="color:#DDD;">'.__('Visual effects(optional)', $this->text_domain).'</h3>
					<table class="form-table"  style="color:#DDD;">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="photoshow_advanced_effect"  style="color:#DDD;">'.__('Replace the images using the effect', $this->text_domain).'</label>
								</th>
								<td>
									<select id="photoshow_advanced_effect" name="photoshow_advanced_effect" disabled>
										<option value = "none">None</option>
										<option value = "fadeToggle">Fade Effect</option>
										<option value = "slideToggle">Slide Effect</option>
										<option value = "toggle">In/Out</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<h3  style="color:#DDD;">'.__('Proxy definition(optional)', $this->text_domain).'</h3>
					<span class="description"  style="color:#DDD;">'.__('Fill the Proxy data only if your website is behind a Proxy', $this->text_domain).'</span>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="proxy_host"  style="color:#DDD;">'.__('Proxy host', $this->text_domain).'</label>
								</th>
								<td>
									<input type="text" name="proxy_host" disabled />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="proxy_port"  style="color:#DDD;">'.__('Proxy port', $this->text_domain).'</label>
								</th>
								<td>
									<input type="text" name="proxy_port" disabled />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="proxy_username"  style="color:#DDD;">'.__('Proxy username', $this->text_domain).'</label>
								</th>
								<td>
									<input type="text" name="proxy_username" disabled />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="proxy_password"  style="color:#DDD;">'.__('Proxy password', $this->text_domain).'</label>
								</th>
								<td>
									<input type="text" name="proxy_password" disabled />
								</td>
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="photoshow_settings" value="true" />
					<div class="submit"><input type="submit" class="button-primary" value="'.__('Update Settings', $this->text_domain).'" /></div>
				</form>
			</div>
		';	
	}
	
	/**
		Set styles to display a frame around Photoshow elements in the Post editor
	*/
	function addMCEStyle($mce_css){
		if ( ! empty( $mce_css ) ) $mce_css .= ',';
		$mce_css .= plugins_url( '/photoshow/css/photoshow.admin.css');
		return $mce_css;
	} // End addMCEStyle
	
	/**
		Set the Photoshow button in media bar over the post editor
	*/
	function setPhotoshowButton(){
		print '<a href="javascript:photoshowAdmin.open();" title="'.__('Insert Flickr Image', $this->text_domain).'"><img src="'.plugins_url('/photoshow/images/photoshow.gif').'" alt="'.__('Insert Flickr Image', $this->text_domain).'" /></a>';
	} // End setPhotoshowButton
	
	/**
		Set the DIV as tag allowed by TinyMCE
	*/
	function allowDiv($init){
		$str = 'div[title|class|style]';
		if ( isset( $init['extended_valid_elements'] ) 
               && ! empty( $init['extended_valid_elements'] ) ) {
            $init['extended_valid_elements'] .= ','.$str;
        } else {
            $init['extended_valid_elements'] = $str;
        }
		return $init;
	} // End allowDiv
	
	/**
		Load the scripts used for Photoshow insertion
	*/
	function adminScripts(){
		wp_enqueue_style('wp-jquery-ui-dialog');
					
		wp_enqueue_script(
			'admin_photoshow_script',
			plugins_url('/photoshow/js/photoshow.admin.js'),
			array('jquery', 'jquery-ui-dialog')
		);
		
		wp_localize_script('admin_photoshow_script', 'str', array(
			'title'  	=> __('Insert Flickr Image', $this->text_domain),
			'width'  	=> __('Width', $this->text_domain),
			'height' 	=> __('Height', $this->text_domain),
			'terms'  	=> __('Terms', $this->text_domain),
			'limit'  	=> __('Number of Images', $this->text_domain),
			'timeout' 	=> __('Load Images every', $this->text_domain),
			'seconds' 	=> __('seconds', $this->text_domain),
			'default' 	=> __('by default', $this->text_domain),
			'required' 	=> __('The * symbol marks the required fields', $this->text_domain),
			'insert'   	=> __('Insert', $this->text_domain)
		));
	} // End adminScripts
	
	/**
		Check if photoshow was inserted in the content and load the corresponding scripts and style files
	*/
	function loadPhotoshowResources(){
		global $wp_query;
		
		if($wp_query->have_posts()){
			foreach($wp_query->posts as $post){
				if(!empty($post->post_content)){
					$dom = new DOMDocument;
					$dom->preserveWhiteSpace = false;
					$dom->loadHTML($post->post_content);
					
					$dom_xpath = new DOMXpath($dom);
					$filter = $dom_xpath->query('//div[class="photoshow"]');
					if($filter && count($filter) > 0){
						wp_enqueue_style(
							'photoshow_style',
							plugins_url('/photoshow/css/photoshow.css')
						);
						
						wp_enqueue_script(
							'photoshow_script',
							plugins_url('/photoshow/js/photoshow.js'),
							array('jquery')
						);
						
						$flickr_api_key = get_option('photoshow_flickr_api_key');
						if(!empty($flickr_api_key)){
							print('<script>var flickr_api_key="'.$flickr_api_key.'";</script>');
						}
						return;
					}
				}	
			}
		}
	} // End loadPhotoshowResources
	
} // End Photoshow
?>