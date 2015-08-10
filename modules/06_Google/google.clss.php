<?php 

if( !class_exists( 'GoogleClass' ) )
{
	class GoogleClass
	{
		var $text_domain;
		
		function __construct( $config )
		{
			$this->text_domain = $config[ 'text_domain' ];
		}
		
		// Displays and manages the settings section for Google search
		function settings()
		{	
			$mssg = '';
			if( isset( $_REQUEST[ 'photoshow_google_settings' ] ) )
			{
				$photoshow_google_active = ( isset( $_POST[ 'photoshow_google_active' ] ) ) ? true : false;
				update_option( 'photoshow_google_active', $photoshow_google_active );
				$mssg .= '<div class="updated"><p><strong>'.__( "Google Search Settings Updated", $this->text_domain ).'</strong></p></div>';
			}	
			$photoshow_google_active = get_option( 'photoshow_google_active' );
			
			
			?>
			<div class="postbox">
				<input type="hidden" id="photoshow_google_settings" name="photoshow_google_settings" value="1" />
				<h3 class='hndle' style="padding:5px;"><span><?php _e( 'Google Search Settings', $this->text_domain ); ?></span></h3>
				<div class="inside">
					<?php print $mssg; ?>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="photoshow_google_active"><?php _e( 'Search in Google', $this->text_domain ); ?></label>
								</th>
								<td>
									<input type="checkbox" id="photoshow_google_active" name="photoshow_google_active" <?php if( $photoshow_google_active ) echo "CHECKED"; ?> />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>	
			<?php
			
		} // End settings
		
		
		function get( $terms, $from, $length )
		{ 	
			// $length parameter is maintained for compatibility only
			$output = new stdClass;
			$output->results = array();
			
			$response = wp_remote_get( "http://ajax.googleapis.com/ajax/services/search/images?v=2.0&rsz=large&start={$from}&q=".urlencode( $terms ), array('timeout' => 120) );

			if( !is_wp_error( $response ) )
			{

				// Create the output
				$phpObj =  json_decode( $response[ 'body' ] );
				if( !is_null( $phpObj ) && !empty( $phpObj->responseData ) && !empty( $phpObj->responseData->results ) )
				{
					try
					{
						$results  = $phpObj->responseData->results;
						$list = array();
						foreach( $results as $entry )
						{
							$image = new stdClass();
							$image->url = $entry->url;
							$image->title = ( isset( $entry->titleNoFormatting ) ) ? utf8_encode( $entry->titleNoFormatting ) : '';
							$image->origin = ( !empty( $entry->originalContextUrl ) ) ? $entry->originalContextUrl : '';
							$list[] = $image;
						}
						$output->results = $list;
					}
					catch( Exception $e)
					{
						$output->error = __( 'There are no images associated to the search terms', $this->text_domain );
					}
				}
				else
				{
					$output->error = __( 'There are no images associated to the search terms', $this->text_domain );
				}
			}
			else
			{
				$output->error = __( $response->get_error_message(), $this->text_domain );
			}
			
			return $output;
			
		} // End get
		
		function isActive()
		{
			return get_option( 'photoshow_google_active' );
		} // End isActive
		
	} // End Class
}
?>